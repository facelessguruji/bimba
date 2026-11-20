<?php

namespace App\Http\Controllers\Gateway\Coinpayments;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\Coinpayments\CoinPaymentHosted;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /*
     * CoinPaymentHosted Gateway
     */

    public static function process($deposit)
    {

        $coinPayAcc = gatewayCredentials($deposit->gateway->gateway_parameters);

        if ($deposit->btc_amount == 0 || $deposit->btc_wallet == "") {
            try {
                $cps = new CoinPaymentHosted();
            } catch (\Exception $e) {
                $send['error'] = true;
                $send['message'] = $e->getMessage();
                return json_encode($send);
            }

            $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
            $callbackUrl = route('ipn.'.$deposit->gateway->alias);

            $req = array(
                'amount' => $deposit->final_amount,
                'currency1' => 'USD',
                'currency2' => $deposit->method_currency,
                'custom' => $deposit->trx,
                'buyer_email' => auth()->user()->email,
                'ipn_url' => $callbackUrl,
            );

            $result = $cps->CreateTransaction($req);
            if ($result['error'] == 'ok') {
                $bcoin = sprintf('%.08f', $result['result']['amount']);
                $sendadd = $result['result']['address'];
                $deposit['btc_amount'] = $bcoin;
                $deposit['btc_wallet'] = $sendadd;
                $deposit->update();
            } else {
                $send['error'] = true;
                $send['message'] = $result['error'];
            }
        }

        $send['amount'] = $deposit->btc_amount;
        $send['sendto'] = $deposit->btc_wallet;
        $send['img'] = cryptoQR($deposit->btc_wallet);
        $send['currency'] = "$deposit->method_currency";
        $send['view'] = 'user.payment.crypto';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        // garbage or unknown reference: nothing to process, answer cleanly
        // instead of dereferencing a null deposit (unauthenticated endpoint)
        if (!$deposit) {
            return response('invalid request', 400);
        }

        if ($status >= 100 || $status == 2) {
            // PAY-01: forged-callback guard - refuse processing while the
            // gateway still runs the shipped placeholder credentials
            PaymentController::requireConfiguredGateway($deposit->gateway->gateway_parameters);

            $coinPayAcc = gatewayCredentials($deposit->gateway->gateway_parameters);

            if ($coinPayAcc->merchant_id != $request->merchant || $deposit->status != Status::PAYMENT_INITIATE) {
                return response('invalid request', 400);
            }

            // SEC-02: never trust the callback payload. Everything that decides
            // the credit (status, currency, received amount) is re-read from
            // CoinPayments with the configured private key via get_tx_info.
            try {
                $cps = new CoinPaymentHosted();
            } catch (\Exception $e) {
                return response('invalid request', 400);
            }
            $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
            // api_call already json-decodes the API response into an array
            $tx = $cps->ViewTransaction(['txid' => $request->txn_id]);
            if (!is_array($tx) || ($tx['error'] ?? '') != 'ok') {
                return response('invalid request', 400);
            }
            $result  = $tx['result'] ?? [];
            $txState = (int) ($result['status'] ?? -99);
            if (!($txState >= 100 || $txState == 2)) {
                return response('invalid request', 400);
            }
            if (($result['currency2'] ?? '') != $deposit->method_currency) {
                return response('invalid request', 400);
            }
            if (floatval($result['receivedf'] ?? 0) < (float) $deposit->btc_amount) {
                return response('invalid request', 400);
            }
            if ($deposit->status == Status::PAYMENT_INITIATE) {
                PaymentController::userDataUpdate($deposit);
            }
        }
    }
}
