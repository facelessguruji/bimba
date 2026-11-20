<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function(){
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        // logout accepts GET too: a hand-typed /user/logout used to throw a
        // raw 405 error page instead of logging out gracefully
        Route::match(['GET', 'POST'], 'logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function(){
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('throttle:register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest')->middleware('throttle:check-user');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function(){
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email')->middleware('throttle:auth-sensitive');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code')->middleware('throttle:auth-sensitive');
    });

    Route::controller('ResetPasswordController')->group(function(){
        Route::post('password/reset', 'reset')->name('password.update')->middleware('throttle:auth-sensitive');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function(){
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->middleware('throttle:auth-sensitive')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->middleware('throttle:auth-sensitive')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify')->middleware('throttle:auth-sensitive');
    });

    Route::middleware(['check.status','registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function(){
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                // Favorite
                Route::post('favorite-listing/{id}', 'addToFavorite')->name('listing.add.to.favorite');
                Route::get('favorite-listing', 'favoriteListings')->name('listing.favorite');

                //Report
                Route::any('payment/history', 'depositHistory')->name('payment.history');
                Route::post('review-rating/{id}', 'storeRating')->name('review.store');
                Route::get('choose-plan/{id}', 'choosePlan')->name('plan.choose');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');

                Route::post('add-device-token','addDeviceToken')->name('add.device.token');

            });

            //Affiliate engine (user side)
            Route::controller('AffiliateController')->group(function(){
                Route::get('affiliate', 'hub')->name('affiliate.hub');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('my-referrals', 'myReferral')->name('my.referral');
                Route::get('team/levels/{username?}', 'teamLevels')->name('team.levels');
                Route::get('held-earnings', 'heldEarnings')->name('held.earnings');
                Route::get('ranking', 'ranking')->name('ranking');
                Route::get('transfer-balance', 'balanceTransfer')->name('balance.transfer');
                Route::post('transfer-balance', 'transferConfirm')->middleware('throttle:money');
            });

            //2FA
            Route::controller('TwofactorController')->group(function(){
                Route::get('twofactor', 'showForm')->name('twofactor');
                Route::post('twofactor/enable', 'enable')->middleware('throttle:auth-sensitive')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable')->middleware('throttle:auth-sensitive')->name('twofactor.disable');
            });

            //KYC
            Route::controller('KycController')->group(function(){
                Route::get('kyc-form', 'form')->name('kyc.form');
                Route::post('kyc-submit', 'submit')->name('kyc.submit');
            });

            //E-pin
            Route::controller('EpinController')->group(function(){
                Route::get('e-pin/recharge', 'epin')->name('epin.recharge');
                Route::get('e-pin/recharge/log', 'epinRechargeLog')->name('recharge.log');
                Route::post('e-recharge', 'eRecharge')->middleware('throttle:money')->name('erecharge');
                Route::post('pin/generate', 'pinGenerate')->middleware('throttle:money')->name('pin.generate');
            });

            //Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function(){
                Route::get('/', 'withdrawMoney')->name('.money');
                Route::post('/', 'withdrawStore')->name('.store')->middleware('throttle:money');
                Route::get('preview', 'withdrawPreview')->name('.preview');
                Route::post('preview', 'withdrawSubmit')->name('.submit')->middleware('throttle:money');
                Route::post('cancel/{trx}', 'cancel')->name('.cancel')->middleware('throttle:money');
                Route::get('history', 'withdrawLog')->name('.history');
            });

            //Plan purchase with wallet / e-pin
            Route::controller('PlanPurchaseController')->group(function(){
                Route::post('plan/purchase', 'purchase')->middleware('throttle:money')->name('plan.purchase');
            });

            //Events
            Route::controller('EventController')->name('events.')->group(function(){
                Route::get('events', 'index')->name('index');
                Route::get('event/create', 'create')->name('create');
                Route::post('event/store', 'store')->name('store');
                Route::get('event/edit/{id}', 'edit')->name('edit');
                Route::post('event/update/{id}', 'update')->name('update');
            });

            //Internal company events (members only, rendered in the user panel)
            Route::controller('InternalEventController')->group(function(){
                Route::get('internal-events', 'index')->name('internal.events');
                Route::get('internal-events/{slug}/{id}', 'details')->name('internal.event.details');
                Route::post('internal-events/{id}/register', 'register')->name('internal.event.register');
            });

            //Lead inbox
            Route::get('leads', 'UserController@leads')->name('leads');
            Route::get('leads/export', 'UserController@exportLeads')->name('leads.export');
            Route::post('leads/status/{id}', 'UserController@leadStatus')->name('leads.status');

            //Profile setting
            Route::controller('ProfileController')->group(function(){
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword')->middleware('throttle:auth-sensitive');
            });

            //AI listing writer
            Route::post('ai/generate-listing-copy', 'ListingController@aiGenerate')->name('listing.ai.generate');

            //Listing
            Route::controller('ListingController')->prefix('listing')->name('listing.')->group(function () {
                Route::get('index', 'myListings')->name('all');
                Route::get('create', 'form')->name('create');
                Route::post('store/{id?}', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('pending-disabled/{id}/{status}', 'switchStatus')->name('status');
            });

        });

        // Payment
        Route::prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function(){
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert')->middleware('throttle:money');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});

// Bare-section redirects: hand-typed member URLs land on the real page
// instead of a 404.
Route::get('listing', fn () => redirect()->route('user.listing.all'));
Route::get('epin', fn () => redirect()->route('user.epin.recharge'));
Route::get('e-pin', fn () => redirect()->route('user.epin.recharge'));
Route::get('ticket', fn () => redirect()->route('ticket.index'));

