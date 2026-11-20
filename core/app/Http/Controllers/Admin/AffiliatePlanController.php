<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AffiliatePlanController extends Controller
{
    /**
     * Standalone Affiliate Plans section: the commission configuration for
     * the ten-level referral model. Separate from Manage Plans (listing
     * subscription tiers) — the two work together but stay distinct.
     */
    public function index()
    {
        $pageTitle = 'Affiliate Plans';
        return view('admin.affiliate_plan.index', compact('pageTitle'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'affiliate_company_percent' => 'required|numeric|min:0|max:100',
            'affiliate_pool_percent'    => 'required|numeric|min:0|max:100',
            'affiliate_royalty_percent' => 'required|numeric|min:0|max:100',
            'affiliate_level_weights'   => 'required',
            'affiliate_level_weights.*' => 'numeric|min:0',
            'activation_rule'           => 'nullable|in:0,1',
            'auto_refer_method'         => 'nullable|in:round_robin,random,fixed,weakest_team,top_recruiter',
            'auto_refer_fixed_user_id'  => 'nullable|integer|exists:users,id',
        ]);

        // The form posts one input per level (affiliate_level_weights[]),
        // but a comma-separated string is still accepted for robustness.
        $rawWeights = $request->input('affiliate_level_weights');
        if (!is_array($rawWeights)) {
            // scalar form: every comma-separated slot must be numeric —
            // garbage like "abc" must be rejected, not silently zeroed
            foreach (explode(',', (string) $rawWeights) as $slot) {
                if (trim($slot) !== '' && !is_numeric(trim($slot))) {
                    $notify[] = ['error', 'Level weights must be numbers.'];
                    return back()->withNotify($notify);
                }
            }
            $rawWeights = array_map('trim', explode(',', (string) $rawWeights));
        }
        $weights = array_map('floatval', $rawWeights);
        $weights = array_slice(array_map(fn ($w) => min(1000, max(0, $w)), $weights), 0, 10);
        if (array_sum($weights) <= 0) {
            $notify[] = ['error', 'At least one level weight must be greater than zero.'];
            return back()->withNotify($notify);
        }

        // allocation must total 100 and the affiliate pool can never exceed
        // the specified 40% share of eligible revenue
        $allocationTotal = (float) $request->affiliate_company_percent
            + (float) $request->affiliate_pool_percent
            + (float) $request->affiliate_royalty_percent;
        if (abs($allocationTotal - 100) > 0.01) {
            $notify[] = ['error', 'Company + pool + royalty must add up to 100%'];
            return back()->withNotify($notify);
        }
        if ((float) $request->affiliate_pool_percent > 40) {
            $notify[] = ['error', 'The total affiliate commission pool can never exceed 40%'];
            return back()->withNotify($notify);
        }

        $general = gs();
        $general->affiliate_company_percent = min(100, max(0, (float) $request->affiliate_company_percent));
        $general->affiliate_pool_percent    = min(100, max(0, (float) $request->affiliate_pool_percent));
        $general->affiliate_royalty_percent = min(100, max(0, (float) $request->affiliate_royalty_percent));
        $general->affiliate_level_weights   = implode(',', $weights);
        $general->activation_rule           = (int) $request->activation_rule;
        $general->auto_refer_method         = $request->auto_refer_method ?: 'round_robin';
        $general->auto_refer_fixed_user_id  = (int) $request->auto_refer_fixed_user_id;
        $general->save();

        $notify[] = ['success', 'Affiliate plan configuration updated successfully'];
        return back()->withNotify($notify);
    }
}
