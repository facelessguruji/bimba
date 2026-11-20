<?php

use App\Constants\Status;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Language;
use App\Models\UserRanking;
use App\Notify\Notify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

function systemDetails()
{
    $system['name']          = 'LeadxGPT';
    $system['version']       = '2.0';
    $system['build_version'] = '2.0.0';

    return $system;
}

function slug($string)
{
    return Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1).'9';
    return random_int($min,$max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false) {
    $template = activeTemplateName();
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName() {
    $template = session('template') ?? gs('active_template');
    // whitelist: a garbage active_template (edited directly in the DB) must
    // not take down every Page lookup, section include and asset path —
    // fall back to the only shipped template
    if (!is_string($template) || $template === '' || !is_dir(resource_path('views/templates/' . $template))) {
        $template = 'basic';
    }
    return $template;
}

function siteLogo($type = null) {
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name, cacheBust: true);
}

function siteLogoDark($type = null) {
    $name = $type ? "/logo_$type.png" : '/logo_dark.png';
    return getImage(getFilePath('logoIcon') . $name, cacheBust: true);
}

function siteFavicon() {
    return getImage(getFilePath('logoIcon'). '/favicon.png', cacheBust: true);
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        // random_int: transaction references must not be predictable (rand()
        // is seedable and its state is observable across requests)
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false, $currencyFormat = true)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym').$printAmount.' '.__(gs('cur_text'));
        }elseif(gs('currency_format') == Status::CUR_TEXT){
            return $printAmount.' '.__(gs('cur_text'));
        }else{
            return gs('cur_sym').$printAmount;
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    // Independent build: templates are local only, no remote feed.
    return json_encode([]);
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


// Member-supplied URLs are only embedded as iframes from known, legitimate
// embed hosts - the `url` validation rule alone would let a listing frame any
// site (phishing overlay inside a trusted page). Non-matching URLs degrade to
// a plain link in the view.
function isEmbeddableVideo($url)
{
    if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    return in_array($host, [
        'www.youtube.com', 'youtube.com', 'youtu.be',
        'www.youtube-nocookie.com', 'youtube-nocookie.com',
        'player.vimeo.com', 'vimeo.com',
    ], true);
}

function isEmbeddableMap($url)
{
    if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    $path = (string) parse_url($url, PHP_URL_PATH);
    // maps.google.* embeds and www.google.*/maps embed links
    if (preg_match('/^maps\.google\.[a-z.]+$/', $host)) {
        return true;
    }
    if (preg_match('/^(www\.)?google\.[a-z.]+$/', $host) && str_starts_with($path, '/maps')) {
        return true;
    }
    return false;
}

function getImage($image, $size = null, $avatar = false, $cacheBust = false)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        // uploaded logos/icons keep the same filename after an admin upload —
        // append the file mtime so browsers fetch the new version instead of
        // serving a stale cached copy
        if ($cacheBust) {
            $clean = '?v=' . filemtime($image);
        }
        return asset($image) . $clean;
    }
    if ($avatar) {
        return asset('assets/images/avatar.png');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true,$pushImage = null)
{
    $globalShortCodes = [
        'site_name' => gs('site_name'),
        'site_currency' => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->pushImage = $pushImage;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null)
{
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data,$view = null)
{
    return $data->appends(request()->all())->links($view);
}


function menuActive($routeName, $type = null, $param = null) {
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if (!$param) return $class;

        $routeParam = array_values(isset(request()->route()->parameters)
            ? request()->route()->parameters : []);

        $firstParam = $routeParam[0] ?? null;
        if (is_string($firstParam) && is_string($param) && strcasecmp($firstParam, $param) === 0) {
            return $class;
        }
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null,$filename = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    if(!$lang){
        $lang = getDefaultLang();
    }
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    if (!$date) {
        return '-';
    }
    $lang = session()->get('lang');
    if(!$lang){
        $lang = getDefaultLang();
    }
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getDefaultLang(){
    return Language::where('is_default', Status::YES)->first()->code ?? 'en';
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false) {

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    // Header-based "deep detect" is only honored when the direct peer is a
    // configured trusted proxy (TRUSTED_PROXIES env, comma-separated IPs /
    // CIDRs). Otherwise any visitor could spoof X-Forwarded-For and rotate
    // fake IPs to evade login throttles and rate limits.
    $trustedProxies = array_filter(array_map('trim', explode(',', (string) config('app.trusted_proxies'))));
    if ($trustedProxies) {
        $isTrusted = false;
        foreach ($trustedProxies as $proxy) {
            if (\Symfony\Component\HttpFoundation\IpUtils::checkIp($ip, $proxy)) {
                $isTrusted = true;
                break;
            }
        }
        if ($isTrusted) {
            //Deep detect ip (first valid forwarded header wins)
            if (filter_var(isset($_SERVER['HTTP_FORWARDED']) ? $_SERVER['HTTP_FORWARDED'] : '', FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_FORWARDED'];
            }
            if (filter_var(isset($_SERVER['HTTP_FORWARDED_FOR']) ? $_SERVER['HTTP_FORWARDED_FOR'] : '', FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_FORWARDED_FOR'];
            }
            if (filter_var(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '', FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            if (filter_var(isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '', FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            if (filter_var(isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : '', FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            }
            if (filter_var(isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '', FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            }
        }
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        if (!$general) {
            // settings row missing (truncated/restoring): fall back to an
            // unsaved model instead of null, otherwise every blade view that
            // calls gs()->siteName() and every settings save would fatal.
            // The next save through any admin form re-inserts the row.
            $general = new GeneralSetting();
            $general->site_name = 'LeadxGPT';
            $general->paginate_number = 20;
            $general->base_color = '4634ff';
        }
        Cache::put('GeneralSetting', $general);
    }
    if ($key) return isset($general->$key) ? $general->$key : null;
    return $general;
}
function isImage($string){
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}


function convertToReadableSize($size) {
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int)$matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size.'GB';
    }

    if ($unit == 'M') {
        return $size.'MB';
    }

    if ($unit == 'K') {
        return $size.'KB';
    }

    return $size.$unit;
}


function frontendImage($sectionName, $image, $size = null,$seo = false, $thumb = false)
{
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    if ($thumb) {
        return getImage('assets/images/frontend/' . $sectionName . '/thumb_' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function averageReview($countRating, $sumRating) {
    return $countRating == 0 ? 0 : $sumRating / $countRating;
}

function showRatingStars($averageRating) {
    $maxRating  = 5;
    $fullStars  = floor($averageRating);
    $halfStar   = ($averageRating - $fullStars) >= 0.5;
    $emptyStars = $maxRating - $fullStars - ($halfStar ? 1 : 0);

    $stars = '';

    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="las la-star"></i>';
    }

    // Half star
    if ($halfStar) {
        $stars .= '<i class="las la-star-half-alt"></i>';
    }

    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="lar la-star"></i>';
    }

    return $stars;
}

function isBusinessOpen($openTime, $closeTime, $daysOff) {
    $currentDay  = strtolower(date('l'));
    $currentTime = time();
    $daysOff     = (array) ($daysOff ?? []);

    if (in_array($currentDay, $daysOff)) {
        return false;
    }

    if (!$closeTime) {
        return true;
    }

    $openTimeSeconds  = strtotime($openTime);
    $closeTimeSeconds = strtotime($closeTime);

    if ($currentTime >= $openTimeSeconds && $currentTime <= $closeTimeSeconds) {
        return true;
    } else {
        return false;
    }
}



function buildResponse($remark, $status, $notify, $data = null)
{
    $response = [
        'remark' => $remark,
        'status' => $status,
    ];
    $message = [];
    if ($notify instanceof \Illuminate\Support\MessageBag) {
        $message['error']  = collect($notify)->map(function ($item) {
            return $item[0];
        })->values()->toArray();
    } else {
        $message = [$status => collect($notify)->map(function ($item) {
            if (is_string($item)) {
                return $item;
            }
            if (count($item) > 1) {
                return $item[1];
            }
            return $item[0];
        })->toArray()];
    }
    $response['message'] = $message;
    if ($data) {
        $response['data'] = $data;
    }
    return response()->json($response);
}

function responseSuccess($remark, $notify, $data = null)
{
    return buildResponse($remark, 'success', $notify, $data);
}

function responseError($remark, $notify, $data = null)
{
    return buildResponse($remark, 'error', $notify, $data);
}

/*
|--------------------------------------------------------------------------
| Affiliate helpers
|--------------------------------------------------------------------------
*/

// Decode a plan's level_com JSON into [level => percent]
function teamLevels($plan)
{
    $rates = [];
    if ($plan && $plan->level_com) {
        $decoded = json_decode($plan->level_com, true);
        if (is_array($decoded)) {
            foreach ($decoded as $level => $percent) {
                $rates[(int) $level] = (float) $percent;
            }
            ksort($rates);
        }
    }
    return $rates;
}

// How many commission levels the user's ACTIVE plan qualifies for (0 = free)
function planDepth($user)
{
    $activePlan = $user->activePlan ?? null;
    if (!$activePlan) {
        return 0;
    }
    return (int) ($activePlan->plan->commission_levels ?? 0);
}

// True when the user holds an active (unexpired) plan
function hasActivePlan($user)
{
    return $user && $user->activePlan()->exists();
}

// Current member rank (or null)
function memberRank($user)
{
    if (!$user || !$user->user_ranking_id) {
        return null;
    }
    return UserRanking::find($user->user_ranking_id);
}

// Verify a Google Authenticator code against the user's secret (or a
// candidate secret during 2FA enrollment)
function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new \App\Lib\GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode  = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}

/**
 * Decode a gateway's credential parameter object and verify it is usable.
 * Aborts with a clear message when the gateway was enabled without saving
 * credentials, instead of a 500 at checkout.
 */
function gatewayCredentials($gatewayParameter)
{
    $credentials = json_decode($gatewayParameter);

    if (!is_object($credentials) || !count((array) $credentials)) {
        Log::warning('GATEWAY CREDS ABORT reached for: ' . substr((string) $gatewayParameter, 0, 60));
        abort(404, 'This payment gateway is enabled but not configured yet. Please contact support.');
    }

    // the installer seed stores {key: {title, global, value}} while the admin
    // form stores {key: "flat value"} — normalize to flat key -> string so the
    // gateway controllers can read credentials the same way in both cases
    foreach ($credentials as $key => $val) {
        if (is_object($val) && isset($val->value) && !is_object($val->value)) {
            $credentials->{$key} = $val->value;
        }
    }

    return $credentials;
}

/**
 * Decode a JSON string and guarantee an object is returned.
 * Payment-gateway webhooks POST attacker-controlled bodies; a malformed
 * payload must degrade to null (the caller answers 4xx and logs) instead of
 * "attempt to read property on null" fataling the IPN endpoint.
 *
 * @param string|null $json
 * @return object|null
 */
function safeJsonDecode($json)
{
    if (!is_string($json) || $json === '') {
        return null;
    }

    $decoded = json_decode($json);

    return is_object($decoded) ? $decoded : null;
}


/**
 * Mask a contact number for public display: keep first and last character,
 * replace everything between with asterisks. 7760406490 -> 7*******0
 */
function maskContactNumber($number)
{
    $number = preg_replace('/[^0-9+]/', '', (string) $number);
    $len = strlen($number);

    if ($len <= 2) {
        return str_repeat('*', $len);
    }

    return $number[0] . str_repeat('*', max($len - 2, 1)) . substr($number, -1);
}

/**
 * Neutralize CSV formula injection: a cell starting with =, +, - or @ would
 * execute as a formula when the file opens in Excel.
 */
function csvSafe($value)
{
    return is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
}

/**
 * The owner's uniform ladder: tree depth / commission level N carries the
 * Nth ladder position name (Affiliate, Senior Affiliate, ...) from the
 * ranks table, so every screen speaks the same names. Falls back to
 * "Level N" past the configured ladder.
 */
function ladderLevelName($level)
{
    $name = \App\Models\UserRanking::where('level', (int) $level)->value('name');
    return $name ?: __('Level') . ' ' . $level;
}

/**
 * User-visible plan name: the admin may leave plan titles empty (packages
 * are presented by price alone on the pricing page), so surfaces that
 * must reference a plan get a price-based fallback instead of a blank.
 */
function planDisplayName($plan)
{
    $title = trim((string) ($plan->title ?? ''));
    if ($title !== '') {
        return $title;
    }
    $price = (float) ($plan->yearly_price ?? ($plan->plan_price ?? 0));
    if ($price <= 0) {
        return 'Free';
    }
    return '$' . number_format($price, 0) . ' Plan';
}
