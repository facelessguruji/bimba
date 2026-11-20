<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Lib\PlanSubscription;
use App\Models\AdminNotification;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\NotPwnedFailOpen;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{

    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";

        // Single referral link support: ?ref=CONTACT_NUMBER (the member's
        // dial code + mobile, digits only). Legacy links that still carry a
        // username resolve through the silent fallback in resolveReferrer().
        // Invalid refs are silently ignored  -  never an error.
        $ref      = request()->query('ref');
        $referrer = $ref ? $this->resolveReferrer($ref) : null;

        // Global Auto-Referral preview: show who the platform would assign
        // when someone registers without a referral link (transparency).
        $autoSponsor = null;
        if (!$referrer && gs('auto_refer_enable')) {
            try {
                $autoSponsor = \App\Lib\Affiliate::previewAutoSponsor();
            } catch (\Throwable $e) {
                $autoSponsor = null;
            }
        }

        Intended::identifyRoute();
        return view('Template::user.auth.register', compact('pageTitle', 'referrer', 'autoSponsor'));
    }


    protected function validator(array $data)
    {

        // Password::add() does not exist: custom rules ride along as
        // separate array entries in the 'password' rule list (fail-open
        // breach check, offline-safe per the NotPwnedFailOpen rule).
        $passwordValidation = Password::min(6);

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $passwordRules = ['required', 'confirmed', $passwordValidation];
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols();
            $passwordRules[]    = new \App\Rules\NotPwnedFailOpen();
        }

        $validate     = Validator::make($data, [
            'firstname' => 'required|max:40',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:users',
            'password'  => $passwordRules,
            'captcha'   => 'sometimes|required',
            'agree'     => $agree
        ],[
            'firstname.required'=>'The first name field is required',
            'lastname.required'=>'The last name field is required',
        ]);

        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }



        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }



    protected function create(array $data)
    {


        //User Create
        $user            = new User();
        $user->email     = strtolower($data['email']);
        $user->firstname = $data['firstname'];
        $user->lastname  = $data['lastname'];
        // temporary internal username  -  the member chooses their real one in
        // step 2 (profile completion). Kept lowercase-alphanumeric and unique
        // so the per-column unique index can never collide.
        do {
            $tempUsername = 'u' . strtolower(substr(md5(uniqid((string) mt_rand(), true)), 0, 12));
        } while (User::where('username', $tempUsername)->exists());
        $user->username  = $tempUsername;
        $user->password  = Hash::make($data['password']);
        $user->ev = gs('ev') ? Status::NO : Status::YES;
        $user->sv = gs('sv') ? Status::NO : Status::YES;
        $user->tv = Status::YES;
        // last-line integrity: the per-column unique indexes decide, not the
        // check-then-act validator  -  a race lands here as a clean message
        try {
            $user->save();
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            throw ValidationException::withMessages([
                'username' => 'This username or email is already taken',
            ]);
        }

        // Assign monoline position (global queue: everyone who joins after
        // you is below you in the line, regardless of who referred them).
        // Minted AND persisted under the settings-row mutex so concurrent
        // registrations can never share a position.
        \App\Lib\Affiliate::assignMonolinePosition($user);

        // Single referral link: ?ref=CONTACT_NUMBER (from hidden field or
        // query). Legacy username links keep working through the silent
        // fallback. Invalid or missing refs are treated as organic, never
        // an error.
        $ref      = $data['ref'] ?? request()->query('ref');
        $referrer = $ref ? $this->resolveReferrer($ref) : null;
        if ($referrer && $referrer->id != $user->id) {
            $user->ref_by     = $referrer->id;
            $user->ref_source = Status::REF_SOURCE_LINK;
            $user->save();
        }

        // Global Auto-Referral: nobody joins orphaned. When there was no
        // valid referrer, assign one from the eligible pool.
        if (!$user->ref_by) {
            (new \App\Lib\Affiliate())->assignAutoReferral($user);
        } else {
            \App\Lib\Affiliate::countNewReferral($user);
        }

        if (gs('default_plan_status') && gs('default_plan') && gs('default_plan_duration') > 0) {
            $plan = Plan::active()->where('id', gs('default_plan'))->first();

            // safety: the configured default plan must be genuinely FREE  -  a
            // mispointed setting must never hand out a paid plan for free
            if (!$plan || (float) $plan->monthly_price > 0 || (float) $plan->yearly_price > 0) {
                $plan = Plan::active()
                    ->where('monthly_price', 0)
                    ->where('yearly_price', 0)
                    ->orderBy('id')
                    ->first();
            }

            // retry-safe: never create a second active subscription
            if ($plan && !$user->activePlan()->exists()) {
                $plan->price = 0;
                $plan->expired_on = now()->addDays(gs('default_plan_duration'));
                $planSubscription = new PlanSubscription($plan, $user);
                $planSubscription->subscribe();

                notify($user, 'DEFAULT_PLAN', [
                    'name'     => $plan->title,
                    'expired'  => $plan->expired_on,
                    'property'  => $plan->listings,
                ]);
            }
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();


        //Login Log Create
        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = isset($info['long']) ? implode(',', $info['long']) : '';
            $userLogin->latitude     = isset($info['lat']) ? implode(',', $info['lat']) : '';
            $userLogin->city         = isset($info['city']) ? implode(',', $info['city']) : '';
            $userLogin->country_code = isset($info['code']) ? implode(',', $info['code']) : '';
            $userLogin->country      = isset($info['country']) ? implode(',', $info['country']) : '';
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = isset($userAgent['browser']) ? $userAgent['browser'] : '';
        $userLogin->os      = isset($userAgent['os_platform']) ? $userAgent['os_platform'] : '';
        $userLogin->save();


        return $user;
    }

    /**
     * Resolve a ?ref= value to an active member by contact number: the
     * input is normalized to digits only (spaces, +, dashes and other
     * separators are stripped), then matched against the bare mobile or
     * the dial code + mobile as stored on the member. Old links that
     * carried a username keep working through this silent legacy
     * fallback. Unknown or malformed refs return null (organic path,
     * never an error).
     */
    protected function resolveReferrer($ref): ?User
    {
        if (!is_string($ref) || $ref === '') {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $ref);
        if ($digits !== '') {
            // PRIMARY (owner directive D16): full international format
            // dial_code + mobile, e.g. 917760406490. Exact match first so
            // members sharing a bare mobile across dial codes never conflict.
            $referrer = User::where('status', Status::USER_ACTIVE)
                ->whereRaw('CONCAT(dial_code, mobile) = ?', [$digits])
                ->orderBy('id')
                ->first();
            if ($referrer) {
                return $referrer;
            }
            // SECONDARY: bare local mobile (shared links typed without the
            // dial code still resolve; ties break on lowest id)
            $referrer = User::where('status', Status::USER_ACTIVE)
                ->where('mobile', $digits)
                ->orderBy('id')
                ->first();
            if ($referrer) {
                return $referrer;
            }
        }
        return User::where('status', Status::USER_ACTIVE)->where('username', strtolower($ref))->first();
    }

    public function checkUser(Request $request){
        // string rules: array input (e.g. email[]=x) must fail validation
        // cleanly instead of loose-binding into a wrong exists() answer
        $request->validate([
            'email'       => 'nullable|string',
            'mobile'      => 'nullable|string',
            'mobile_code' => 'nullable|string',
            'username'    => 'nullable|string',
        ]);
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email',$request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile',$request->mobile)->where('dial_code',$request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username',$request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered()
    {
        // registration auto-logs the member in, so it must also honour the
        // intended URL (e.g. the listing the guest came from via View Contact)
        $redirection = \App\Lib\Intended::getRedirection();
        $notify[]    = ['success', 'Account created successfully. Welcome to ' . gs('site_name') . '!'];
        return ($redirection ? $redirection : to_route('user.home'))->withNotify($notify);
    }

}
