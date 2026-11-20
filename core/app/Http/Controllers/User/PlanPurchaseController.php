<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Affiliate;
use App\Lib\AfterCommit;
use App\Lib\PlanSubscription;
use App\Models\Pin;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanPurchaseController extends Controller
{
    /**
     * Purchase a listing plan with wallet balance or an e-pin.
     * Gateway purchases keep using the base deposit flow.
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'plan_id'    => 'required|integer',
            'price_type' => 'nullable|in:yearly',
            'method'     => 'required|in:wallet,epin',
            'pin'        => 'required_if:method,epin',
        ]);

        $user      = auth()->user();
        $plan      = Plan::active()->findOrFail($request->plan_id);
        $priceType = 'yearly_price'; // plans are yearly-only

        // duplicate-submit guard: the same plan bought twice within 10 seconds
        // is a double click, not a second purchase (a different plan right
        // after stays allowed)
        $recent = Transaction::where('user_id', $user->id)
            ->where('remark', 'purchase_plan')
            ->where('details', 'like', '%' . $plan->title . '%')
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();
        if ($recent) {
            $notify[] = ['error', 'Your previous purchase of this plan is being processed. Please wait a moment and check your plan page.'];
            return back()->withNotify($notify);
        }
        $price     = (float) $plan->$priceType;

        // FREE plan: nothing is sold, so the wallet "purchase" is just an
        // activation (no deduction, no transaction, no affiliate engine).
        // Without this a member whose signup did not auto-assign a plan can
        // never obtain the Free plan and is locked out of listings entirely.
        // E-Pin for a free plan stays meaningless, so that is still rejected.
        if ($price <= 0) {
            if ($request->method != 'wallet') {
                $notify[] = ['error', 'This plan is free, no payment is required'];
                return back()->withNotify($notify);
            }

            // retry-safe: never create a second active subscription
            if ($user->activePlan()->exists()) {
                $notify[] = ['error', 'You already have an active plan'];
                return back()->withNotify($notify);
            }

            $plan->price     = 0;
            $plan->expired_on = now()->addYear();

            $planSubscription = new PlanSubscription($plan, $user);
            $planSubscription->subscribe();

            AfterCommit::push(fn () => notify($user, 'BUY_PLAN', [
                'name'  => $plan->title,
                'price' => showAmount(0, currencyFormat: false),
            ]));
            AfterCommit::run();

            $notify[] = ['success', $plan->title . ' plan activated successfully'];
            return to_route('user.home')->withNotify($notify);
        }

        // RENEWAL: buying the same level the member already holds active.
        // Renewals are 100% company revenue — wallet balance is not accepted;
        // payment gateway and E-Pin are both fine.
        $isRenewalPurchase = $user->plans()
            ->where('plan_id', $plan->id)
            ->where('status', Status::ENABLE)
            ->where('expired_on', '>', now())
            ->exists();

        if ($isRenewalPurchase && $request->method == 'wallet') {
            $notify[] = ['error', 'Renewals must be paid through a payment gateway or E-Pin. Wallet balance cannot be used for membership renewals.'];
            return back()->withNotify($notify);
        }

        $trx = getTrx();

        DB::transaction(function () use ($request, $user, $plan, $price, $priceType, $trx, &$pinCode) {
            if ($request->method == 'wallet') {
                // atomic check-and-deduct  -  never double-spends
                $claimed = User::where('id', $user->id)
                    ->where('balance', '>=', $price)
                    ->decrement('balance', $price);
                if (!$claimed) {
                    $pinCode = 'insufficient';
                    return;
                }
                $user->refresh();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $price;
                $transaction->post_balance = $user->balance;
                $transaction->charge       = 0;
                $transaction->trx_type     = '-';
                $transaction->details      = 'Purchased ' . $plan->title . ' plan (wallet balance)';
                $transaction->remark       = 'purchase_plan';
                $transaction->trx          = $trx;
                $transaction->save();
            } else {
                // atomic pin claim  -  one pin can never pay for two plans
                $claimed = Pin::where('pin', $request->pin)
                    ->where('status', Status::PIN_UNUSED)
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                    ->update(['status' => Status::PIN_USED, 'user_id' => $user->id]);
                if (!$claimed) {
                    $pinCode = 'badpin';
                    return;
                }
                $pin = Pin::where('pin', $request->pin)->first();
                if ($pin->generate_user_id == $user->id) {
                    Pin::where('pin', $request->pin)->update(['status' => Status::PIN_UNUSED, 'user_id' => null]);
                    $pinCode = 'selfpin';
                    return;
                }
                // the pin must cover the plan price  -  a small pin can never
                // unlock an expensive plan; revert the claim so the pin
                // is not burned for nothing
                if ($pin->amount < $price) {
                    Pin::where('pin', $request->pin)->update(['status' => Status::PIN_UNUSED, 'user_id' => null]);
                    $pinCode = 'shortpin';
                    return;
                }

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $price;
                $transaction->post_balance = $user->refresh()->balance;
                $transaction->charge       = 0;
                // e-pins are an external payment medium  -  no wallet balance
                // moves here, so this row is informational (trx_type NULL)
                // exactly like gateway purchases; writing '-' would break the
                // ledger replay invariant (balance == signed transaction sum)
                $transaction->trx_type     = null;
                $transaction->details      = 'Purchased ' . $plan->title . ' plan (e-pin ' . $pin->pin . ')';
                $transaction->remark       = 'purchase_plan';
                $transaction->trx          = $trx;
                $transaction->save();
            }

            // B4: pass the used pin's generator so the engine can pay the
            // e-pin seller bonus; wallet purchases pass null (the pin row
            // was claimed above, so generate_user_id is stable here)
            $pinIssuerId = $request->method == 'epin' ? Pin::where('pin', $request->pin)->value('generate_user_id') : null;
            $this->activatePlan($user, $plan, $priceType, $trx, $pinIssuerId);
        });

        // purchase notifications were deferred inside the transaction
        AfterCommit::run();

        if (isset($pinCode)) {
            $notify[] = ['error', $pinCode == 'insufficient'
                ? 'You\'ve no sufficient balance. Please deposit first.'
                : ($pinCode == 'selfpin' ? 'You cannot use a pin generated by your own account'
                : ($pinCode == 'shortpin' ? 'This e-pin is worth less than the plan price' : 'Invalid or already used e-pin'))];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', $plan->title . ' plan purchased successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    /**
     * Create the user_plans row via the base subscription service and fire
     * the affiliate engine exactly once for this purchase.
     *
     * @param int|null $pinIssuerId id of the member who generated the e-pin
     *                              used to pay (null for wallet purchases)
     */
    public static function activatePlan($user, $plan, $priceType, $trx, $pinIssuerId = null)
    {
        $hadActivePlan = $user->plans()->where('plan_price', '>', 0)->exists();
        // renewal = the tier currently held ACTIVE; a lapsed re-buy is a new sale
        $isRenewal     = $user->plans()->where('plan_price', '>', 0)->where('plan_id', $plan->id)->where('status', Status::ENABLE)->where('expired_on', '>', now())->exists();

        // V2.0 pin reward baseline: the highest plan price ever held BEFORE
        // this purchase, so rewards fire once per price level (renewals and
        // re-buys of a held/lapsed tier never re-grant)
        $prevHighest = (float) $user->plans()->where('plan_price', '>', 0)->max('plan_price');

        $plan->price     = $plan->yearly_price;
        $plan->expired_on = now()->addYear();

        $planSubscription = new PlanSubscription($plan, $user);
        $planSubscription->subscribe();

        $engine = Affiliate::forPurchase($user, $plan, $trx, $hadActivePlan, $isRenewal, $pinIssuerId);
        $engine->purchasePlan();

        // V2.0 upgrade reward: equal-worth e-pins (1 x $10 pin per $10 of the
        // new tier, entry $10 tier excluded). Fired only when the member
        // genuinely climbs to a tier they never held before; pins are $10
        // recharge vouchers with NO expiry, sellable to referrals.
        if ($plan->price >= 20 && $plan->price > $prevHighest) {
            $rewardPins = intdiv((int) round($plan->price), 10);
            $rewardRows = [];
            for ($i = 0; $i < $rewardPins; $i++) {
                $rewardRows[] = [
                    'generate_user_id' => $user->id,
                    'user_id'          => null,
                    'amount'           => 10,
                    'pin'              => 'UPG' . strtoupper(substr(md5($trx . $i . $user->id), 0, 10)),
                    'status'           => Status::PIN_UNUSED,
                    'expires_at'       => null,
                    'details'          => 'Upgrade reward - ' . showAmount($plan->price, currencyFormat: false) . ' tier',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
            Pin::insert($rewardRows);
            AfterCommit::push(function () use ($user, $rewardPins) {
                session()->flash('pin_reward', "Upgrade reward: {$rewardPins} x \$10 e-pin"
                    . ($rewardPins > 1 ? 's' : '')
                    . ' added to your e-pin page. Sell them to your referrals!');
            });
        }

        AfterCommit::push(fn () => notify($user, 'BUY_PLAN', [
            'name'  => $plan->title,
            'price' => showAmount($plan->price, currencyFormat: false),
        ]));

        return $engine;
    }
}
