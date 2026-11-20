<?php

namespace App\Http\Controllers\Gateway;

use Illuminate\Support\Facades\Log;
use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Affiliate;
use App\Lib\AfterCommit;
use App\Lib\FormProcessor;
use App\Lib\PlanSubscription;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function deposit()
    {
        $pageTitle = 'Add Funds';
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();
        return view('Template::user.payment.deposit', compact('pageTitle', 'gatewayCurrency'));
    }

    public function depositInsert(Request $request)
    {
        // two flows share this endpoint:
        //  - plan purchase  (from the buy-plan form): plan_id + type present,
        //    the amount is derived from the plan price server-side
        //  - add funds       (from the wallet page): only amount + gateway,
        //    the user types the amount within the gateway's limits
        $request->validate([
            'plan_id'  => 'nullable|integer',
            'type'     => 'nullable|in:yearly',
            'gateway'  => 'required|integer',
            'currency' => 'required',
            'amount'   => $request->plan_id ? 'nullable' : 'required|numeric|gt:0',
        ]);

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        $plan = null;
        if ($request->plan_id) {
            $plan   = Plan::active()->findOrFail($request->plan_id);
            $type   = $request->type . "_price";
            $amount = $plan->$type;
        } else {
            $amount = round((float) $request->amount, 2);
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'The amount is outside the limit allowed by this payment method'];
            return back()->withNotify($notify);
        }

        // charge and payable are derived from the plan price only  -  the
        // client never decides what a plan costs
        $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable     = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->plan_id         = $plan?->id ?? 0; // 0 on the add-funds flow
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->gateway_ref      = "";
        $data->trx             = getTrx();
        $data->price_type      = 'yearly'; // plans are yearly-only
        $data->success_url = route('user.deposit.history');
        $data->failed_url = route('user.deposit.history');
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }





    public function depositConfirm()
    {
        // the confirm step is Track-based (Track is set by depositInsert) -
        // direct visits are just an invalid entry point, so send the member
        // back to the payment picker with an info message instead of a 404
        $track = session()->get('Track');

        $deposit = $track
            ? Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->first()
            : null;

        if (!$deposit) {
            $notify[] = ['info', 'No payment is in progress. Please choose a payment method first.'];
            return to_route('user.deposit.index')->withNotify($notify);
        }

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = safeJsonDecode($data);

        // a gateway returning a malformed envelope must never fatal the
        // checkout  -  show the member a recoverable error instead
        if (!$data) {
            $notify[] = ['error', 'The payment gateway returned an invalid response. Please try again or choose another method.'];
            return back()->withNotify($notify);
        }

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

          // for Stripe V3
        if(isset($data->session)){
            $deposit->gateway_ref = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }


    /**
     * PAY-01 forged-callback guard: refuse to process an automatic gateway's
     * IPN/callback while its credentials are still the shipped placeholders.
     * With no real API keys configured, anyone can POST a forged "success"
     * callback against a pending deposit reference and mint plan or wallet
     * credit. A credential counts as configured only when it is neither
     * empty, nor an all-dash placeholder, nor the shipped sandbox merchant
     * id - the guard auto-disarms the moment real credentials are saved
     * (full provider signature verification is the D9-gated follow-up before
     * any production key is entered).
     */
    public static function requireConfiguredGateway($gatewayParameter)
    {
        $credentials = json_decode($gatewayParameter);

        $configured = is_object($credentials) && count((array) $credentials) > 0;

        // the shipped donor sandbox merchant id is publicly known and can
        // never authenticate a callback
        $shippedPlaceholders = ['6515561'];

        foreach ($credentials ?? [] as $val) {
            $value = (is_object($val) && isset($val->value) && !is_object($val->value)) ? $val->value : $val;

            if (!is_string($value)) {
                continue; // non-string config values are not placeholder credentials
            }

            $value = trim($value);

            if ($value === '' || preg_match('/^-+$/', $value) || in_array($value, $shippedPlaceholders, true)) {
                $configured = false;
                break;
            }
        }

        if (!$configured) {
            Log::warning('IPN refused: gateway credentials are still placeholders (forged-callback guard)');
            abort(403, 'This payment gateway is not configured yet. Please contact support.');
        }
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        // atomic claim: exactly one caller (gateway IPN retry, cron, or admin
        // approval) may flip a deposit to success  -  the engine fires once.
        // The claim and all the work it gates share one transaction, so a
        // crash can never leave a "success" deposit without its plan and
        // commissions.
        return DB::transaction(function () use ($deposit, $isManual) {
        $claimed = Deposit::where('id', $deposit->id)
            ->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])
            ->update(['status' => Status::PAYMENT_SUCCESS]);
        if (!$claimed) {
            return;
        }
        {
            $deposit->status = Status::PAYMENT_SUCCESS;

            $user = User::find($deposit->user_id);

            if ($deposit->plan) {
                $plan        = $deposit->plan;
                $plan->price = $deposit->amount;

                $plan->expired_on = now()->addYear(); // yearly-only membership

                // capture pre-purchase state for the affiliate engine
                $hadActivePlan = $user->plans()->where('plan_price', '>', 0)->exists();
                // renewal = the tier currently held ACTIVE; a lapsed re-buy is a new sale
                $isRenewal     = $user->plans()->where('plan_price', '>', 0)->where('plan_id', $plan->id)->where('status', Status::ENABLE)->where('expired_on', '>', now())->exists();

                $planSubscription = new PlanSubscription($plan, $user);
                $planSubscription->subscribe();

                // audit-trail record for the purchase (the deposit row is the
                // actual money record  -  no wallet movement on gateway payments)
                $purchaseTransaction               = new Transaction();
                $purchaseTransaction->user_id      = $user->id;
                $purchaseTransaction->amount       = $plan->price;
                $purchaseTransaction->post_balance = $user->balance;
                $purchaseTransaction->charge       = 0;
                $purchaseTransaction->trx_type     = null;
                $purchaseTransaction->details      = 'Purchased ' . $plan->title . ' plan via ' . ($isManual ? 'manual gateway' : 'payment gateway');
                $purchaseTransaction->remark       = 'purchase_plan';
                $purchaseTransaction->trx          = $deposit->trx;
                $purchaseTransaction->save();

                // affiliate engine  -  fires exactly once per payment success
                $engine = Affiliate::forPurchase($user, $plan, $deposit->trx, $hadActivePlan, $isRenewal);
                $engine->purchasePlan();

                AfterCommit::push(fn () => notify($user, 'BUY_PLAN', [
                    'name'         => $plan->title,
                    'price'        => showAmount($plan->price, currencyFormat: false),
                    'expired_date' => showDateTime($plan->expired_on),
                ]));

                $planSubscription->disabledExtraListings();
                $planSubscription->removeExtraData();
            } else {
                // Add Funds deposit (plan_id = 0): credit the wallet exactly
                // once, inside the same transaction as the success claim.
                $credited = User::where('id', $user->id)->increment('balance', $deposit->amount);
                if (!$credited) {
                    throw new \RuntimeException('Wallet credit failed for deposit ' . $deposit->trx);
                }
                $user->refresh();

                $depositTransaction               = new Transaction();
                $depositTransaction->user_id      = $user->id;
                $depositTransaction->amount       = $deposit->amount;
                $depositTransaction->post_balance = $user->balance;
                $depositTransaction->charge       = $deposit->charge;
                $depositTransaction->trx_type     = '+';
                $depositTransaction->details      = 'Deposit via ' . $deposit->methodName();
                $depositTransaction->remark       = 'deposit';
                $depositTransaction->trx          = $deposit->trx;
                $depositTransaction->save();
            }

            $methodName = $deposit->methodName();

            AfterCommit::push(function () use ($isManual, $user, $methodName, $deposit) {
                if (!$isManual) {
                    $adminNotification            = new AdminNotification();
                    $adminNotification->user_id   = $user->id;
                    $adminNotification->title     = 'Payment successful via ' . $methodName;
                    $adminNotification->click_url = urlPath('admin.deposit.successful');
                    $adminNotification->save();
                }

                notify($user, $isManual ? 'PAYMENT_APPROVE' : 'PAYMENT_COMPLETE', [
                    'method_name'     => $methodName,
                    'method_currency' => $deposit->method_currency,
                    'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                    'amount'          => showAmount($deposit->amount, currencyFormat: false),
                    'charge'          => showAmount($deposit->charge, currencyFormat: false),
                    'rate'            => showAmount($deposit->rate, currencyFormat: false),
                    'trx'             => $deposit->trx,
                    'post_balance'    => showAmount($user->balance, currencyFormat: false)
                ]);
            });
        }
        });

        // deposit-success notifications were deferred inside the
        // transaction  -  send them now that the row locks are released
        AfterCommit::run();
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        // graceful: opening the confirm page without an in-progress payment is a
        // stale visit, not a broken page - send the member to the deposit desk
        if (!$data) {
            $notify[] = ['error', 'No manual payment is in progress. Please start a new deposit first.'];
            return to_route('user.deposit.index')->withNotify($notify);
        }
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Payment';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        $notify[] = ['error', 'No manual payment is in progress. Please start a new deposit first.'];
        return to_route('user.deposit.index')->withNotify($notify);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = ($gateway->form->form_data ?? []);

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Payment request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
            'amount'          => showAmount($data->amount, currencyFormat: false),
            'charge'          => showAmount($data->charge, currencyFormat: false),
            'rate'            => showAmount($data->rate, currencyFormat: false),
            'trx'             => $data->trx
        ]);

        $notify[] = ['success', 'Your deposit request has been submitted successfully'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
