<?php

use Carbon\Carbon;
use App\Models\Action;

function user($guard = null)
{
    return auth($guard)->user();
}

function loggedIn($guard = null)
{
    return auth($guard)->check();
}

function apiuser($id)
{
    return \App\Models\User::find($id);
}

function formatNumber($input)
{
    $suffixes = array('', 'k', 'm', 'g', 't');
    $suffixIndex = 0;

    while (abs($input) >= 1000 && $suffixIndex < sizeof($suffixes)) {
        $suffixIndex++;
        $input /= 1000;
    }

    return (
        $input > 0
            // precision of 3 decimal places
            ? floor($input * 1000) / 1000
            : ceil($input * 1000) / 1000
        )
        . $suffixes[$suffixIndex];
}

function randomDates()
{
    return Carbon::now()->sub('days', mt_rand(1, 45))->add('minutes', mt_rand(1, 59));
}

function randomImage($width = 420, $height = 250)
{
    return "https://picsum.photos/id/" . mt_rand(99, 110) . "/{$width}/{$height}";
}

/**
 * random true false to conditionally performe some action
 * @return boolean
 */
function shouldDo()
{
    $state = [true, false];
    return $state[mt_rand(0, count($state) - 1)];
}

/**
 * returns a passed value or default value if passed value is null
 * @param  int|string|bool $val
 * @return int|string|bool
 */
function unless($value, $default = null)
{
    return $value != null ? $value : $default;
}

function uuid()
{
    return \Str::uuid();
}

//otp sms helper
function sendSMSmessageotp($name, $otp)
{
    //$message = "Hi " . $name . "%0aThank you for showing interest in PhoneParLoan. Your OTP is%0a" . $otp;
    $message = "<#>" . "Hi " . $name . "%0aThank you for showing interest in PhoneParLoan. Your OTP :%0a" . $otp . "\n" . "I26/IZa2Kuu";
    return $message;
}

function sendmessage($mobile, $message, $otp = '')
{

    $message = urlencode($message);
    $url = sprintf("http://control.msg91.com/api/sendotp.php?authkey=%s&mobile=%s&message=%s&sender=%s&otp=%s",
        env('MSG91_AUTH_KEY'), $mobile, $message, env('MSG91_SENDER_ID'), $otp);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

/**
 * register user on cv
 */
function cvregisterValidate($request)
{
    $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->toUser();
    $API_KEY='';
    $url='';
    if ($request->env == 'uat')
    {
        $API_KEY = env('CV_UAT_API_KEY');
        $url = env('CV_UAT_API_URL');
    }
    if ($request->env == 'prod')
    {

        $API_KEY = env('CV_PROD_API_KEY');
        $url = env('CV_PROD_API_URL');
    }

    $header = [
        'content-type' => 'application/json',
        'apikey' => $API_KEY
    ];

    $input['userId'] = $user->_id;
    $input['mobileNo'] = $user->mobile;
    $requestContent = [
        'headers' => $header,
        'json' => $input
    ];

    $client = new \GuzzleHttp\Client();

    $apiRequest = $client->request('POST', $url, $requestContent);

    return $apiRequest;
}


 function getCasheCheckSum($options = []){

         $data = $options['data'];
         $key = $options['key'];

         $hmac = hash_hmac("sha1", $data, $key, TRUE);
         $signature = base64_encode($hmac);
         return $signature;
     }

function random_strings($length_of_string)
{

    // sha1 the timstamps and returns substring
    // of specified length
    return substr(sha1(time()), 0, $length_of_string);
}
/**
 * Register the user to aavail Finance
 * @return mixed|\Psr\Http\Message\ResponseInterface
 * @throws \GuzzleHttp\Exception\GuzzleException
 *
 */

function availFinanceRegisterValidate($request)
{
    $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->toUser();
    $API_KEY = '';
    $url = '';
    if ($request->env == 'uat') {
        $API_KEY = env('AVAIL_FINANCE_UAT_API_KEY');
        $token = env('AVAIL_FINANCE_UAT_AUTH_TOKEN');
        $url = env('AVAIL_FINANCE_UAT_API_USER_URL');
    }

    if ($request->env == 'prod') {

        $API_KEY = env('AVAIL_FINANCE_PROD_API_KEY');
        $token = env('AVAIL_FINANCE_PROD_AUTH_TOKEN');
        $url = env('AVAIL_FINANCE_PROD_API_USER_URL');
    }

    $header = [
        'content-type' => 'application/json',
        'authKey' => $API_KEY,
        'authToken' => $token
    ];


    $unique_reference_no = random_strings(30);

    //$input['userId'] = $user->_id;
    $input['mobile_number'] = $request->mobile_number;
    $input['name'] = $request->name;
    $input['email'] = $request->email;
    $input['unique_reference_no'] = $unique_reference_no;
    //$inout = json_encode($input, true);
    $requestContent = [
        'headers' => $header,
        'json' => $input
    ];

    $client = new \GuzzleHttp\Client();

    $apiRequest = $client->request('POST', $url, $requestContent);

    return $apiRequest;
}



/**
 * Register the user to aavail Finance
 * @return mixed|\Psr\Http\Message\ResponseInterface
 * @throws \GuzzleHttp\Exception\GuzzleException
 *
 */

function availFinanceUpdate($request)
{
    $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->toUser();
    $API_KEY = '';
    $url = '';
    if ($request->env == 'uat') {
        $API_KEY = env('AVAIL_FINANCE_UAT_API_KEY');
        $token = env('AVAIL_FINANCE_UAT_AUTH_TOKEN');
        $url = env('AVAIL_FINANCE_UAT_UPDATE_USER_URL');
    }

    if ($request->env == 'prod') {

        $API_KEY = env('AVAIL_FINANCE_PROD_API_KEY');
        $token = env('AVAIL_FINANCE_PROD_AUTH_TOKEN');
        $url = env('AVAIL_FINANCE_PROD_UPDATE_USER_URL');
    }

    $header = [
        'content-type' => 'application/json',
        'authKey' => $API_KEY,
        'authToken' => $token
    ];


    $unique_reference_no = random_strings(30);
    $input=[
                'unique_reference_no'=>$unique_reference_no,
                'user_id'=> $request->user_id,
                'gender'=>$request->gender,
                'name'=>$request->name,
                'email'=>$request->email,
                'pan_number'=>$request->pan_number,
                'dob'=>$request->dob,
                'salary_mode'=>$request->salary_mode,
                'current_address_pincode'=>$request->current_address_pincode,
                "current_employer_name"=>$request->current_employer_name,
                "current_address_state"=> $request->current_address_state,
                "current_address_street"=>$request->current_address_street,
                "current_address_flat_no"=> $request->current_address_flat_no,
                "house_ownership"=> $request->house_ownership
            ];



    $requestContent = [
        'headers' => $header,
        'json' => $input
    ];

    $client = new \GuzzleHttp\Client();

    $apiRequest = $client->request('POST', $url, $requestContent);

    return $apiRequest;
}






