<?php

use Carbon\Carbon;
use App\Lib\Captcha;
use App\Notify\Notify;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Frontend;
use App\Models\Extension;
use Illuminate\Support\Str;
use App\Models\ModuleSetting;
use App\Models\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Models\ChargeLog;
use App\Models\Currency;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;

function systemDetails()
{
    $system['name'] = 'xcash';
    $system['version'] = '2.1';
    $system['build_version'] = '4.3.9';
    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
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


function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
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
    $analytics = Extension::where('act', $key)->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
} 

function showAmount($amount, $currency = null, $separate = true, $exceptZeros = false, $checkNegative = false)
{
    if ($currency && $currency->currency_type != 1) {
        $decimal = 8;
    } else {
        $decimal = 2;
    }

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

    if($checkNegative){ 

        $symbol = gs()->cur_sym;

        if($currency){
            $symbol = $currency->currency_symbol;
        }

        $printAmount = $symbol.str_replace('-', '', $printAmount);

        if($amount < 0){
            $printAmount = '- '.$printAmount;
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
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
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
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
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


function getImage($image, $size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{    
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
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
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}

 
function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) { 
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
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
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
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


function gatewayRedirectUrl($type = false){
    if ($type) {
        return strtolower(userGuard()['type']).'.deposit.history';
    }else{
        return strtolower(userGuard()['type']).'.deposit';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
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
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
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

function gs()
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    return $general;
}

function merchant()
{
    return auth()->guard('merchant')->user();
}

function agent()
{
    return auth()->guard('agent')->user();
}

function module($key, $module = null)
{
    if (!$module) {
        $module = ModuleSetting::query();
    }

    return $module->where('user_type', userGuard()['type'])->where('slug', $key)->first();
}

function userGuard()
{
    if (auth()->check()) {
        $guard = 1;
        $userType = 'USER';
        $user = auth()->user();
    } elseif (auth()->guard('agent')->check()) {
        $guard = 2;
        $userType = 'AGENT';
        $user = auth()->guard('agent')->user();
    } elseif (auth()->guard('merchant')->check()) {
        $guard = 3;
        $userType = 'MERCHANT';
        $user = auth()->guard('merchant')->user();
    }

    return [
        'user' => @$user,
        'type' => @$userType,
        'guard' => @$guard
    ];
}

function keyGenerator($length = 50)
{
    $characters = 'abcdefghijklmnpqrstuvwxyz0123456789';
    $string = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    return $string;
}

function gatewayView($alias = null, $manual = false)
{
    if ($manual) {
        return 'manual_payment.' . $alias;
    }
    return 'payment.' . $alias;
}

function defaultCurrency()
{
    return Currency::where('is_default', 1)->first()->currency_code ?? 'USD';
}

function createWallet($currency, $user = null){

    $wallet = new Wallet();
    $wallet->user_id = $user->id;
    $wallet->user_type = strtoupper(substr($user->getTable(), 0, -1));
    $wallet->balance = 0;
    $wallet->currency_id = $currency->id;
    $wallet->currency_code = $currency->currency_code;
    $wallet->save();

    return $wallet;
}

function getCurrency($code)
{
    return Currency::where('currency_code', $code)->first();
}

function currencyConverter($amount, $rate)
{
    return $amount / $rate;
}

function toBaseCurrency($amount, $rate)
{
    return $amount * $rate;
}

function currencyRate()
{
    return Currency::where('is_default', 1)->first()->rate ?? 0.00;
}

function chargeCalculator($amount, $percent, $fixed)
{
    $percentCharge = $amount * $percent / 100;
    return $fixed + $percentCharge;
}

function getVoucher()
{
    $numbers = getNumber(16);
    $muNumbers = '';
    for ($i = 0; $i < 16; $i++) {
        if (($i != 0) && ($i % 4 == 0)) {
            $muNumbers .= '-';
        }
        $muNumbers .= $numbers[$i];
    }

    return $muNumbers;
}

function queryBuild($key, $value)
{
    $queries = request()->query();
    if (count($queries) > 0) {
        $delimeter = '&';
    } else {
        $delimeter = '?';
    }
    if (request()->has($key)) {
        $url = request()->getRequestUri();
        $pattern = "\?$key";
        $match = preg_match("/$pattern/", $url);
        if ($match != 0) {
            return  preg_replace('~(\?|&)' . $key . '[^&]*~', "\?$key=$value", $url);
        }
        $filteredURL = preg_replace('~(\?|&)' . $key . '[^&]*~', '', $url);
        return  $filteredURL . $delimeter . "$key=$value";
    }
    return  request()->getRequestUri() . $delimeter . "$key=$value";
}

function totalProfit($date = null){   
    $profits = ChargeLog::where('remark', '!=','add_money')->where('remark', '!=','withdraw')->with(['currency','user','agent','merchant'])->when($date, function($q,$data){
        $q->whereDate('created_at','>=',Carbon::parse($data['start']))->whereDate('created_at','<=',Carbon::parse($data['end']));
    })->get();

    if(!$profits->isEmpty()){
        foreach($profits as $profit){
            $totalProfit[] = $profit->amount * $profit->currency->rate;
        }
        return array_sum($totalProfit);
    }
    return 0.00;
}

function logoutAnother($currentUser){ 
    $user = ['user', 'agent', 'merchant'];

    foreach($user as $name){
        if($name != $currentUser){
            if($name == 'user'){
                auth()->logout();
            }else{
                auth()->guard($name)->logout(); 
            }
        }
    }
}

function getSleepImage(){
    $images = glob('assets/images/frontend/sleep/*');
    $image = $images[rand(0, count(@$images ?? []) - 1)];

    if($image){
        $image = asset($image);
    }else{
        $image = asset('assets/images/frontend/sleep/default.jpg');
    }

    return $image;
}

function otpType($validation = false){
   
    $otpType = [];  
    $general = gs();
    $user = userGuard()['user'];
    $validationRule = 'nullable'; 
    
    if($general->otp_verification && ($general->en || $general->sn || $user->ts)){        
        if($general->en){
            $otpType[] = 'email';
        }
        if($general->sn){
            $otpType[] = 'sms';
        }
        if($user->ts){
            $otpType[] = '2fa';
        }
    }
  
    if($validation){ 
        if(count($otpType)){
            $validationRule = 'required|in:'.implode(',', $otpType);
        }
        return $validationRule;
    }

    return $otpType;
}

function callApiMethod($routeName, $actionId){
    $action = \Route::getRoutes()->getByName($routeName)->getActionName();
    $data = explode('@', $action); 
    $controller = New $data[0]; 
    $method = $data[1];
    return $controller->$method($actionId);
}
function random_number($count,$begin='')
{
	$v = rand(1,9);	
	$count = $count - strlen($begin)-1; 
	if($count<=0) return "FAILED !";
	
	for($i=1;$i<=$count;$i++)
	{
		$v .= rand(0,9);
	}
	return $begin.$v;
	
}
function random_text($count,$begin='')
{
	$v = rand(1,3);	
	switch($v)
	{
		case 1: $V = chr(rand(48,57));break;
		case 2: $V = chr(rand(97,122));break;
		case 3: $V = chr(rand(65,90));break;
	}
	$count = $count - strlen($begin)-1; 
	
	for($i=1;$i<=$count;$i++)
	{
		$v = rand(1,3);	
		switch($v)
		{
			case 1: $V .= chr(rand(48,57));break;
			case 2: $V .= chr(rand(97,122));break;
			case 3: $V .= chr(rand(65,90));break;
		}
	}
	return  $V;
}