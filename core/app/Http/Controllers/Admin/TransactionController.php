<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $pageTitle    = 'Transactions';
        $remarks      = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::searchable(['trx', 'user:username', 'details'])->filter(['trx_type', 'remark'])->dateFilter()
            ->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.transaction.index', compact('pageTitle', 'transactions', 'remarks'));
    }
}
