<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $pageTitle = "Account Recovery";
        return view('Template::user.auth.passwords.email', compact('pageTitle'));
    }

    public function sendResetCodeEmail(Request $request)
    {
        $request->validate([
            'value'=>'required'
        ]);

        if(!verifyCaptcha()){
            $notify[] = ['error','Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $fieldType = $this->findFieldType();
        $user = User::where($fieldType, $request->value)->first();

        if (!$user) {
            // uniform response: the endpoint must not reveal whether the
            // account exists (enumeration surface)
            session()->put('pass_res_mail', $request->value);
            $notify[] = ['success', 'Password reset email sent successfully'];
            return to_route('user.password.code.verify')->withNotify($notify);
        }

        PasswordReset::where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = \Carbon\Carbon::now();
        $password->save();

        $userBrowserInfo = osBrowser();
        notify($user, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => isset($userBrowserInfo['os_platform']) ? $userBrowserInfo['os_platform'] : '',
            'browser' => isset($userBrowserInfo['browser']) ? $userBrowserInfo['browser'] : '',
            'ip' => getRealIp(),
            'time' => date('Y-m-d h:i:s A')
        ],['email']);

        $email = $user->email;
        session()->put('pass_res_mail',$email);
        $notify[] = ['success', 'Password reset email sent successfully'];
        return to_route('user.password.code.verify')->withNotify($notify);
    }

    public function findFieldType()
    {
        $input = request()->input('value');

        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $input]);
        return $fieldType;
    }

    public function codeVerify(Request $request){
        $pageTitle = 'Verify Email';
        $email = $request->session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error','Oops! session expired'];
            return to_route('user.password.request')->withNotify($notify);
        }
        return view('Template::user.auth.passwords.code_verify',compact('pageTitle','email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required'
        ]);
        $code =  str_replace(' ', '', $request->code);

        $reset = PasswordReset::where('token', $code)->where('email', $request->email)->first();
        // reset codes are single-use credentials with a hard lifetime  -  a
        // code seen once (shared inbox, mail log) must not become a
        // permanent password-change key
        $ttl = (int) config('auth.passwords.users.expire', 60);
        if (!$reset || $reset->created_at->lt(now()->subMinutes($ttl))) {
            $notify[] = ['error', 'Verification code doesn\'t match or has expired'];
            return to_route('user.password.request')->withNotify($notify);
        }
        $notify[] = ['success', 'You can change your password'];
        session()->flash('fpass_email', $request->email);
        return to_route('user.password.reset', $code)->withNotify($notify);
    }

}
