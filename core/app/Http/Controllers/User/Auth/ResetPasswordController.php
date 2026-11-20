<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\NotPwnedFailOpen;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {

        $email = session('fpass_email');
        $token = session()->has('token') ? session('token') : $token;
        $reset = PasswordReset::where('token', $token)->where('email', $email)->first();
        $ttl   = (int) config('auth.passwords.users.expire', 60);
        if (!$reset || $reset->created_at->lt(now()->subMinutes($ttl))) {
            $notify[] = ['error', 'Invalid or expired token'];
            return to_route('user.password.request')->withNotify($notify);
        }
        return view('Template::user.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $email, 'pageTitle' => 'Reset Password']
        );
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules());
        $reset = PasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        $ttl   = (int) config('auth.passwords.users.expire', 60);
        if (!$reset || $reset->created_at->lt(now()->subMinutes($ttl))) {
            $notify[] = ['error', 'Invalid or expired verification code'];
            return to_route('user.login')->withNotify($notify);
        }

        $user = User::where('email', $reset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // single-use: the code dies with the password change
        PasswordReset::where('email', $reset->email)->delete();

        // credential rotation evicts every other signed-in session
        try {
            Auth::logoutOtherDevices($request->password);
        } catch (\Throwable $e) {
            report($e);
        }

        $userBrowser = osBrowser();
        notify($user, 'PASS_RESET_DONE', [
            'operating_system' => isset($userBrowser['os_platform']) ? $userBrowser['os_platform'] : '',
            'browser' => isset($userBrowser['browser']) ? $userBrowser['browser'] : '',
            'ip' => getRealIp(),
            'time' => date('Y-m-d h:i:s A')
        ],['email']);

        $notify[] = ['success', 'Password changed successfully'];
        return to_route('user.login')->withNotify($notify);
    }


    protected function rules()
    {
        // Password::add() does not exist: custom rules ride along as
        // separate array entries in the 'password' rule list (fail-open
        // breach check, offline-safe per the NotPwnedFailOpen rule).
        $passwordValidation = Password::min(6);
        $passwordRules      = ['required','confirmed',$passwordValidation];
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols();
            $passwordRules[]    = new NotPwnedFailOpen();
        }
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => $passwordRules,
        ];
    }

}
