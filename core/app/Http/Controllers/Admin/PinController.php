<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Pin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinController extends Controller
{
    public function allPins()
    {
        $pageTitle = "All Pins";
        $pins      = Pin::searchable(['pin'])->with('user', 'generator')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'    => 'required|numeric|gt:0',
            // a runaway count would lock the request for minutes inserting pins
            'number'    => 'required|integer|gt:0|max:1000',
            'expires_at' => 'nullable|date|after:now',
            // B4: optional member account the pins are generated FOR; when
            // set, that member pays from their own balance
            'generate_user_id' => 'nullable|integer|gt:0',
        ]);

        $target = null;
        if ($request->generate_user_id) {
            $target = User::find($request->generate_user_id);
            if (!$target) {
                $notify[] = ['error', 'Member not found for the provided user id'];
                return back()->withNotify($notify);
            }
        }

        $expiresAt = $request->expires_at ? now()->parse($request->expires_at) : null;

        // no member selected: admin-minted pins, no money moves (unchanged)
        if (!$target) {
            for ($i = 1; $i <= $request->number; $i++) {
                $pin          = new Pin();
                $pin->amount  = $request->amount;
                $pin->pin     = rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999);
                $pin->details = "Created via admin";
                $pin->expires_at = $expiresAt;
                $pin->save();
            }

            $notify[] = ['success', 'E-Pin added successfully'];
            return back()->withNotify($notify);
        }

        // generating FOR a member: they pay the same rule as self
        // generation. Agents get the volume discount, non-agents the
        // flat epin_charge percent. Same atomic conditional decrement +
        // ledger row as the member path.
        $qty = (int) $request->number;

        $discountPercent = 0;
        if ($target->is_epin_agent) {
            $discountPercent = AgentController::discountPercentFor($qty);
            $total           = round($request->amount * $qty * (100 - $discountPercent) / 100, 8);
            $charge          = 0;
        } else {
            $chargePerPin = max(0, ($request->amount / 100) * (float) gs('epin_charge'));
            $charge       = round($chargePerPin * $qty, 8);
            $total        = round($request->amount * $qty + $charge, 8);
        }

        DB::transaction(function () use ($target, $qty, $expiresAt, $charge, $total, $discountPercent, $request, &$pinCode) {
            // atomic conditional deduct from the member paying for the pins
            $deducted = User::where('id', $target->id)->where('balance', '>=', $total)->decrement('balance', $total);
            if (!$deducted) {
                $pinCode = 'insufficient';
                return;
            }
            $target->refresh();

            for ($i = 1; $i <= $qty; $i++) {
                $pin             = new Pin();
                $pin->amount     = $request->amount;
                $pin->pin        = rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999);
                $pin->details    = "Created via admin for " . $target->username;
                $pin->expires_at = $expiresAt;
                $pin->generate_user_id = $target->id;
                $pin->save();
            }

            $details = 'Created E-pin via admin x ' . $qty;
            if ($discountPercent > 0) {
                $details .= ' (agent volume discount ' . rtrim(rtrim(number_format($discountPercent, 2), '0'), '.') . '%)';
            }

            $transaction               = new Transaction();
            $transaction->user_id      = $target->id;
            // agents: the discounted net total IS the whole movement
            // (charge 0); non-agents keep the face value + charge memo
            // convention of the member generation path
            $transaction->amount       = $target->is_epin_agent ? $total : round($request->amount * $qty, 8);
            $transaction->post_balance = $target->balance;
            $transaction->charge       = $charge;
            $transaction->trx_type     = '-';
            $transaction->details      = $details;
            $transaction->remark       = 'epin';
            $transaction->trx          = getTrx();
            $transaction->save();
        });

        if (isset($pinCode)) {
            $notify[] = ['error', $target->username . ' has insufficient balance (' . showAmount($total) . ' required)'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'E-Pins generated for ' . $target->username . ' successfully'];
        return back()->withNotify($notify);
    }

    public function userPins()
    {
        $pageTitle = "All Member Pins";
        $pins      = Pin::whereNotNull('generate_user_id')->searchable(['pin'])->with('generator')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function adminPins()
    {
        $pageTitle = "All Admin Pins";
        $pins      = Pin::whereNull('generate_user_id')->searchable(['pin'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function usedPins()
    {
        $pageTitle = "All Used Pins";
        $pins      = Pin::used()->searchable(['pin'])->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function unusedPins()
    {
        $pageTitle = "All Unused Pins";
        $pins      = Pin::unused()->searchable(['pin'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }
}
