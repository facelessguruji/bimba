<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Affiliate;
use App\Models\HeldCommissionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeldCommissionController extends Controller
{
    public function index()
    {
        $pageTitle = 'Held Earnings';
        $query     = HeldCommissionLog::with('user', 'fromUser')->orderBy('id', 'desc');

        if (request()->status !== null && request()->status !== '') {
            $query->where('status', request()->status == 'held' ? Status::HELD : Status::RELEASED);
        }
        if (request()->reason) {
            $query->where('reason', request()->reason);
        }
        if (request()->search) {
            $query->whereHas('user', function ($q) {
                $q->where('username', 'like', '%' . request()->search . '%');
            });
        }

        $heldLogs  = $query->paginate(getPaginate());
        $heldTotal = HeldCommissionLog::held()->sum('amount');
        return view('admin.held.index', compact('pageTitle', 'heldLogs', 'heldTotal'));
    }

    public function release(Request $request, $id)
    {
        $log = HeldCommissionLog::where('id', $id)->held()->firstOrFail();
        $user = $log->user;

        DB::transaction(function () use ($user, $log) {
            $engine = new Affiliate();
            $engine->releaseHeldCommissions($user, max(planDepth($user), $log->level));
        });

        $notify[] = ['success', 'Held commission released successfully'];
        return back()->withNotify($notify);
    }
}
