<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function allUsers()
    {
        $pageTitle = 'KYC Users';
        $users     = User::where('kv', '!=', Status::KYC_UNVERIFIED)->searchable(['username'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.kyc.users', compact('pageTitle', 'users'));
    }

    public function pendingUsers()
    {
        $pageTitle = 'KYC Pending Users';
        $users     = User::where('kv', Status::KYC_PENDING)->searchable(['username'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.kyc.users', compact('pageTitle', 'users'));
    }

    public function approvedUsers()
    {
        $pageTitle = 'KYC Approved Users';
        $users     = User::where('kv', Status::KYC_VERIFIED)->searchable(['username'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.kyc.users', compact('pageTitle', 'users'));
    }

    public function details($id)
    {
        $pageTitle = 'KYC Details';
        $user      = User::findOrFail($id);
        return view('admin.kyc.detail', compact('pageTitle', 'user'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->kv = Status::KYC_VERIFIED;
        $user->save();

        notify($user, 'KYC_APPROVE', []);

        $notify[] = ['success', 'KYC approved successfully'];
        return to_route('admin.kyc.pending')->withNotify($notify);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required'
        ]);
        $user = User::findOrFail($id);
        $user->kv = Status::KYC_UNVERIFIED;
        $user->kyc_rejection_reason = $request->reason;
        $user->save();

        notify($user, 'KYC_REJECT', [
            'rejection_message' => $request->reason
        ]);

        $notify[] = ['success', 'KYC rejected successfully'];
        return to_route('admin.kyc.pending')->withNotify($notify);
    }

    public function setting()
    {
        $pageTitle = 'KYC Setting';
        $form      = Form::where('act', 'kyc')->first();
        return view('admin.kyc.setting', compact('pageTitle', 'form'));
    }

    public function settingUpdate(Request $request)
    {
        $formProcessor       = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', 'kyc')->first();
        $formProcessor->generate('kyc', $exist, 'act');

        $notify[] = ['success', 'KYC data updated successfully'];
        return back()->withNotify($notify);
    }
}
