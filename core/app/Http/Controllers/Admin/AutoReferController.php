<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Http\Request;

class AutoReferController extends Controller
{
    public function setting()
    {
        $pageTitle    = 'Auto-Refer Settings';
        $eligiblePool = User::where('status', Status::USER_ACTIVE)->whereHas('activePlan')->count();
        return view('admin.setting.auto_refer', compact('pageTitle', 'eligiblePool'));
    }

    public function settingUpdate(Request $request)
    {
        // The fixed fallback user is only meaningful (and only required to
        // exist) when the fixed method is selected. Other methods leave 0
        // in the field, and 0 must not trip exists:users,id or every save
        // on this page would fail validation and block the enable toggles.
        $rules = [
            'auto_refer_method'              => 'required|in:round_robin,random,fixed,weakest_team,top_recruiter',
            // e-pin generation charge is a percentage: negatives let members
            // mint money, values above 100 are nonsense and overflow the
            // decimal(5,2) column (50000 would silently become 999.99)
            'epin_charge'                    => 'nullable|numeric|between:0,100',
        ];
        $rules['auto_refer_fixed_user_id'] = $request->auto_refer_method === 'fixed'
            ? 'required|integer|exists:users,id'
            : 'nullable|integer';
        $request->validate($rules);

        if ($request->auto_refer_method === 'fixed' && !$request->auto_refer_fixed_user_id) {
            $notify[] = ['error', 'The fixed method needs a valid user id.'];
            return back()->withNotify($notify);
        }

        $general                          = GeneralSetting::first();
        $general->auto_refer_enable       = $request->boolean('auto_refer_enable') ? Status::ENABLE : Status::DISABLE;
        $general->auto_refer_require_active_plan = $request->boolean('auto_refer_require_active_plan') ? Status::ENABLE : Status::DISABLE;
        $general->auto_refer_require_email       = $request->boolean('auto_refer_require_email') ? Status::ENABLE : Status::DISABLE;
        $general->auto_refer_method       = $request->auto_refer_method;
        $general->auto_refer_fixed_user_id = (int) $request->auto_refer_fixed_user_id;
        $general->epin                    = $request->boolean('epin') ? Status::ENABLE : Status::DISABLE;
        $general->epin_charge             = (float) $request->epin_charge;
        $general->balance_transfer        = $request->boolean('balance_transfer') ? Status::ENABLE : Status::DISABLE;
        $general->user_ranking            = $request->boolean('user_ranking') ? Status::ENABLE : Status::DISABLE;
        // commission allocation (company/pool/royalty/weights) is owned by the
        // standalone Affiliate Plans section - this page no longer edits it.
        // Withdrawal eligibility has no bypasses: grace days and KYC gating removed.
        $general->save();

        $notify[] = ['success', 'Auto-refer settings updated successfully'];
        return back()->withNotify($notify);
    }

    public function report()
    {
        $pageTitle = 'AI Auto Assigned Report';
        $users     = User::where('ref_source', Status::REF_SOURCE_AUTO)->where('ref_by', '!=', 0)
            ->with('referrer')->searchable(['username'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.report.auto_referrals', compact('pageTitle', 'users'));
    }
}
