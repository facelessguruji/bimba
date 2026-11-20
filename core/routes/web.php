<?php

use Illuminate\Support\Facades\Route;

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store')->middleware('throttle:ticket');
    Route::get('view/{ticket}', 'viewTicket')->name('view')->middleware('throttle:ticket');
    Route::post('reply/{id}', 'replyTicket')->name('reply')->middleware('throttle:ticket');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::controller('FrontController')->group(function () {
    Route::get('events', 'events')->name('events');
    Route::get('event/{slug}/{id}', 'eventDetails')->name('event.details');
    Route::post('event/{id}/register', 'eventRegister')->name('event.register');
    Route::post('event/{id}/participate', 'eventParticipate')->name('event.participate');
    Route::get('event/{id}/attendees/export', 'eventAttendeesExport')->name('event.attendees.export');
    Route::get('pricing', 'pricingPlan')->name('pricing');
    Route::get('listings', 'listings')->name('listings');
    Route::get('listing/{slug}/{id}', 'listingDetail')->name('listing.details');
    Route::get('categories', 'categories')->name('categories');
    Route::get('cities', 'cities')->name('cities');
});

//Cron endpoint
Route::get('/sitemap.xml', 'SiteController@sitemap')->name('sitemap');

Route::get('cron', 'CronController@cron')->name('cron');

//Lead capture
Route::controller('LeadController')->group(function () {
    Route::post('lead/submit', 'submit')->middleware('throttle:contact')->name('lead.submit');
    Route::post('lead/listing/{id}', 'submitForListing')->middleware('throttle:contact')->name('lead.listing');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit')->middleware('throttle:contact');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog', 'blogs')->name('blog');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('/faq', 'faq')->name('faq');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
