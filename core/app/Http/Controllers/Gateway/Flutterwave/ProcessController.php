<?php

namespace App\Http\Controllers\Gateway\Flutterwave;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    /*
     * flutterwave Gateway
     */

    public static function process($deposit)
    {
        $flutterAcc = gatewayCredentials($deposit->gateway->gateway_parameters);

        $send['API_publicKey'] = $flutterAcc->public_key;
        $send['encryption_key'] = $flutterAcc->encryption_key;
        $send['customer_email'] = auth()->user()->email;
        $send['amount'] = round($deposit->final_amount,2);
        $send['customer_phone'] = auth()->user()->mobileNumber;
        $send['currency'] = $deposit->method_currency;
        $send['txref'] = $deposit->trx;
        $send['notify_url'] = url('ipn/flutterwave');



        $alias = $deposit->gateway->alias;
        $send['view'] = 'user.payment.'.$alias;
        return json_encode($send);
    }

    public function ipn($track, $type)
    {

        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        // callbacks can arrive with a garbage/unknown reference: without the
        // guard the null deposit crashed the endpoint (500) instead of
        // rejecting the callback cleanly
        if (!$deposit) {
            abort(404);
        }

        // PAY-01: forged-callback guard - refuse processing while the
        // gateway still runs the shipped placeholder credentials
        PaymentController::requireConfiguredGateway($deposit->gateway->gateway_parameters);

        if ($type == 'error') {
            $message = 'Transaction failed, Ref: ' . $track;
            $notify[] = ['error', $message];
            return redirect($deposit->failed_url)->withNotify($notify);
        }

        $flutterAcc = gatewayCredentials($deposit->gateway->gateway_parameters);

        $query = array(
            "SECKEY" =>  $flutterAcc->secret_key,
            "txref" => $track
        );

        $dataString = json_encode($query);
        $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = safeJsonDecode($response);

        if (!$response) {
            $notify[]   = ['error', 'Invalid response from payment gateway'];
            $notifyApi[] = 'invalid verification response';

            if ($deposit->is_web) {
                return response()->json([
                    'code'=>200,
                    'status'=>'ok',
                    'message'=>['error'=>$notifyApi]
                ]);
            }

            return redirect($deposit->failed_url)->withNotify($notify);
        }

        $deposit->detail = $response->data ?? null;
        $deposit->save();

        if (($response->status ?? '') == 'error') {
            $message = $response->message;
            $notify[] = ['error', $message];
            $notifyApi[] = $message;

            if ($deposit->is_web) {
                return response()->json([
                    'code'=>200,
                    'status'=>'ok',
                    'message'=>['error'=>$notifyApi]
                ]);
            }

            return redirect($deposit->failed_url)->withNotify($notify);
        }

        if ($response->data->status == "successful" && $response->data->chargecode == "00" && $deposit->final_amount == $response->data->amount && $deposit->method_currency == $response->data->currency && $deposit->status == Status::PAYMENT_INITIATE) {
            PaymentController::userDataUpdate($deposit);

            $message = 'Transaction was successful, Ref: ' . $track;
            $notify[] = ['success', $message];
            $notifyApi[] = $message;

            return redirect($deposit->success_url)->withNotify($notify);
        }

        $message = 'Unable to process';
        $notify[] = ['error', $message];
        $notifyApi[] = $message;

        if ($deposit->is_web) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$notifyApi]
            ]);
        }


        return back()->withNotify($notify);

    }
}
