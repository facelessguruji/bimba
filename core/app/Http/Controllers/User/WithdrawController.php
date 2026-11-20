<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Lib\AfterCommit;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    /**
     * Withdrawal privilege rule: only a currently-active PAID membership
     * unlocks withdrawal submission - no grace period, no extra conditions.
     *
     * @return mixed redirect when blocked, null when allowed
     */
    protected function withdrawalGate()
    {
        $user = auth()->user();

        // strictly a currently-active PAID membership: no grace period, no
        // extra conditions - the paid plan is the only key to withdrawals
        $allowed = $user->plans()
            ->where('plan_price', '>', 0)
            ->where('status', Status::ENABLE)
            ->whereDate('expired_on', '>', now())
            ->exists();

        if (!$allowed) {
            $notify[] = ['error', 'An active paid membership plan is required to request a withdrawal. Please purchase or renew your membership plan to continue.'];
            return to_route('pricing')->withNotify($notify);
        }

        // V2.0 rank tier wall: the earned rank sets the minimum ACTIVE plan
        // tier that may withdraw (Senior Affiliate $10, Team Leader $20, ...
        // Crown Ambassador $100). Income above the held tier stays visible in
        // the balance and unlocks the moment the member upgrades.
        $tier = (float) (optional($user->userRanking)->min_plan_amount ?? 0);
        if ($tier > 0) {
            $tierOk = $user->plans()
                ->where('plan_price', '>=', $tier)
                ->where('status', Status::ENABLE)
                ->whereDate('expired_on', '>', now())
                ->exists();
            if (!$tierOk) {
                $notify[] = ['error', 'Your rank ' . $user->userRanking->name . ' requires an active plan of ' . showAmount($tier) . ' or higher to request a withdrawal. Please upgrade your membership plan.'];
                return to_route('pricing')->withNotify($notify);
            }
        }

        // KYC hard gate: when withdraw_kyc is enabled in settings, only a
        // verified member can request a withdrawal. Consulted by the money
        // page, the preview and the submit so the state cannot change in
        // between without being caught.
        if (gs('withdraw_kyc') == 1 && $user->kv != Status::KYC_VERIFIED) {
            $notify[] = ['error', 'Your KYC verification is required before you can request a withdrawal. Please complete KYC from your dashboard.'];
            return to_route('user.kyc.form')->withNotify($notify);
        }

        return null;
    }

    public function withdrawMoney()
    {
        // the withdrawal section stays accessible to every member — free and
        // expired members see the reason; only the submission is blocked
        $gateMessage = $this->withdrawalGate();
        $gateNotify  = [];
        if ($gateMessage) {
            // the gate flashed its reason (plan or KYC) into the session;
            // show it inline and clear the flash so the global toast does
            // not repeat it again on the next page
            $gateNotify = session('notify') ?? [];
            session()->forget('notify');
        }
        $withdrawMethod = WithdrawMethod::active()->orderBy('name')->get();
        $pageTitle      = 'Withdraw Money';
        return view('Template::user.withdraw.methods', compact('pageTitle', 'withdrawMethod', 'gateMessage', 'gateNotify'));
    }

    public function withdrawStore(Request $request)
    {
        if ($gate = $this->withdrawalGate()) {
            return $gate;
        }

        $request->validate([
            'method_code' => 'required',
            'amount'      => 'required|numeric'
        ]);

        $user = auth()->user();

        // anti-fraud cap: a member can hold only one pending withdrawal at a
        // time - stacking requests would multiply the payout surface
        if (Withdrawal::where('user_id', $user->id)->where('status', Status::WITHDRAW_PENDING)->exists()) {
            $notify[] = ['error', 'You already have a pending withdrawal request. Please cancel it or wait until it is processed before submitting another one.'];
            return back()->withNotify($notify);
        }

        // anti-fraud cap: at most 3 withdrawal requests within 24 hours
        if (Withdrawal::where('user_id', $user->id)->where('created_at', '>=', now()->subDay())->count() > 3) {
            $notify[] = ['error', 'You have reached the limit of 3 withdrawal requests within 24 hours. Please try again later.'];
            return back()->withNotify($notify);
        }

        $method = WithdrawMethod::where('id', $request->method_code)->active()->firstOrFail();
        $user   = auth()->user();

        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount'];
            return back()->withNotify($notify)->withInput($request->all());
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'Insufficient balance for withdrawal'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        // clamp charge at 0: a negative fixed/percent charge edited directly
        // into the DB would inflate the payout above the requested amount
        $charge      = max(0, $method->fixed_charge + ($request->amount * $method->percent_charge / 100));
        $afterCharge = $request->amount - $charge;

        if ($afterCharge <= 0) {
            $notify[] = ['error', 'Withdraw amount must be sufficient for charges'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $finalAmount = max(0, $afterCharge * $method->rate);

        $withdraw               = new Withdrawal();
        $withdraw->method_id    = $method->id;
        $withdraw->user_id      = $user->id;
        $withdraw->amount       = $request->amount;
        $withdraw->currency     = $method->currency;
        $withdraw->rate         = $method->rate;
        $withdraw->charge       = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx          = getTrx();
        $withdraw->save();

        session()->put('wtrx', $withdraw->trx);
        return to_route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        // the gate is re-checked here too: a plan can lapse or KYC can be
        // revoked between choosing a method and confirming the request
        if ($gate = $this->withdrawalGate()) {
            return $gate;
        }

        $withdraw  = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'desc')->firstOrFail();
        $pageTitle = 'Withdraw Preview';
        return view('Template::user.withdraw.preview', compact('pageTitle', 'withdraw'));
    }

    public function withdrawSubmit(Request $request)
    {
        // the plan/KYC gate is re-checked at submit  -  a plan can expire or
        // grace can lapse between choosing a method and confirming
        if ($gate = $this->withdrawalGate()) {
            return $gate;
        }

        $request->validate([
            'authenticator_code' => 'sometimes|required',
        ]);

        $withdraw = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'desc')->firstOrFail();

        $method = $withdraw->method;
        if ($method->status == Status::DISABLE) {
            abort(404);
        }

        $formData     = @$method->form->form_data ?? [];
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify)->withInput($request->all());
            }
        }

        if ($withdraw->amount > $user->balance) {
            $notify[] = ['error', 'Your requested amount is larger than your current balance.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        // anti-fraud cap re-checked at the claim point: an initiated request
        // from before a pending one existed must not slip past the cap here
        if (Withdrawal::where('user_id', $user->id)->where('status', Status::WITHDRAW_PENDING)->exists()) {
            $notify[] = ['error', 'You already have a pending withdrawal request. Please cancel it or wait until it is processed before submitting another one.'];
            return to_route('user.withdraw.history')->withNotify($notify);
        }

        // atomic request claim: exactly one concurrent submit can flip the
        // initiated request to pending  -  a double submit finds zero rows
        $claimed = Withdrawal::where('id', $withdraw->id)
            ->where('status', Status::PAYMENT_INITIATE)
            ->update(['status' => Status::WITHDRAW_PENDING, 'withdraw_information' => json_encode($userData)]);
        if (!$claimed) {
            $notify[] = ['error', 'This withdrawal request has already been submitted.'];
            return to_route('user.withdraw.history')->withNotify($notify);
        }

        // atomic conditional deduct: the balance can never go negative
        $deducted = User::where('id', $user->id)->where('balance', '>=', $withdraw->amount)->decrement('balance', $withdraw->amount);
        if (!$deducted) {
            // refund the claim so the member can retry after funding
            Withdrawal::where('id', $withdraw->id)->update(['status' => Status::PAYMENT_INITIATE]);
            $notify[] = ['error', 'Your requested amount is larger than your current balance.'];
            return back()->withNotify($notify)->withInput($request->all());
        }
        $user->refresh();

        $transaction               = new Transaction();
        $transaction->user_id      = $withdraw->user_id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $withdraw->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Withdraw request via ' . $withdraw->method->name;
        $transaction->trx          = $withdraw->trx;
        $transaction->remark       = 'withdraw';
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.data.details', $withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name'     => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount'   => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount'          => showAmount($withdraw->amount, currencyFormat: false),
            'charge'          => showAmount($withdraw->charge, currencyFormat: false),
            'rate'            => showAmount($withdraw->rate, currencyFormat: false),
            'trx'             => $withdraw->trx,
            'post_balance'    => showAmount($user->balance, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return to_route('user.withdraw.history')->withNotify($notify);
    }

    /**
     * Member cancels their own PENDING withdrawal: atomic claim, full refund,
     * ledger row. Mirrors the admin reject/refund path minus the feedback.
     */
    public function cancel($trx)
    {
        $user = auth()->user();

        $withdraw = Withdrawal::where('trx', $trx)
            ->where('user_id', $user->id)
            ->where('status', Status::WITHDRAW_PENDING)
            ->first();

        if (!$withdraw) {
            $notify[] = ['error', 'No pending withdrawal found for this reference.'];
            return back()->withNotify($notify);
        }

        DB::transaction(function () use ($user, $withdraw) {
            // atomic claim: a concurrent admin approval or double-click can
            // never split the refund
            $claimed = Withdrawal::where('id', $withdraw->id)
                ->where('status', Status::WITHDRAW_PENDING)
                ->update(['status' => Status::WITHDRAW_CANCEL]);
            if (!$claimed) {
                throw new \RuntimeException('withdraw_already_processed');
            }

            // full refund — credits can never race into a negative
            User::where('id', $user->id)->increment('balance', $withdraw->amount);
            $user->refresh();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $withdraw->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge       = 0;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Withdrawal request cancelled';
            $transaction->remark       = 'withdraw_cancel';
            $transaction->trx          = $withdraw->trx;
            $transaction->save();
        });

        AfterCommit::run();

        $notify[] = ['success', 'Withdrawal request cancelled and the amount returned to your balance.'];
        return to_route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog(Request $request)
    {
        $pageTitle = "Withdrawal Log";
        $withdraws = Withdrawal::where('user_id', auth()->id())->where('status', '!=', Status::PAYMENT_INITIATE);
        if ($request->search) {
            $withdraws = $withdraws->where('trx', $request->search);
        }
        $withdraws = $withdraws->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.withdraw.log', compact('pageTitle', 'withdraws'));
    }
}
