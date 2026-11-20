<?php

namespace App\Http\Controllers\Gateway\Razorpay;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use Razorpay\Api\Api;


class ProcessController extends Controller
{
    /*
     * RazorPay Gateway
     */

    public static function process($deposit)
    {
        $razorAcc = gatewayCredentials($deposit->gateway->gateway_parameters);

        //  API request and response for creating an order
        $apiKey = $razorAcc->key_id;
        $apiSecret = $razorAcc->key_secret;

        try {
            $api = new Api($apiKey, $apiSecret);
            $order = $api->order->create(
                array(
                    'receipt' => $deposit->trx,
                    'amount' => round($deposit->final_amount * 100),
                    'currency' => $deposit->method_currency,
                    'payment_capture' => '0'
                )
            );
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }


        $deposit->gateway_ref = $order->id;
        $deposit->save();

        $val['key'] = $razorAcc->key_id;
        $val['amount'] = round($deposit->final_amount * 100);
        $val['currency'] = $deposit->method_currency;
        $val['order_id'] = $order['id'];
        $val['buttontext'] = "Pay with Razorpay";
        $val['name'] = auth()->user()->username;
        $val['description'] = "Payment By Razorpay";
        $val['image'] = siteLogo();
        $val['prefill.name'] = auth()->user()->firstname . ' ' . auth()->user()->lastname;
        $val['prefill.email'] = auth()->user()->email;
        $val['prefill.contact'] = auth()->user()->mobileNumber;
        $val['theme.color'] = "#2ecc71";
        $send['val'] = $val;

        $send['method'] = 'POST';


        $alias = $deposit->gateway->alias;

        $send['url'] = route('ipn.'.$alias) . '?order_id=' . encrypt($deposit->gateway_ref);
        $send['custom'] = $deposit->trx;
        $send['checkout_js'] = "https://checkout.razorpay.com/v1/checkout.js";
        $send['view'] = 'user.payment.'.$alias;

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        try {
            $orderId = decrypt($request->order_id);
        } catch (\Throwable $e) {
            abort(404);
        }
        // status filter: a completed deposit can never be re-processed by a callback replay
        $deposit = Deposit::where('gateway_ref', $orderId)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            abort(404);
        }

        // PAY-01: forged-callback guard - refuse processing while the
        // gateway still runs the shipped placeholder credentials
        PaymentController::requireConfiguredGateway($deposit->gateway->gateway_parameters);

        $razorAcc = gatewayCredentials($deposit->gateway->gateway_parameters);

        $sig = hash_hmac('sha256', $orderId . "|" . $request->razorpay_payment_id, $razorAcc->key_secret);
        $deposit->detail = $request->all();
        $deposit->save();

        if (hash_equals((string) $sig, (string) $request->razorpay_signature) && $deposit->status == Status::PAYMENT_INITIATE) {
            PaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Transaction was successful'];
            return redirect($deposit->success_url)->withNotify($notify);
        } else {
            $notify[] = ['error', "Invalid Request"];
            return back()->withNotify($notify);
        }

    }
}
