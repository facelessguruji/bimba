<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Constants\Status;
use App\Models\Admin;
use App\Models\AdminPasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        $pageTitle = "Account Recovery";
        $ttl        = (int) config('auth.passwords.admins.expire', 60);
        $resetToken = AdminPasswordReset::where('token', $token)->where('status', Status::ENABLE)->first();
        // reset codes are single-use credentials with a hard lifetime;
        // bounce to the recovery request form (admin.password.reset)
        if (!$resetToken || $resetToken->created_at->lt(now()->subMinutes($ttl))) {
            $notify[] = ['error', 'Invalid or expired token'];
            return to_route('admin.password.reset')->withNotify($notify);
        }
        $email = $resetToken->email;
        return view('admin.auth.passwords.reset', compact('pageTitle', 'email', 'token'));
    }


    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:4',
        ]);

        $ttl   = (int) config('auth.passwords.admins.expire', 60);
        $reset = AdminPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset || $reset->created_at->lt(now()->subMinutes($ttl))) {
            $notify[] = ['error', 'Invalid or expired verification code'];
            return to_route('admin.login')->withNotify($notify);
        }
        $admin = Admin::where('email', $reset->email)->first();
        if ($reset->status == Status::DISABLE) {
            $notify[] = ['error', 'Invalid code'];
            return to_route('admin.login')->withNotify($notify);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        // single-use: the code dies with the password change
        AdminPasswordReset::where('token', $request->token)->update(['status' => Status::DISABLE]);
        try {
            Auth::logoutOtherDevices($request->password);
        } catch (\Throwable $e) {
            report($e);
        }
        $reset->status = Status::DISABLE;
        $reset->save();

        $browser = osBrowser();
        notify($admin, 'PASS_RESET_DONE', [
            'operating_system' => isset($browser['os_platform']) ? $browser['os_platform'] : '',
            'browser' => isset($browser['browser']) ? $browser['browser'] : '',
            'ip' => getRealIp(),
            'time' => date('Y-m-d h:i:s A')
        ],['email'],false);

        $notify[] = ['success', 'Password changed'];
        return to_route('admin.login')->withNotify($notify);
    }
}
