<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\GeneralSetting;
use App\Models\HeldCommissionLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserRanking;
use Illuminate\Support\Facades\DB;

class Affiliate
{
    /**
     * Member who purchased the plan
     *
     * @var object
     */
    public $user;

    /**
     * Listing plan which was purchased
     *
     * @var object
     */
    public $plan;

    /**
     * General setting
     *
     * @var object
     */
    public $setting;

    /**
     * Transaction number of the whole process
     *
     * @var string
     */
    public $trx;

    /**
     * Amount credited to the network for this purchase. The plan's real
     * price, reduced by the renewal percent on repeat purchases.
     *
     * @var float
     */
    public $creditAmount = 0;

    /**
     * Whether this purchase pays the network at all. Plans carry
     * is_commission_eligible; renewals and non-eligible products are
     * 100% company revenue (owner directive D2/D16 spec).
     *
     * @var bool
     */
    public $commissionEligible = true;

    /**
     * True when the buyer already held an active plan before this purchase
     * (used for the direct-active counter and upgrade semantics).
     *
     * @var bool
     */
    public $isUpgrade = false;

    /**
     * True when this purchase renews the same plan tier.
     *
     * @var bool
     */
    public $isRenewal = false;

    /**
     * E-pin seller bonus (B4): user id of the member who generated the
     * pin used to pay this purchase. Null for wallet/gateway purchases.
     * The issuer only gets paid when they are an e-pin agent.
     *
     * @var int|null
     */
    public $pinIssuerId = null;

    /**
     * Uplines touched by the current purchase (for the rank re-check).
     *
     * @var array
     */

    public function __construct($user = null, $plan = null, $trx = null)
    {
        $this->user    = $user;
        $this->plan    = $plan;
        $this->trx     = $trx;
        $this->setting = gs();
    }

    /**
     * Configure the purchase context before firing the engine.
     *
     * @param object $user
     * @param object $plan
     * @param string $trx
     * @param bool $isUpgrade buyer already had an active plan before this purchase
     * @param bool $isRenewal the same plan tier is purchased again
     * @param int|null $pinIssuerId id of the member who generated the e-pin
     *                              used to pay this purchase (null otherwise)
     * @return self
     */
    public static function forPurchase($user, $plan, $trx, $isUpgrade = false, $isRenewal = false, $pinIssuerId = null)
    {
        $engine              = new self($user, $plan, $trx);
        $engine->isUpgrade   = $isUpgrade;
        $engine->isRenewal   = $isRenewal;
        $engine->pinIssuerId = $pinIssuerId ? (int) $pinIssuerId : null;
        // renewals are 100% company revenue / 0% affiliate commission per spec:
        // creditAmount zero disables the whole commission pipeline for them
        $engine->commissionEligible = !$isRenewal && (int) ($plan->is_commission_eligible ?? 1) === 1;
        // non-eligible products and renewals are 100% company revenue:
        // creditAmount zero disables the whole commission pipeline for them
        $engine->creditAmount = $engine->commissionEligible ? round($plan->price, 8) : 0;
        return $engine;
    }

    /**
     * Run the whole affiliate pipeline for a completed plan purchase.
     * The user_plans row is already created by PlanSubscription  -  this
     * method owns the money + network side only.
     *
     * @return void
     */
    public function purchasePlan()
    {
        // engine marker: if any payout row was already written for this
        // payment, the engine ran once - a re-fire must not re-count
        // direct counters even though the money probes would hold
        if (DB::table('affiliate_logs')->where('payment_trx', $this->trx)->where('type', '!=', 'rank')->exists()) {
            return;
        }

        DB::transaction(function () {
            // non-eligible product: no network side at all (no held
            // release, no counters, no payouts) - 100% company revenue
            if (!$this->commissionEligible) {
                return;
            }

            // A new purchase (initial or upgrade) releases held earnings
            $this->releaseHeldCommissions($this->user);

            $this->tenLevelCommissions();
            // B4: the e-pin seller bonus is paid out of the credited amount
            // BEFORE the company residual, so the reconciliation identity
            // level payouts + agent bonus + company == creditAmount holds
            $this->payPinSellerBonus();
            $this->updateDirectCounts();
            $this->companyAllocation();

            // ranks are evaluated last  -  after counters are final
        });

        // engine-internal notifications were deferred inside the
        // transaction  -  send them now that the row locks are released
        AfterCommit::run();
    }

    /**
     * Ten-level referral commissions per the master spec:
     * pool   = eligible purchase amount x affiliate_pool_percent (40%)
     * share  = pool x level weight / 55  (weights 10,9,...,1 by default)
     * L1 is the buyer's direct sponsor; L2-L10 walk up the referral chain.
     * Free members earn — membership status only gates withdrawals.
     * A missing upline member leaves that level unallocated (no redistribution).
     * Renewals generate zero commission (creditAmount is zero on renewals).
     */
    public function tenLevelCommissions()
    {
        if ($this->creditAmount <= 0) {
            return;
        }

        $pool    = round($this->creditAmount * (float) gs('affiliate_pool_percent') / 100, 8);
        $weights = array_map('floatval', explode(',', (string) gs('affiliate_level_weights')));
        $weights = array_slice($weights, 0, 10);
        // the combined level payouts can never exceed the pool: each level gets
        // pool x weight / sum(weights), whatever the admin configured
        $weightTotal = max(1, array_sum($weights));

        // walk the buyer's referral chain: L1 = direct sponsor, L2+ = their sponsors
        $uplineId = $this->user->ref_by;
        $level    = 1;

        while ($level <= 10 && $uplineId) {
            $upline = User::where('id', $uplineId)->where('status', Status::USER_ACTIVE)->first();
            if (!$upline) {
                break; // chain ends - remaining levels stay unallocated
            }

            $weight = $weights[$level - 1] ?? null;
            if ($weight === null || $weight <= 0) {
                $uplineId = $upline->ref_by;
                $level++;
                continue;
            }

            $amount = round($pool * $weight / $weightTotal, 8);
            if ($amount > 0) {
                // idempotency probe: one payout per (trx, level)
                $paid = DB::table('affiliate_logs')
                    ->where('payment_trx', $this->trx)
                    ->where('type', 'level')
                    ->where('level', $level)
                    ->exists();
                if (!$paid) {
                    User::where('id', $upline->id)->increment('balance', $amount);
                    User::where('id', $upline->id)->increment('total_level_com', $amount);
                    $upline->refresh();

                    $transaction               = new Transaction();
                    $transaction->user_id      = $upline->id;
                    $transaction->amount       = $amount;
                    $transaction->post_balance = $upline->balance;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '+';
                    $transaction->details      = ladderLevelName($level) . ' referral commission from ' . $this->user->username;
                    $transaction->remark       = 'level_commission';
                    $transaction->trx          = $this->trx;
                    $transaction->save();

                    $this->logCommission($upline->id, $level, $amount, round($amount / max($this->creditAmount, 0.00000001) * 100, 2), 'level');
                }
            }

            $uplineId = $upline->ref_by;
            $level++;
        }
    }

    /**
     * E-pin seller bonus (B4): when the purchase was paid with an e-pin
     * generated by an active agent, that agent earns a percent of the
     * credited purchase amount.
     *
     * Runs inside the purchase transaction AFTER tenLevelCommissions() and
     * BEFORE companyAllocation(), so companyAllocation's residual math
     * (creditAmount minus everything already logged for this trx) treats
     * the bonus as a payout: the invariant payouts + company == creditAmount
     * is preserved with no change to the allocation logic.
     *
     * Money rules honored: atomic conditional balance increment, a real
     * '+' transactions ledger row (the bonus IS a wallet movement), and an
     * affiliate_logs audit row of type 'agent_bonus'. The log insert is the
     * idempotency probe, so a re-fired engine can never pay twice.
     *
     * @return void
     */
    public function payPinSellerBonus()
    {
        if ($this->creditAmount <= 0 || !$this->pinIssuerId) {
            return;
        }

        // the buyer can never pay themselves a bonus for their own pin:
        // the purchase controller blocks self-pins, this guard is defense
        // in depth against future callers
        if ((int) $this->pinIssuerId == (int) $this->user->id) {
            return;
        }

        $percent = (float) gs('epin_seller_bonus_percent');
        if ($percent <= 0) {
            return;
        }

        // exactly one agent bonus per purchase trx (idempotency probe)
        $paid = DB::table('affiliate_logs')
            ->where('payment_trx', $this->trx)
            ->where('type', 'agent_bonus')
            ->exists();
        if ($paid) {
            return;
        }

        // banned members and revoked agents earn nothing
        $issuer = User::where('id', $this->pinIssuerId)
            ->where('is_epin_agent', 1)
            ->where('status', Status::USER_ACTIVE)
            ->first();
        if (!$issuer) {
            return;
        }

        $bonus = round($this->creditAmount * $percent / 100, 8);
        if ($bonus <= 0) {
            return;
        }

        // atomic wallet credit: the bonus is a real movement of company
        // liability, so it carries a '+' ledger row (counter-free: no
        // total_ref_com / total_level_com inflation by design)
        User::where('id', $issuer->id)->increment('balance', $bonus);
        $issuer->refresh();

        $transaction               = new Transaction();
        $transaction->user_id      = $issuer->id;
        $transaction->amount       = $bonus;
        $transaction->post_balance = $issuer->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->details      = 'E-pin seller bonus from ' . $this->user->username;
        $transaction->remark       = 'agent_pin_bonus';
        $transaction->trx          = $this->trx;
        $transaction->save();

        // audit row under the SAME purchase trx, which is what lets the
        // company residual absorb the bonus automatically
        $this->logCommission($issuer->id, 0, $bonus, $percent, 'agent_bonus');
    }

    /**
     * Record the company allocation (60% default) plus any unallocated pool
     * from missing upline members, as a type 'company' affiliate_logs row so
     * the ledger reconciles: level payouts + company allocation = credited amount.
     */
    public function companyAllocation()
    {
        if ($this->creditAmount <= 0) {
            return;
        }

        $paid          = (float) DB::table('affiliate_logs')
            ->where('payment_trx', $this->trx)
            ->where('type', '!=', 'release')
            ->sum('amount');
        $companyShare  = round($this->creditAmount - $paid, 8);
        if ($companyShare <= 0) {
            return;
        }

        $this->logCommission(0, 0, $companyShare, (float) gs('affiliate_company_percent'), 'company');
    }

    /**
     * Release held commissions the user now qualifies for. Called on plan
     * purchase (upgrade path) and from the admin manual release. Exactly
     * once per row under lock.
     *
     * @param object $user
     * @param int|null $depthOverride force a qualified depth (admin release)
     * @return float total released
     */
    public function releaseHeldCommissions($user, $depthOverride = null)
    {
        $depth = $depthOverride ?? planDepth($user);
        if ($depth <= 0 && !hasActivePlan($user)) {
            return 0;
        }

        $total    = 0;
        $released = 0;

        HeldCommissionLog::where('user_id', $user->id)
            ->held()
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->each(function ($log) use ($user, $depth, &$total, &$released) {
                $eligible = $log->reason == 'no_plan'
                    ? hasActivePlan($user)
                    : ($log->reason == 'depth' && $log->level <= $depth);
                if (!$eligible) {
                    return;
                }

                // flip the row inside the lock  -  guarantees exactly-once
                $affected = HeldCommissionLog::where('id', $log->id)
                    ->where('status', Status::HELD)
                    ->update([
                        'status'      => Status::RELEASED,
                        'released_at' => now(),
                    ]);
                if (!$affected) {
                    return;
                }

                User::where('id', $user->id)->increment('balance', $log->amount);
                if ($log->level <= 1) {
                    User::where('id', $user->id)->increment('total_ref_com', $log->amount);
                } else {
                    User::where('id', $user->id)->increment('total_level_com', $log->amount);
                }
                $user->refresh();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $log->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge       = 0;
                $transaction->trx_type     = '+';
                $transaction->details      = 'Held commission released (' . ladderLevelName($log->level) . ')';
                $transaction->remark       = 'commission_released';
                $transaction->trx          = $log->purchase_trx;
                $transaction->save();

                // audit-trail line under the ORIGINAL purchase trx so every
                // payout is visible in affiliate_logs (type 'release' rows
                // are excluded from the company-profit reconciliation)
                $this->logCommission($user->id, $log->level, $log->amount, 0, 'release', $log->purchase_trx, $log->from_user_id);

                $total    += $log->amount;
                $released++;
            });

        if ($released > 0) {
            // atomic decrement  -  the counter can never drift below zero
            // even if a legacy row and a concurrent hold raced
            UserExtra::where('user_id', $user->id)
                ->where('held_commission', '>=', $total)
                ->decrement('held_commission', $total);

            AfterCommit::push(fn () => notify($user, 'COMMISSION_RELEASED', [
                'amount'       => showAmount($total, currencyFormat: false),
                'count'        => $released,
                'post_balance' => showAmount($user->balance, currencyFormat: false),
            ]));
        }

        return $total;
    }

    /**
     * Direct referral counters for the referrer: total always on
     * registration, active when the referral activates a first plan.
     * Upgrades and renewals never re-count  -  a referral is counted
     * exactly once, on its first-ever plan activation.
     *
     * @return void
     */
    public function updateDirectCounts()
    {
        // banned referrers accrue nothing: no counter, no rank evaluation
        $referrer = User::where('id', $this->user->ref_by)
            ->where('status', Status::USER_ACTIVE)
            ->first();
        if (!$referrer) {
            return;
        }
        if ($this->isUpgrade || $this->isRenewal) {
            return;
        }
        $extra = $this->userExtra($referrer);
        $extra->increment('direct_active');

        // single-line model: the referrer's rank rides on active directs,
        // so re-check it the moment one more direct goes active
        $this->updateRankState($referrer);
    }

    /**
     * Count a new registration in the referrer's total directs.
     *
     * @param object $newUser
     * @return void
     */
    public static function countNewReferral($newUser)
    {
        if (!$newUser->ref_by) {
            return;
        }
        $referrer = User::where('id', $newUser->ref_by)->first();
        if (!$referrer) {
            return;
        }
        $extra = (new self())->userExtra($referrer);
        $extra->increment('direct_total');
    }

    /**
     * Write an affiliate_logs row for audit trail.
     *
     * This row is also the engine's idempotency probe and the refund
     * claw-back source, so it is NOT allowed to fail silently: it runs
     * inside the purchase transaction and a failure rolls the whole
     * payout back  -  payout and audit row commit together or not at all.
     */
    public function logCommission($beneficiaryId, $level, $amount, $percent, $type = 'referral', $trx = null, $payerId = null)
    {
        DB::table('affiliate_logs')->insert([
            'payment_trx'    => $trx ?? $this->trx,
            'payer_id'       => $payerId ?? $this->user->id,
            'beneficiary_id' => $beneficiaryId,
            'level'          => $level,
            'amount'         => $amount,
            'percent'        => $percent,
            'type'           => $type,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    /**
     * Re-evaluate the rank for a member against the active-directs
     * threshold (single-line model). Promotes at most one rank per call
     * and pays the rank bonus exactly once.
     *
     * @param object $user
     * @return void
     */
    public function updateRankState($user)
    {
        // the ranking module toggle gates the whole engine  -  bonuses and
        // promotions stop the moment an admin disables it
        if (!gs('user_ranking')) {
            return;
        }

        // a banned member earns no rank and no bonus
        if ($user->status != Status::USER_ACTIVE) {
            return;
        }

        $extra = UserExtra::where('user_id', $user->id)->first();
        if (!$extra) {
            return;
        }

        // serialize rank read + write per member (prevents double bonus under
        // concurrent purchases crediting the same upline)
        $user = User::where('id', $user->id)->lockForUpdate()->first();
        if (!$user) {
            return;
        }

        // single-line referral model: ranks are earned on active directs alone
        $earned = UserRanking::active()
            ->where('direct_active', '<=', $extra->direct_active)
            ->orderBy('level', 'desc')
            ->first();

        if (!$earned) {
            return;
        }

        $currentLevel = 0;
        if ($user->user_ranking_id) {
            $current = UserRanking::find($user->user_ranking_id);
            $currentLevel = $current ? (int) $current->level : 0;
        }

        if ((int) $earned->level <= $currentLevel) {
            return;
        }

        $user->user_ranking_id  = $earned->id;
        $user->last_rank_update = now();
        $user->save();

        // D22 (owner): ranks are STATUS ONLY. No rank bonus exists: no
        // balance movement, no transaction, no audit row. Promotion itself
        // is the reward, and the member is simply notified.

        // this method also runs from the cron outside any transaction  -
        // push() sends immediately there and defers inside money transactions
        AfterCommit::push(fn () => notify($user, 'RANK_ACHIEVED', [
            'rank'   => $earned->name,
        ]));
    }

    /**
     * Unilevel genealogy by levels  -  children via ref_by.
     *
     * @param object $user
     * @param int $maxLevels
     * @return array [level => Collection of members]
     */
    public function showTreePage($user, $maxLevels = 5)
    {
        $levels = [];
        $parents = collect([$user->id]);

        for ($level = 1; $level <= $maxLevels; $level++) {
            $children = User::whereIn('ref_by', $parents)
                ->with('userExtra', 'activePlan.plan', 'userRanking')
                ->withCount('referrals')
                ->orderBy('id')
                ->get();
            if ($children->isEmpty()) {
                break;
            }
            $levels[$level] = $children;
            $parents        = $children->pluck('id');
        }

        return $levels;
    }

    /**
     * Preview who Global Auto-Referral would assign next  -  shown on the
     * registration page for transparency. Mirrors assignAutoReferral()
     * selection without persisting anything.
     *
     * @return object|null
     */
    public static function previewAutoSponsor()
    {
        $requirePlan = (bool) gs('auto_refer_require_active_plan');

        $query = User::where('status', Status::USER_ACTIVE);
        if ($requirePlan) {
            $query->whereHas('activePlan');
        }
        if (gs('auto_refer_require_email')) {
            $query->where('ev', Status::VERIFIED);
        }

        $method = gs('auto_refer_method') ?: 'round_robin';

                if ($method == 'fixed') {
            // the fixed account must satisfy the SAME eligibility filters as
            // the rotation pool (active, and plan/email when toggled on)
            $fixed = User::where('id', (int) gs('auto_refer_fixed_user_id'))
                ->where('status', Status::USER_ACTIVE);
            if (gs('auto_refer_require_active_plan')) {
                $fixed->whereHas('activePlan', fn ($q) => $q->where('plan_price', '>', 0));
            }
            if (gs('auto_refer_require_email')) {
                $fixed->where('ev', Status::VERIFIED);
            }
            return $fixed->value('id') ?: null;
        }

        if ($method == 'random') {
            return $query->inRandomOrder()->first();
        }

        // weakest team: the eligible member whose downline is smallest
        if ($method == 'weakest_team') {
            return (clone $query)
                ->withCount(['referrals as team_size'])
                ->orderBy('team_size')
                ->orderBy('id')
                ->first();
        }

        // top recruiter: the eligible member with the most personal directs
        if ($method == 'top_recruiter') {
            return (clone $query)
                ->withCount(['referrals as direct_count'])
                ->orderByDesc('direct_count')
                ->orderBy('id')
                ->first();
        }

        $last = (int) gs('auto_refer_last_user_id');
        $next = (clone $query)->where('id', '>', $last)->orderBy('id')->first()
            ?? (clone $query)->orderBy('id')->first();

        // mirror the assignment fallback chain: fixed account → first member
        return $next
            ?? User::where('id', (int) gs('auto_refer_fixed_user_id'))->first()
            ?? User::orderBy('id')->first();
    }

    /**
     * Global Auto-Referral: assign a referrer to a member who registered
     * without a referral link. Selection is round-robin over eligible
     * members (active plan + active account) with an atomic pointer, with
     * a fallback chain so nobody joins orphaned.
     *
     * @param object $newUser
     * @return void
     */
    public function assignAutoReferral($newUser)
    {
        if (!gs('auto_refer_enable')) {
            return;
        }

        $referrerId = DB::transaction(function () use ($newUser) {
            $requirePlan = (bool) gs('auto_refer_require_active_plan');

            $query = User::where('status', Status::USER_ACTIVE)
                ->where('id', '!=', $newUser->id)
                ->where('id', '>', 0);

            if ($requirePlan) {
                $query->whereHas('activePlan');
            }
            if (gs('auto_refer_require_email')) {
                $query->where('ev', Status::VERIFIED);
            }

            $method = gs('auto_refer_method') ?: 'round_robin';

            if ($method == 'fixed') {
                // same eligibility filters as the rotation pool
                $fixed = User::where('id', (int) gs('auto_refer_fixed_user_id'))
                    ->where('status', Status::USER_ACTIVE);
                if (gs('auto_refer_require_active_plan')) {
                    $fixed->whereHas('activePlan', fn ($q) => $q->where('plan_price', '>', 0));
                }
                if (gs('auto_refer_require_email')) {
                    $fixed->where('ev', Status::VERIFIED);
                }
                return (int) $fixed->value('id') ?: null;
            }

            if ($method == 'random') {
                $pool = $query->pluck('id')->all();
                if ($pool) {
                    return $pool[array_rand($pool)];
                }
                return null;
            }

            // weakest team: the eligible member whose downline is smallest
            if ($method == 'weakest_team') {
                $pick = (clone $query)
                    ->withCount(['referrals as team_size'])
                    ->orderBy('team_size')
                    ->orderBy('id')
                    ->first();
                return $pick?->id;
            }

            // top recruiter: the eligible member with the most personal directs
            if ($method == 'top_recruiter') {
                $pick = (clone $query)
                    ->withCount(['referrals as direct_count'])
                    ->orderByDesc('direct_count')
                    ->orderBy('id')
                    ->first();
                return $pick?->id;
            }

            // round_robin: serialize rotation  -  lock the settings row, read the
            // pointer from it, advance and save through the model so the
            // settings cache is refreshed atomically with the pointer.
            $general = \App\Models\GeneralSetting::lockForUpdate()->first();
            $last    = $general ? (int) $general->auto_refer_last_user_id : 0;

            $next = (clone $query)->where('id', '>', $last)->orderBy('id')->first();
            if (!$next) {
                $next = (clone $query)->orderBy('id')->first();
            }
            if (!$next) {
                return null;
            }

            if ($general) {
                $general->auto_refer_last_user_id = $next->id;
                $general->save();
            }
            return $next->id;
        });

        // fallback chain: fixed account → first active member → first member
        if (!$referrerId) {
            // fallback fixed account passes the same eligibility filters
            $fixed = User::where('id', (int) gs('auto_refer_fixed_user_id'))
                ->where('status', Status::USER_ACTIVE);
            if (gs('auto_refer_require_active_plan')) {
                $fixed->whereHas('activePlan', fn ($q) => $q->where('plan_price', '>', 0));
            }
            $referrerId = (int) $fixed->value('id') ?: null;
        }
        if (!$referrerId) {
            $referrerId = User::where('status', Status::USER_ACTIVE)
                ->where('id', '!=', $newUser->id)->orderBy('id')->value('id');
        }
        if (!$referrerId) {
            $referrerId = User::where('id', '!=', $newUser->id)->orderBy('id')->value('id');
        }
        if (!$referrerId || $referrerId == $newUser->id) {
            return;
        }

        $newUser->ref_by     = $referrerId;
        $newUser->ref_source = Status::REF_SOURCE_AUTO;
        $newUser->save();

        self::countNewReferral($newUser);

        $referrer = User::find($referrerId);
        if ($referrer) {
            notify($referrer, 'AUTO_REFERRAL_RECEIVED', [
                'username' => $newUser->username ?: $newUser->firstname,
                'fullname' => $newUser->fullname,
            ]);
        }
    }

    /**
     * Fetch (or lazily create) the member's counter row.
     *
     * @param object $user
     * @return object
     */
    public function userExtra($user)
    {
        $extra = UserExtra::where('user_id', $user->id)->lockForUpdate()->first();
        if (!$extra) {
            try {
                $extra          = new UserExtra();
                $extra->user_id = $user->id;
                $extra->save();
            } catch (\Throwable $e) {
                // a concurrent first-touch inserted the row between the read
                // and the write  -  the unique index rejected ours, take theirs
                $extra = UserExtra::where('user_id', $user->id)->firstOrFail();
            }
        }
        return $extra;
    }

    /**
     * Assign the member's global monoline queue position. The settings row
     * acts as the global mutex and the position is minted AND persisted on
     * the member row inside the same lock, so two concurrent registrations
     * can never mint the same position and corrupt the payout ladder. The
     * UNIQUE index on users.monoline_position backstops the guarantee.
     *
     * @param object $user
     * @return int
     */
    public static function assignMonolinePosition($user)
    {
        return (int) DB::transaction(function () use ($user) {
            GeneralSetting::lockForUpdate()->first();
            $user->monoline_position = (User::max('monoline_position') ?? 0) + 1;
            $user->save();
            return $user->monoline_position;
        });
    }
}
