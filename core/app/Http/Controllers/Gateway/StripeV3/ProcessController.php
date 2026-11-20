<?php

namespace App\Http\Controllers\Gateway\StripeV3;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $StripeAcc = gatewayCredentials($deposit->gateway->gateway_parameters);
        $alias = $deposit->gateway->alias;
        \Stripe\Stripe::setApiKey("$StripeAcc->secret_key");
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data'=>[
                        'unit_amount' => round($deposit->final_amount,2) * 100,
                        'currency' => "$deposit->method_currency",
                        'product_data'=>[
                            'name' => gs('site_name'),
                            'description' => 'Deposit with Stripe',
                            'images' => [siteLogo()],
                        ]
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'cancel_url' => $deposit->failed_url,
                'success_url' => $deposit->success_url,
            ]);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $send['view'] = 'user.payment.'.$alias;
        $send['session'] = $session;
        $send['StripeJSAcc'] = $StripeAcc;
        $deposit->gateway_ref = $session->id ?? '';
        $deposit->save();
        return json_encode($send);
    }


    public function ipn(Request $request)
    {
        $StripeAcc = GatewayCurrency::where('gateway_alias','StripeV3')->orderBy('id','desc')->first();

        if (!$StripeAcc) {
            // webhook for a gateway that has no currency row configured
            return response('gateway not configured', 400);
        }

        // PAY-01: forged-callback guard - refuse processing while the
        // gateway still runs the shipped placeholder credentials
        PaymentController::requireConfiguredGateway($StripeAcc->gateway_parameter);

        $gateway_parameter = gatewayCredentials($StripeAcc->gateway_parameter);


        \Stripe\Stripe::setApiKey($gateway_parameter->secret_key);

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $gateway_parameter->end_point; // main
        $payload = file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';


        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            // status filter: a completed deposit can never be re-processed
            // by a Stripe event replay
            $deposit = Deposit::where('gateway_ref',  $session->id ?? '')
                ->where('status', Status::PAYMENT_INITIATE)
                ->orderBy('id', 'DESC')
                ->first();

            if($deposit){
                PaymentController::userDataUpdate($deposit);
            }
        }
        http_response_code(200);
    }
}
