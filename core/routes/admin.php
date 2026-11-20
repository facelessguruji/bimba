<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function(){
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login');
            // logout accepts GET too: a hand-typed or stale /admin/logout used
            // to throw a raw 405 error page instead of logging out gracefully
            Route::match(['GET', 'POST'], 'logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });
        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function(){
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail')->middleware('throttle:auth-sensitive');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code')->middleware('throttle:auth-sensitive');
        });

        Route::controller('ResetPasswordController')->group(function(){
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change')->middleware('throttle:auth-sensitive');
        });
    });
});

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function(){
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications','notifications')->name('notifications');
        Route::get('notification/read/{id}','notificationRead')->name('notification.read');
        Route::get('notifications/read-all','readAllNotification')->name('notifications.read.all');
        Route::post('notifications/delete-all','deleteAllNotification')->name('notifications.delete.all');
        Route::post('notifications/delete-single/{id}','deleteSingleNotification')->name('notifications.delete.single');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    //Plan
    Route::controller('PlanController')->prefix('plan')->name('plan.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Category
    Route::controller('CategoryController')->prefix('category')->name('category.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('feature/{id}', 'feature')->name('feature');
    });

    //Country
    Route::controller('CountryController')->prefix('country')->name('country.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Location
    Route::controller('CityController')->prefix('city')->name('city.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('feature/{id}', 'feature')->name('feature');
    });

    //Manage Listing
    Route::controller('ListingController')->prefix('listing')->name('listing.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('approved', 'approvedListings')->name('approved');
        Route::get('rejected', 'rejectedListings')->name('rejected');
        Route::get('pending', 'pendingListings')->name('pending');
        Route::get('disabled', 'disabledListings')->name('disabled');
        Route::get('details/{id}', 'details')->name('details');

        Route::post('status/{id}', 'status')->name('status');
        Route::post('feature/{id}', 'feature')->name('feature');
        Route::post('approve/{id}', 'approve')->name('approve');
        Route::post('reject/{id}', 'reject')->name('reject');
    });

    //== Review ==
    Route::controller('ReviewController')->name('review.')->prefix('review')->group(function () {
        Route::get('all', 'index')->name('index');
        Route::post('delete/{id}/{slug}', 'delete')->name('delete');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function(){
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single.submit');
        // GET renders a read-only confirmation page so the direct URL
        // /admin/users/login/{id} works again; the state change itself
        // still requires a CSRF-token POST (see ManageUsersController)
        Route::get('login/{id}', 'confirmLogin')->name('login.confirm');
        Route::post('login/{id}', 'login')->middleware('throttle:auth-sensitive')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('list', 'list')->name('list');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');

        //Affiliate admin extensions
        Route::get('with-plan', 'paidUsers')->name('paid');
        Route::post('balance/{id}', 'addSubBalance')->name('balance');
        Route::get('team/{username}', 'teamTree')->name('team');
    });

    //E-Pins
    Route::controller('PinController')->prefix('pin')->name('pin.')->group(function () {
        Route::get('/', 'allPins')->name('index');
        Route::post('/add', 'store')->name('store');
        Route::get('/user', 'userPins')->name('user');
        Route::get('/admin', 'adminPins')->name('admin');
        Route::get('/used', 'usedPins')->name('used');
        Route::get('/unused', 'unusedPins')->name('unused');
    });

    //E-Pin Agents (B4: volume discounts + seller bonus)
    Route::controller('AgentController')->prefix('agent')->name('agent.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/toggle/{id}', 'toggle')->name('toggle');
        Route::post('/settings', 'settings')->name('settings');
    });

    //Affiliate Ranks
    Route::controller('RankingController')->name('ranking.')->prefix('ranking')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store/{id?}', 'store')->name('store');
        Route::post('/status/{id}', 'status')->name('status');
    });

    //Withdrawals
    Route::name('withdraw.')->prefix('withdraw')->group(function () {
        Route::get('/pending', 'WithdrawalController@pending')->name('data.pending');
        Route::get('/approved', 'WithdrawalController@approved')->name('data.approved');
        Route::get('/rejected', 'WithdrawalController@rejected')->name('data.rejected');
        Route::get('/all', 'WithdrawalController@all')->name('data.all');
        Route::get('/details/{id}', 'WithdrawalController@details')->name('data.details');
        Route::post('/approve', 'WithdrawalController@approve')->name('approve');
        Route::post('/reject', 'WithdrawalController@reject')->name('reject');

        //Withdraw methods
        Route::controller('WithdrawMethodController')->prefix('method')->name('method.')->group(function () {
            Route::get('/', 'methods')->name('index');
            Route::get('/new', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::post('/update/{id}', 'update')->name('update');
            Route::post('/status/{id}', 'status')->name('status');
        });
    });

    //KYC
    Route::controller('KycController')->name('kyc.')->prefix('kyc')->group(function () {
        Route::get('/setting', 'setting')->name('setting');
        Route::post('/setting', 'settingUpdate');
        Route::get('/users', 'allUsers')->name('all');
        Route::get('/pending', 'pendingUsers')->name('pending');
        Route::get('/approved', 'approvedUsers')->name('approved');
        Route::get('/details/{id}', 'details')->name('details');
        Route::post('/approve/{id}', 'approve')->name('approve');
        Route::post('/reject/{id}', 'reject')->name('reject');
    });

    //Transactions ledger
    Route::controller('TransactionController')->name('transactions.')->prefix('transactions')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    //Held earnings
    Route::controller('HeldCommissionController')->name('held.')->prefix('held')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/release/{id}', 'release')->name('release');
    });

    //Auto-Refer
    Route::controller('AutoReferController')->prefix('auto-refer')->group(function () {
        Route::get('/setting', 'setting')->name('setting.auto-refer');
        Route::post('/setting', 'settingUpdate');
        Route::get('/report', 'report')->name('report.auto-referrals');
    });


    //Events
    Route::controller('EventController')->prefix('events')->name('events.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/pending', 'pending')->name('pending');
        Route::get('/approved', 'approved')->name('approved');
        Route::post('/approve/{id}', 'approve')->name('approve');
        Route::post('/reject/{id}', 'reject')->name('reject');
        Route::get('/internal/create', 'create')->name('internal.create');
        Route::post('/internal/store', 'store')->name('internal.store');
        Route::get('/internal/edit/{id}', 'editInternal')->name('internal.edit');
        Route::post('/internal/update/{id}', 'updateInternal')->name('internal.update');
        Route::post('/internal/status/{id}', 'internalStatus')->name('internal.status');
    });
    Route::controller('EventCategoryController')->prefix('event-category')->name('event.category.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
    });

    //Affiliate Plans (standalone commission configuration - NOT Manage Plans)
    Route::controller('AffiliatePlanController')->name('affiliate.plan.')->prefix('affiliate-plan')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update', 'update')->name('update');
    });

    //AI settings + leads
    Route::controller('AiController')->name('ai.')->prefix('ai')->group(function () {
        Route::get('/settings', 'settings')->name('settings');
        Route::post('/settings', 'settingsUpdate');
        Route::get('/logs', 'logs')->name('logs');
        Route::get('/seo', 'seo')->name('seo');
    });

    //Leads (named per the build-prompt route map)
    Route::controller('AiController')->name('lead.')->prefix('lead')->group(function () {
        Route::get('/', 'leads')->name('index');
        Route::get('export', 'leadExport')->name('export');
        Route::post('import', 'leadImport')->name('import');
        Route::get('/{id}', 'leadDetails')->name('details');
        Route::post('/{id}/status', 'leadStatus')->name('status');
    });

    //Cron Jobs
    Route::controller('CronConfigurationController')->name('cron.')->prefix('cron')->group(function () {
        Route::get('index', 'cronJobs')->name('index');
        Route::post('store', 'cronJobStore')->name('store');
        Route::post('update', 'cronJobUpdate')->name('update');
        Route::post('delete/{id}', 'cronJobDelete')->name('delete');
        Route::post('schedule/store', 'scheduleStore')->name('schedule.store');
        Route::post('schedule/update/{id}', 'scheduleUpdate')->name('schedule.update');
        Route::post('schedule/status/{id}', 'scheduleStatus')->name('schedule.status');
        // schedule deletion via GET removed: destructive actions must never be
        // reachable by a plain link (the controller method stays for future use)
        Route::get('log/{id}', 'log')->name('log');
        Route::post('log/flush/{id}', 'flushLog')->name('log.flush');
        Route::get('schedule', 'schedule')->name('schedule');
        Route::post('pause/{id}', 'cronJobPause')->name('pause');
    });

    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->group(function(){
        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });


        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });


    // DEPOSIT SYSTEM
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function(){
        Route::get('all/{user_id?}', 'deposit')->name('list');
        Route::get('pending/{user_id?}', 'pending')->name('pending');
        Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
        Route::get('approved/{user_id?}', 'approved')->name('approved');
        Route::get('successful/{user_id?}', 'successful')->name('successful');
        Route::get('initiated/{user_id?}', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
        Route::post('refund/{id}', 'refund')->name('refund');

    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function(){
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ip.history');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });


    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function(){
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });


    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function(){
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::controller('GeneralSettingController')->group(function(){

        Route::get('system-setting', 'systemSetting')->name('setting.system');

        // General Setting
        Route::get('general-setting', 'general')->name('setting.general');
        Route::post('general-setting', 'generalUpdate');

        //configuration
        Route::get('setting/system-configuration','systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration','systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css','customCss')->name('setting.custom.css');
        Route::post('custom-css','customCssSubmit');

        Route::get('sitemap','sitemap')->name('setting.sitemap');
        Route::post('sitemap','sitemapSubmit');

        Route::get('robot','robot')->name('setting.robot');
        Route::post('robot','robotSubmit');

        //Cookie
        Route::get('cookie','cookie')->name('setting.cookie');
        Route::post('cookie','cookieSubmit');

        //maintenance_mode
        Route::get('maintenance-mode','maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode','maintenanceModeSubmit');

    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function(){
        //Template Setting
        Route::get('global/email','globalEmail')->name('global.email');
        Route::post('global/email/update','globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms','globalSms')->name('global.sms');
        Route::post('global/sms/update','globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push','globalPush')->name('global.push');
        Route::post('global/push/update','globalPushUpdate')->name('global.push.update');

        Route::get('templates','templates')->name('templates');
        Route::get('template/edit/{type}/{id}','templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}','templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting','emailSetting')->name('email');
        Route::post('email/setting','emailSettingUpdate')->name('email.update');
        Route::post('email/test','emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting','smsSetting')->name('sms');
        Route::post('sms/setting','smsSettingUpdate')->name('sms.update');
        Route::post('sms/test','smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate')->name('push.update');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });


    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function(){
        Route::get('info','systemInfo')->name('info');
        Route::get('server-info','systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('system-update','systemUpdate')->name('update');
        Route::post('system-update','systemUpdateProcess')->name('update.process');
        Route::get('system-update/log','systemUpdateLog')->name('update.log');
    });


    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');


    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function(){
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key?}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate')->name('sections.element.seo.update');
            Route::post('update-seo', 'updateSeoContent')->name('seo.update');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function(){
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::get('manage-pages/check-slug/{id?}', 'checkSlug')->name('manage.pages.check.slug');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');

            Route::get('manage-seo/{id}','manageSeo')->name('manage.pages.seo');
            Route::post('manage-seo/{id}','manageSeoStore')->name('manage.pages.seo.store');
        });

    });
});

// Bare-section redirects: typing /admin/listing without a suffix used to 404.
// Every admin section prefix now redirects to the page its menu opens, so
// hand-typed URLs always land somewhere real.
Route::get('listing', fn () => redirect()->route('admin.listing.index'));
Route::get('category', fn () => redirect()->route('admin.category.index'));
Route::get('city', fn () => redirect()->route('admin.city.index'));
Route::get('country', fn () => redirect()->route('admin.country.index'));
Route::get('plan', fn () => redirect()->route('admin.plan.index'));
Route::get('review', fn () => redirect()->route('admin.review.index'));
Route::get('withdraw', fn () => redirect()->route('admin.withdraw.data.all'));
Route::get('deposit', fn () => redirect()->route('admin.deposit.pending'));
Route::get('kyc', fn () => redirect()->route('admin.kyc.pending'));
Route::get('report', fn () => redirect()->route('admin.report.login.history'));

