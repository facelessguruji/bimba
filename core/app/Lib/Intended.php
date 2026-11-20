<?php

namespace App\Lib;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class Intended {
    public static function identifyRoute(){
        if(session()->get('intended_validation_error')){
            return false;
        }
        $intendedUrls = config('intended_routes');
        $previousRouteName = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        // moving between the login and register pages must not lose a saved
        // intended URL (guest clicked View Contact on a listing, then switched)
        if (in_array($previousRouteName, ['user.login', 'user.register']) && session()->has('intended_info')) {
            return;
        }
        if (array_key_exists($previousRouteName, $intendedUrls)) {
            $previousUrl = url()->previous();
            $previousUrlParts = parse_url($previousUrl);
            $queryString = isset($previousUrlParts['query']) ? $previousUrlParts['query'] : '';
            parse_str($queryString, $queryParams);
            $redirectRouteName = $previousRouteName;
            $redirectRouteUrl = $previousUrl;
            try{
                if($intendedUrls[$previousRouteName]){
                    $redirectRouteName = $intendedUrls[$previousRouteName];
                    $redirectRouteUrl = route($redirectRouteName);
                }
            }catch(\Exception $error){
                throw new \Exception("Intended route [$redirectRouteName] not defined");
            }
            // url()->previous() can carry a crafted off-domain Referer header,
            // so only same-origin targets may become a post-auth redirect
            if (!self::isSameOriginUrl($redirectRouteUrl)) {
                $redirectRouteName = 'user.home';
                $redirectRouteUrl = route('user.home');
            }
            $data['route_name'] = $redirectRouteName;
            $data['route_full_url'] = $redirectRouteUrl;
            $data['query_params'] = $queryParams;
            $data['form_data'] = request()->all();
            self::assignSession($data);
        } else {
            session()->forget('intended_info');
        }
    }

    public static function assignSession($data){
        session()->put('intended_info', $data);
    }

    public static function reAssignSession(){
        $data = session()->get('intended_info');
        if($data){
            self::assignSession($data);
        }
        Session::flash('intended_validation_error', 1);
    }

    public static function getRedirection(){
        if (session()->has('intended_info')) {
            $url = session('intended_info');
            session()->forget('intended_info');
            $redirectUrl = $url['route_full_url'];
            if (!self::isSameOriginUrl($redirectUrl)) {
                $redirectUrl = route('user.home');
            }
            return redirect()->to($redirectUrl);
        }
        return false;
    }

    // accepts relative paths and same-origin absolute URLs only; blocks
    // off-domain hosts and non-http schemes (e.g. javascript:)
    public static function isSameOriginUrl($url){
        if (!is_string($url) || $url === '') {
            return false;
        }
        $urlParts = parse_url($url);
        if ($urlParts === false) {
            return false;
        }
        $scheme = strtolower($urlParts['scheme'] ?? '');
        if ($scheme !== '' && !in_array($scheme, ['http', 'https'], true)) {
            return false;
        }
        $host = $urlParts['host'] ?? '';
        return $host === '' || strcasecmp($host, request()->getHost()) === 0;
    }
}
