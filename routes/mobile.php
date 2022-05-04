<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Mobile API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register mobile API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/**
 * Show current time to check if the mobile
 * routing is configured correctly
 */

Route::any('/', function () {

    return response()->json(['server_time' => \Carbon\Carbon::now()], 200, []);
});

Route::post('register', 'v1\AuthController@register');
Route::post('login', 'v1\AuthController@login');
Route::post('verifyotp', 'v1\MobileVerificationController@mobileVerification');

//All version 1 related routes come here.
Route::prefix('v1')->name('1.')->namespace('v1')->middleware('auth:mobile')->group(function () {

    Route::get('refresh', 'AuthController@refresh')->name('api.jwt.refresh');

    Route::get('alert', 'MobileAlertController@getAlert');

    Route::get('setting', 'AppSettingController@getAppSetting');

    Route::post('saveloction', 'UserFrequentController@frequent');

    Route::post('upload_json', 'UnderwritingUploadController@store');

    Route::post('update_user', 'UserController@update');

    Route::get('user_info', 'UserController@index');

    Route::get('aggregator_list', 'AggregatorController@index');

    Route::post('aggregator_cv_register', 'AggregatorController@registerAggregatorCv');

    Route::post('sms_data_to_cv', 'AggregatorController@SmsToCV');

    Route::post('sms_data_to_cv', 'AggregatorController@SmsToCV');

    Route::post('fill_webview_cv', 'AggregatorController@cvFillWebView');

    Route::post('cashe_lead', 'AggregatorController@casheLead');

    Route::any('/', function () {

        return response()->json(['server_time' => \Carbon\Carbon::now()], 200, []);
    });

    Route::post('verifyotp','VerifyOTPController@verifyOtp');
    Route::post('retryotp', 'VerifyOTPController@RetryOtp');

    Route::post('create-avail-finance-user','AvailFinanceController@registerAvailFinance');
    Route::get('get-avail-user', 'AvailFinanceController@getAvailFinanceUser');
    Route::post('update-avail-finance-user', 'AvailFinanceController@updateAvailFinance' );

});

