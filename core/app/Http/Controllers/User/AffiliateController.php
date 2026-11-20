<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\HeldCommissionLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserRanking;
use App\Lib\Affiliate;
use App\Lib\AfterCommit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    /**
     * Affiliate hub: referral link + share, earnings stats and the
     * member's affiliate list in one place.
     */
    public function hub(Request $request)
    {
        $user      = auth()->user();
        $pageTitle = 'Affiliate Program';

        $referrals = User::where('ref_by', $user->id)->with('activePlan.plan', 'userRanking')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($qq) use ($s) {
                    $qq->where('username', 'like', "%$s%")
                        ->orWhere('email', 'like', "%$s%")
                        ->orWhere('firstname', 'like', "%$s%")
                        ->orWhere('lastname', 'like', "%$s%");
                });
            })
            ->orderBy('id', 'desc')->paginate(getPaginate());

        $extra = $user->userExtra;
        $earned = Transaction::where('user_id', $user->id)
            ->whereIn('remark', ['referral_commission', 'level_commission', 'monoline_commission', 'matching_bonus', 'commission_released', 'rank_bonus'])
            ->sum('amount');

        // Referral links carry the member's contact number (dial code +
        // mobile, the same digits they are reachable on), never the
        // username. Old username links keep working through the silent
        // legacy fallback on the registration page.
        $refLink = route('user.register') . '?ref=' . $user->dial_code . $user->mobile;

        return view('Template::user.affiliate.hub', compact(
            'pageTitle', 'referrals', 'extra', 'earned', 'refLink'
        ));
    }

    public function myReferral()
    {
        $pageTitle = "My Referrals";
        $referrals = User::where('ref_by', auth()->id())->with('activePlan.plan', 'userRanking')->orderBy('id', 'desc')->paginate(getPaginate());
        $sections  = getPageSections('basic');
        $refSource = request()->ref_source;
        if ($refSource !== null && $refSource !== '') {
            $referrals = User::where('ref_by', auth()->id())->where('ref_source', $refSource)->with('activePlan.plan', 'userRanking')->orderBy('id', 'desc')->paginate(getPaginate());
        }
        return view('Template::user.affiliate.referrals', compact('pageTitle', 'referrals'));
    }

    public function teamLevels($username = null)
    {
        $user = auth()->user();
        if ($username) {
            $target = User::where('username', $username)->first();
            abort_unless($target, 404);
            $this->checkMyTeam($target, $user);
            $user = $target;
        }
        $pageTitle  = 'My Team';
        $engine     = new Affiliate();
        $levels     = $engine->showTreePage($user);
        return view('Template::user.affiliate.levels', compact('pageTitle', 'user', 'levels'));
    }

    public function heldEarnings()
    {
        $pageTitle = 'Held Earnings';
        $held      = HeldCommissionLog::where('user_id', auth()->id())->with('fromUser')->orderBy('id', 'desc');
        $heldLogs  = (clone $held)->paginate(getPaginate());
        $heldTotal = (clone $held)->where('status', Status::HELD)->sum('amount');
        $released  = (clone $held)->released()->sum('amount');
        return view('Template::user.affiliate.held_earnings', compact('pageTitle', 'heldLogs', 'heldTotal', 'released'));
    }

    public function ranking()
    {
        if (!gs('user_ranking')) {
            abort(404);
        }
        $pageTitle    = 'Affiliate Ranks';
        $userRankings = UserRanking::ordered()->get();
        $user         = auth()->user()->load('userRanking', 'userExtra');
        return view('Template::user.affiliate.ranking', compact('pageTitle', 'userRankings', 'user'));
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks   = Transaction::where('user_id', auth()->id())->distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx', 'details'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.affiliate.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function balanceTransfer()
    {
        if (gs('balance_transfer') != Status::ENABLE) {
            abort(404);
        }
        $pageTitle = 'Transfer Balance';
        return view('Template::user.affiliate.balance_transfer', compact('pageTitle'));
    }

    public function transferConfirm(Request $request)
    {
        if (gs('balance_transfer') != Status::ENABLE) {
            abort(404);
        }

        $request->validate([
            'username' => 'required|exists:users,username',
            'amount'   => 'required|numeric|gt:0'
        ]);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        if ($user->username == $request->username) {
            $notify[] = ['error', 'You cannot transfer balance to your own account'];
            return back()->withNotify($notify);
        }

        $amount         = (float) $request->amount;

        // charges and limits come from admin settings (restored donor behaviour)
        // clamped to >= 0: a negative charge (e.g. edited directly in the DB)
        // would otherwise credit money out of thin air on every transfer
        $fixedCharge   = max(0, (float) gs('balance_transfer_fixed_charge'));
        $percentCharge = max(0, (float) gs('balance_transfer_per_charge'));
        $minTransfer   = max(0, (float) gs('balance_transfer_min'));
        $maxTransfer   = max(0, (float) gs('balance_transfer_max'));

        if ($minTransfer && $amount < $minTransfer) {
            $notify[] = ['error', 'Minimum transfer amount is ' . showAmount($minTransfer)];
            return back()->withNotify($notify);
        }
        if ($maxTransfer && $amount > $maxTransfer) {
            $notify[] = ['error', 'Maximum transfer amount is ' . showAmount($maxTransfer)];
            return back()->withNotify($notify);
        }

        $receiver       = User::where('username', $request->username)->first();
        $transferCharge = $fixedCharge + ($amount * $percentCharge / 100);

        try {
        DB::transaction(function () use ($user, $receiver, $amount, $transferCharge) {
            // atomic conditional deduct INSIDE the crediting transaction:
            // concurrent requests can never overdraw, and a crash mid-flow
            // rolls everything back together
            $deducted = User::where('id', $user->id)->where('balance', '>=', $amount + $transferCharge)->decrement('balance', $amount + $transferCharge);
            if (!$deducted) {
                throw new \Exception('insufficient_balance');
            }
            $user->refresh();

            $receiverTrx = getTrx();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge       = $transferCharge;
            $transaction->trx_type     = '-';
            $transaction->details      = 'Balance transferred to ' . $receiver->username;
            $transaction->remark       = 'balance_transfer';
            $transaction->trx          = $receiverTrx;
            $transaction->save();

            if ($transferCharge > 0) {
                $chargeTransaction               = new Transaction();
                $chargeTransaction->user_id      = $user->id;
                $chargeTransaction->amount       = $transferCharge;
                $chargeTransaction->post_balance = $user->balance;
                $chargeTransaction->trx_type     = '-';
                $chargeTransaction->details      = 'Balance transfer charge';
                $chargeTransaction->remark       = 'transfer_charge';
                $chargeTransaction->trx          = $receiverTrx;
                $chargeTransaction->save();
            }

            User::where('id', $receiver->id)->increment('balance', $amount);
            $receiver->refresh();

            $receiverTransaction               = new Transaction();
            $receiverTransaction->user_id      = $receiver->id;
            $receiverTransaction->amount       = $amount;
            $receiverTransaction->post_balance = $receiver->balance;
            $receiverTransaction->charge       = 0;
            $receiverTransaction->trx_type     = '+';
            $receiverTransaction->details      = 'Balance received from ' . $user->username;
            $receiverTransaction->remark       = 'balance_transfer';
            $receiverTransaction->trx          = $receiverTrx;
            $receiverTransaction->save();

            AfterCommit::push(fn () => notify($user, 'BAL_SUB', [
                'amount'       => showAmount($amount, currencyFormat: false),
                'post_balance' => showAmount($user->balance, currencyFormat: false),
            ]));

            AfterCommit::push(fn () => notify($receiver, 'BAL_ADD', [
                'amount'       => showAmount($amount, currencyFormat: false),
                'post_balance' => showAmount($receiver->balance, currencyFormat: false),
            ]));
        });

        AfterCommit::run();

        $notify[] = ['success', 'Balance transferred successfully'];
        return back()->withNotify($notify);
        } catch (\Throwable $e) {
            if ($e->getMessage() === 'insufficient_balance') {
                $notify[] = ['error', 'You have insufficient balance'];
                return back()->withNotify($notify);
            }
            throw $e;
        }
    }

    /**
     * Members may only view team pages of their own team.
     */
    protected function checkMyTeam($target, $viewer)
    {
        if ($target->id == $viewer->id) {
            return;
        }
        if ($target->ref_by == $viewer->id) {
            return;
        }
        $referrer = $target;
        for ($i = 0; $i < 50; $i++) {
            if (!$referrer->ref_by) {
                abort(401);
            }
            $referrer = User::find($referrer->ref_by);
            if (!$referrer) {
                abort(401);
            }
            if ($referrer->id == $viewer->id) {
                return;
            }
        }
        abort(401);
    }
}
