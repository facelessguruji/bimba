<?php

namespace App\Http\Controllers\Gateway\CoinpaymentsFiat;

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
        $coinpayAcc = gatewayCredentials($deposit->gateway->gateway_parameters);

        $val['merchant'] = $coinpayAcc->merchant_id;
        $val['item_name'] = 'Payment to ' . gs('site_name');
        $val['currency'] = $deposit->method_currency;
        $val['currency_code'] = "$deposit->method_currency";
        $val['amountf'] = round($deposit->final_amount,2);
        $val['ipn_url'] =  route('ipn.'.$deposit->gateway->alias);
        $val['custom'] = "$deposit->trx";
        $val['amount'] = round($deposit->final_amount,2);
        $val['return'] = $deposit->success_url;
        $val['cancel_return'] = $deposit->failed_url;
        $val['notify_url'] = route('ipn.'.$deposit->gateway->alias);
        $val['success_url'] = $deposit->success_url;
        $val['cancel_url'] = $deposit->failed_url;
        $val['custom'] = $deposit->trx;
        $val['cmd'] = '_pay_simple';
        $val['want_shipping'] = 0;
        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = 'https://www.coinpayments.net/index.php';

        return json_encode($send);
    }

    public function ipn(Request $request)
    {

        $track = $request->custom;
        $status = $request->status;
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        // garbage or unknown reference: nothing to process (unauthenticated endpoint)
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

            // SEC-02: never trust the callback payload. The transaction is
            // re-read from CoinPayments with the configured private key via
            // get_tx_info; the credit only happens if CoinPayments itself
            // reports the transaction complete for THIS deposit.
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
            if (($result['currency1'] ?? '') != $deposit->method_currency) {
                return response('invalid request', 400);
            }
            if (($result['custom'] ?? '') != $deposit->trx) {
                return response('invalid request', 400);
            }
            if (floatval($result['amount1'] ?? 0) + 0.011 < round($deposit->final_amount, 2)) {
                return response('invalid request', 400);
            }
            if ($deposit->status == Status::PAYMENT_INITIATE) {
                PaymentController::userDataUpdate($deposit);
            }
        }
    }
}
