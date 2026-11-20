<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\Demo;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RegistrationStep;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using:function(){
            // hidden admin path: set ADMIN_PREFIX in .env (random string). The
            // old /admin address stops existing entirely; nothing about the
            // admin routes is discoverable without knowing the prefix.
            // read via config() so the prefix survives `config:cache`
            // (a cached config nulls direct env() reads outside config files).
            $adminPrefix = config('app.admin_prefix');

            Route::namespace('App\Http\Controllers')->group(function() use ($adminPrefix){

                Route::middleware(['web'])
                    ->namespace('Admin')
                    ->prefix($adminPrefix)
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));

                    Route::middleware(['web','maintenance'])
                    ->namespace('Gateway')
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));

                Route::middleware(['web','maintenance'])->prefix('user')->group(base_path('routes/user.php'));
                Route::middleware(['web','maintenance'])->group(base_path('routes/web.php'));

            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Laravel's default guest redirect is route('login'), which does not
        // exist in this app (logins are named admin.login / user.login). The
        // stale-session path (AuthenticateSession evicting a session whose
        // password hash changed, e.g. after an admin password reset) used to
        // blow up with "Route [login] not defined". Point guests at the right
        // login screen for their area instead.
        $middleware->redirectGuestsTo(function ($request) {
            $adminPrefix = config('app.admin_prefix');
            return $request->is($adminPrefix) || $request->is($adminPrefix.'/*')
                ? route('admin.login')
                : route('user.login');
        });

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            // AuthenticateSession is deliberately NOT used: it stores ONE
            // password hash (password_hash_web) shared by both the admin and
            // member guards, so logging into one area silently logged the
            // other out on the next request (admin login -> bounced to user
            // login, error page, then dashboard). Password changes still
            // invalidate the changed account's own session.
            \App\Http\Middleware\LanguageMiddleware::class,
            \App\Http\Middleware\ActiveTemplateMiddleware::class,
        ]);

        $middleware->alias([
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            'admin' => RedirectIfNotAdmin::class,
            'admin.guest' => RedirectIfAdmin::class,

            'check.status' => CheckStatus::class,
            'demo' => Demo::class,
            'registration.complete' => RegistrationStep::class,
            'maintenance' => MaintenanceMode::class,
        ]);

        $middleware->preventRequestForgery(
            except: ['ipn*']
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function () {
            if (request()->is('api/*')) {
                return true;
            }
        });
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 401) {
                if (request()->is('api/*')) {
                    $notify[] = 'Unauthorized request';
                    return response()->json([
                        'remark' => 'unauthenticated',
                        'status' => 'error',
                        'message' => ['error' => $notify]
                    ]);
                }
            }

            // hand-typed GET on a POST-only action URL (approve, delete, store,
            // logout...) used to render a raw "Method Not Allowed" page. Destructive
            // actions must never run on GET, so send the visitor somewhere sensible:
            // back to the page they came from, or their area's dashboard when the
            // visit was direct (no referrer).
            if ($response->getStatusCode() === 405 && !request()->is('api/*')) {
                $adminPrefix = config('app.admin_prefix');
                $referrer = request()->header('referer');
                $target = ($referrer && $referrer !== request()->fullUrl() && str_starts_with($referrer, request()->root()))
                    ? $referrer
                    : (request()->is($adminPrefix) || request()->is($adminPrefix.'/*')
                        ? route('admin.dashboard') : route('home'));
                return redirect()->to($target)->with('notify', [
                    ['info', 'That action is triggered by a button on its page, not by opening the link directly.']
                ]);
            }

            return $response;
        });
    })->create();
