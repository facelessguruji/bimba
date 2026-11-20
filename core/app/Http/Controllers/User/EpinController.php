<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Controller;
use App\Lib\AfterCommit;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Pin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpinController extends Controller
{
    public function epin()
    {
        if (gs('epin') != Status::ENABLE) {
            abort(404);
        }
        $pageTitle = "Recharge Your Wallet";
        $pins      = Pin::where('generate_user_id', auth()->id())->latest()->paginate(getPaginate());
        return view('Template::user.epin_recharge', compact('pageTitle', 'pins'));
    }

    public function eRecharge(Request $request)
    {
        if (gs('epin') != Status::ENABLE) {
            abort(404);
        }

        $request->validate([
            'pin' => 'required|exists:pins,pin'
        ]);

        $user = auth()->user();

        // a member can never recharge their own generated pin
        $userPin = Pin::where('pin', $request->pin)->where('status', Status::PIN_UNUSED)->where('generate_user_id', $user->id)->first();
        if ($userPin) {
            $notify[] = ['error', 'You can not e-pin recharge to self account.'];
            return back()->withNotify($notify);
        }

        DB::transaction(function () use ($request, $user, &$pinCode) {
            // atomic pin claim: exactly one concurrent submission can flip the
            // pin to used  -  a second submitter finds zero affected rows
            $claimed = Pin::where('pin', $request->pin)
                ->where('status', Status::PIN_UNUSED)
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->update(['status' => Status::PIN_USED, 'user_id' => $user->id]);
            if (!$claimed) {
                $pinCode = 'badpin';
                return;
            }
            $pin = Pin::where('pin', $request->pin)->first();

            // atomic wallet credit
            $user->increment('balance', $pin->amount);
            $user->refresh();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $pin->amount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type     = '+';
            $transaction->remark       = 'epin';
            $transaction->details      = 'E-Pin recharge via ' . $pin->pin;
            $transaction->trx          = getTrx();
            $transaction->save();

            $deposit                  = new Deposit();
            $deposit->user_id         = $user->id;
            $deposit->method_code     = 0;
            $deposit->method_currency = gs('cur_text');
            $deposit->amount          = $pin->amount;
            $deposit->rate            = 1;
            $deposit->final_amount    = $pin->amount;
            $deposit->btc_amount      = 0;
            $deposit->gateway_ref     = "";
            $deposit->trx             = $transaction->trx;
            $deposit->payment_try     = 0;
            $deposit->status          = Status::PAYMENT_SUCCESS;
            $deposit->save();

            AfterCommit::push(function () use ($user, $pin) {
                notify($user, 'PIN_RECHARGE', [
                    'pin'          => $pin->pin,
                    'amount'       => showAmount($pin->amount, currencyFormat: false),
                    'post_balance' => showAmount($user->balance, currencyFormat: false),
                ]);

                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'Balance recharged via e-pin by ' . $user->username;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            });
        });

        // e-pin recharge notifications were deferred inside the transaction
        AfterCommit::run();

        if (isset($pinCode)) {
            $notify[] = ['error', 'This pin is invalid, expired or already used.'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Balance has been added to your account'];
        return back()->withNotify($notify);
    }

    public function epinRechargeLog()
    {
        if (gs('epin') != Status::ENABLE) {
            abort(404);
        }
        $pageTitle    = 'Recharge History';
        $transactions = Transaction::where('user_id', auth()->id())->where('remark', 'epin')->orderBy('id', 'desc')->paginate(getPaginate());
        $remarks = Transaction::where('user_id', auth()->id())->distinct('remark')->orderBy('remark')->get('remark');
        return view('Template::user.affiliate.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function pinGenerate(Request $request)
    {
        if (gs('epin') != Status::ENABLE) {
            abort(404);
        }

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'number' => 'nullable|integer|gte:1|max:1000',
        ]);

        $user = auth()->user();
        $qty  = (int) $request->input('number', 1);

        // B4 volume pricing: agents pay qty x amount minus their best volume
        // discount tier instead of the flat epin_charge percent. Non-agents
        // keep the existing face value + epin_charge behavior unchanged.
        $discountPercent = 0;
        if ($user->is_epin_agent) {
            $discountPercent = AgentController::discountPercentFor($qty);
            $total           = round($request->amount * $qty * (100 - $discountPercent) / 100, 8);
            $charge          = 0;
        } else {
            // clamp at 0: a negative epin_charge (e.g. edited directly in the DB)
            // would let members mint below-face-value pins
            $chargePerPin = max(0, ($request->amount / 100) * (float) gs('epin_charge'));
            $charge       = round($chargePerPin * $qty, 8);
            $total        = round($request->amount * $qty + $charge, 8);
        }

        DB::transaction(function () use ($request, $user, $qty, $charge, $total, $discountPercent, &$pinCode) {
            // atomic conditional deduct: a concurrent withdrawal or purchase
            // cannot push the balance negative or lose an update
            $deducted = User::where('id', $user->id)->where('balance', '>=', $total)->decrement('balance', $total);
            if (!$deducted) {
                $pinCode = 'insufficient';
                return;
            }
            $user->refresh();

            for ($i = 1; $i <= $qty; $i++) {
                $pin                   = new Pin();
                $pin->generate_user_id = $user->id;
                $pin->amount           = $request->amount;
                $pin->pin              = rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999);
                $pin->details          = "Created via " . $user->username;
                $pin->save();
            }

            $details = 'Created E-pin';
            if ($qty > 1) {
                $details .= ' x ' . $qty;
            }
            if ($discountPercent > 0) {
                $details .= ' (agent volume discount ' . rtrim(rtrim(number_format($discountPercent, 2), '0'), '.') . '%)';
            }

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            // amount is the face value moved; for agents the discounted net
            // total IS the full movement (charge stays 0), so the ledger
            // replay invariant stays exact
            $transaction->amount       = $user->is_epin_agent ? $total : round($request->amount * $qty, 8);
            $transaction->post_balance = $user->balance;
            $transaction->charge       = $charge;
            $transaction->trx_type     = '-';
            $transaction->details      = $details;
            $transaction->remark       = 'epin';
            $transaction->trx          = getTrx();
            $transaction->save();

            AfterCommit::push(function () use ($user) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'E-pin generated by ' . $user->username;
                $adminNotification->click_url = urlPath('admin.pin.index');
                $adminNotification->save();
            });
        });

        // e-pin generation notifications were deferred inside the transaction
        AfterCommit::run();

        if (isset($pinCode)) {
            $notify[] = ['error', 'You have insufficient balance'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'The pin has been created'];
        return back()->withNotify($notify);
    }
}
