<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Lib\AfterCommit;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function pending($userId = null)
    {
        $pageTitle = 'Pending Payments';
        $deposits = $this->depositData('pending',userId:$userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }


    public function approved($userId = null)
    {
        $pageTitle = 'Approved Payments';
        $deposits = $this->depositData('approved',userId:$userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function successful($userId = null)
    {
        $pageTitle = 'Successful Payments';
        $deposits = $this->depositData('successful',userId:$userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function rejected($userId = null)
    {
        $pageTitle = 'Rejected Payments';
        $deposits = $this->depositData('rejected',userId:$userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function initiated($userId = null)
    {
        $pageTitle = 'Initiated Payments';
        $deposits = $this->depositData('initiated',userId:$userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function deposit($userId = null)
    {
        $pageTitle = 'Payments History';
        $depositData = $this->depositData($scope = null, $summary = true,userId:$userId);
        $deposits = $depositData['data'];
        $summary = $depositData['summary'];
        $successful = $summary['successful'];
        $pending = $summary['pending'];
        $rejected = $summary['rejected'];
        $initiated = $summary['initiated'];
        return view('admin.deposit.log', compact('pageTitle', 'deposits','successful','pending','rejected','initiated'));
    }

    protected function depositData($scope = null,$summary = false,$userId = null)
    {
        if ($scope) {
            $deposits = Deposit::$scope()->with(['user', 'gateway']);
        }else{
            $deposits = Deposit::with(['user', 'gateway']);
        }

        if ($userId) {
            $deposits = $deposits->where('user_id',$userId);
        }

        $deposits = $deposits->searchable(['trx','user:username'])->dateFilter();

        $request = request();

        if ($request->method) {
            if ($request->method != Status::GOOGLE_PAY) {
                $method = Gateway::where('alias',$request->method)->firstOrFail();
                $deposits = $deposits->where('method_code',$method->code);
            }else{
                $deposits = $deposits->where('method_code',Status::GOOGLE_PAY);
            }
        }

        if (!$summary) {
            return $deposits->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $successful = clone $deposits;
            $pending = clone $deposits;
            $rejected = clone $deposits;
            $initiated = clone $deposits;

            $successfulSummary = $successful->where('status',Status::PAYMENT_SUCCESS)->sum('amount');
            $pendingSummary = $pending->where('status',Status::PAYMENT_PENDING)->sum('amount');
            $rejectedSummary = $rejected->where('status',Status::PAYMENT_REJECT)->sum('amount');
            $initiatedSummary = $initiated->where('status',Status::PAYMENT_INITIATE)->sum('amount');

            return [
                'data'=>$deposits->orderBy('id','desc')->paginate(getPaginate()),
                'summary'=>[
                    'successful'=>$successfulSummary,
                    'pending'=>$pendingSummary,
                    'rejected'=>$rejectedSummary,
                    'initiated'=>$initiatedSummary,
                ]
            ];
        }
    }

    public function details($id)
    {
        $deposit = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();
        $pageTitle = $deposit->user->username.' requested ' . showAmount($deposit->amount);
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit','details'));
    }


    public function approve($id)
    {
        $deposit = Deposit::where('id',$id)->where('status',Status::PAYMENT_PENDING)->firstOrFail();

        PaymentController::userDataUpdate($deposit,true);

        $notify[] = ['success', 'Deposit request approved successfully'];

        return to_route('admin.deposit.pending')->withNotify($notify);
    }

    /**
     * Refund a SUCCESSFUL deposit (chargeback or manual refund): reverses
     * whatever the payment granted and claws back the commissions recorded
     * under that payment's trx, atomically.
     */
    public function refund($id)
    {
        $deposit = Deposit::where('id', $id)->where('status', Status::PAYMENT_SUCCESS)->firstOrFail();

        DB::transaction(function () use ($deposit) {
            $claimed = Deposit::where('id', $deposit->id)
                ->where('status', Status::PAYMENT_SUCCESS)
                ->update(['status' => Status::PAYMENT_REFUND]);
            if (!$claimed) {
                return; // already refunded by a parallel click
            }

            $user = User::find($deposit->user_id);

            // 1. reverse the grant: deactivate the purchased plan or debit the added funds
            if ($deposit->plan_id) {
                \App\Models\UserPlan::where('user_id', $user->id)
                    ->where('plan_id', $deposit->plan_id)
                    ->where('status', Status::ENABLE)
                    ->update(['status' => Status::DISABLE, 'updated_at' => now()]);
            } else {
                // PAY-04: write the '-' ledger row only when the balance debit
                // actually happened - the ledger invariant (balance == signed
                // transaction sum) must hold even when the member already
                // spent the funds; the deposit stays marked refunded
                $debited = User::where('id', $user->id)
                    ->where('balance', '>=', $deposit->amount)
                    ->decrement('balance', $deposit->amount);
                if ($debited) {
                    $user->refresh();
                    $transaction               = new Transaction();
                    $transaction->user_id      = $user->id;
                    $transaction->amount       = $deposit->amount;
                    $transaction->post_balance = $user->balance;
                    $transaction->trx_type     = '-';
                    $transaction->details      = 'Refund of deposit ' . $deposit->trx;
                    $transaction->remark       = 'deposit_refund';
                    $transaction->trx          = $deposit->trx;
                    $transaction->save();
                }
            }

            // 2. claw back the commissions paid on this purchase trx
            $logs = \App\Models\AffiliateLog::where('payment_trx', $deposit->trx)->get();
            foreach ($logs as $log) {
                if ($log->type == 'company' || $log->amount <= 0 || !$log->beneficiary_id) {
                    continue; // company share is revenue allocation, not a member balance
                }
                $credited = User::where('id', $log->beneficiary_id)
                    ->where('balance', '>=', $log->amount)
                    ->decrement('balance', $log->amount);
                if ($credited) {
                    $beneficiary = User::find($log->beneficiary_id);
                    $transaction               = new Transaction();
                    $transaction->user_id      = $beneficiary->id;
                    $transaction->amount       = $log->amount;
                    $transaction->post_balance = $beneficiary->balance;
                    $transaction->trx_type     = '-';
                    $transaction->details      = 'Commission reversal for refunded payment ' . $deposit->trx;
                    $transaction->remark       = 'commission_reversal';
                    $transaction->trx          = getTrx();
                    $transaction->save();
                }
            }

            AfterCommit::push(fn () => notify($user, 'DEPOSIT_REFUND', [
                'trx'    => $deposit->trx,
                'amount' => showAmount($deposit->amount, currencyFormat: false),
            ]));
        });

        AfterCommit::run();

        $notify[] = ['success', 'Deposit refunded and its effects reversed'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|string|max:255'
        ]);
        $deposit = Deposit::where('id',$request->id)->where('status',Status::PAYMENT_PENDING)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status = Status::PAYMENT_REJECT;
        $deposit->save();

        notify($deposit->user, 'DEPOSIT_REJECT', [
            'method_name' => $deposit->methodName(),
            'method_currency' => $deposit->method_currency,
            'method_amount' => showAmount($deposit->final_amount,currencyFormat:false),
            'amount' => showAmount($deposit->amount,currencyFormat:false),
            'charge' => showAmount($deposit->charge,currencyFormat:false),
            'rate' => showAmount($deposit->rate,currencyFormat:false),
            'trx' => $deposit->trx,
            'rejection_message' => $request->message
        ]);

        $notify[] = ['success', 'Deposit request rejected successfully'];
        return  to_route('admin.deposit.pending')->withNotify($notify);

    }
}
