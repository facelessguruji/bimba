<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Constants\Status;
use App\Lib\Intended;

class AuthorizationController extends Controller
{
    protected function checkCodeValidity($user,$addMin = 2)
    {
        if (!$user->ver_code_send_at){
            return false;
        }
        if ($user->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }

    public function authorizeForm()
    {
        $user = auth()->user();
        if (!$user->status) {
            $pageTitle = 'Banned';
            $type = 'ban';
        }elseif(!$user->ev) {
            $type = 'email';
            $pageTitle = 'Verify Email';
            $notifyTemplate = 'EVER_CODE';
        }elseif (!$user->sv) {
            $type = 'sms';
            $pageTitle = 'Verify Mobile Number';
            $notifyTemplate = 'SVER_CODE';
        }elseif (!$user->tv) {
            $type = '2fa';
            $pageTitle = '2FA Verification';
        }else{
            return to_route('user.home');
        }

        if (!$this->checkCodeValidity($user) && ($type != 'ban') && ($type != '2fa')) {
            $user->ver_code = verificationCode(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
            notify($user, $notifyTemplate, [
                'code' => $user->ver_code
            ],[$type]);
        }

        return view('Template::user.auth.authorization.'.$type, compact('user', 'pageTitle'));

    }

    public function sendVerifyCode($type)
    {
        $user = auth()->user();

        if ($this->checkCodeValidity($user)) {
            $targetTime = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $user->ver_code = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();

        if ($type == 'email') {
            $type = 'email';
            $notifyTemplate = 'EVER_CODE';
        } else {
            $type = 'sms';
            $notifyTemplate = 'SVER_CODE';
        }

        notify($user, $notifyTemplate, [
            'code' => $user->ver_code
        ],[$type]);

        $notify[] = ['success', 'Verification code sent successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Brute-force guard for verification/2FA codes: 5 failed attempts clear
     * the stored code AND lock the account out of verification for 15 minutes
     * (persistent cache counter, not a self-resetting session value).
     */
    protected function checkVerifyAttempts($user, bool $matched): void
    {
        if ($matched) {
            Cache::forget('verify_fails_' . $user->id);
            return;
        }
        $attempts = (int) Cache::get('verify_fails_' . $user->id, 0) + 1;
        Cache::put('verify_fails_' . $user->id, $attempts, now()->addMinutes(15));

        if ($attempts >= 5) {
            Cache::put('verify_locked_' . $user->id, now()->timestamp, now()->addMinutes(15));
            Cache::forget('verify_fails_' . $user->id);
            if ($user->ver_code !== null) {
                $user->ver_code = null;
                $user->save();
            }
            throw ValidationException::withMessages(['code' => 'Too many wrong attempts. Verification is locked for 15 minutes.']);
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    /**
     * Lockout gate: throws when the member is inside a verification lockout.
     */
    protected function ensureNotVerifyLocked($user): void
    {
        if (Cache::has('verify_locked_' . $user->id)) {
            throw ValidationException::withMessages(['code' => 'Too many wrong attempts. Verification is locked. Try again later.']);
        }
    }

    /**
     * Verification codes are short-lived credentials  -  reject codes older
     * than the configured lifetime even if they still match.
     */
    protected function ensureCodeAlive($user): void
    {
        if ($user->ver_code_send_at && $user->ver_code_send_at->lt(now()->subMinutes(30))) {
            throw ValidationException::withMessages(['code' => 'This verification code has expired. Please request a new one.']);
        }
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'code'=>'required'
        ]);

        $user = auth()->user();
        $this->ensureNotVerifyLocked($user);
        $this->ensureCodeAlive($user);

        if ($user->ver_code == $request->code) {
            $this->checkVerifyAttempts($user, true);
            $user->ev = Status::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();

            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        }
        $this->checkVerifyAttempts($user, false);
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);


        $user = auth()->user();
        $this->ensureNotVerifyLocked($user);
        $this->ensureCodeAlive($user);

        if ($user->ver_code == $request->code) {
            $this->checkVerifyAttempts($user, true);
            $user->sv = Status::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        }
        $this->checkVerifyAttempts($user, false);
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function g2faVerification(Request $request)
    {
        $user = auth()->user();
        $this->ensureNotVerifyLocked($user);
        $request->validate([
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        } else {
            $this->checkVerifyAttempts($user, false);
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

}
