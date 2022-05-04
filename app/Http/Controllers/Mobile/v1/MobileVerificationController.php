<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use App\Models\OtpLog;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class MobileVerificationController extends BaseController
{

    /**
     * Verify otp from user input on basis of mobile number
     * use msg91 for otp service
     * and otp log table
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function mobileVerification(Request $request)
    {
        try {

            // putting some validation rules for User input
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|regex:/[0-9]{10}/|digits:10'
            ]);

            if($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            //validating users by mobile number to get user id
            $user = User::where('mobile', $request->get('mobile'))->first();
            $token='';
            $four_digit_random_number = mt_rand(1000, 9999);

            if($user){

                $token = JWTAuth::fromUser($user);
                $user->password = \Illuminate\Support\Facades\Hash::make($four_digit_random_number);
                $user->save();
            }

            // add country code with mobile
            $mobile = '+91' . $request->get('mobile');

            //send using msg91
            $phone = substr($mobile, 1);

            $message = sendSMSmessageotp($full_name = 'Guest', $four_digit_random_number);
            sendmessage($phone, $message, $four_digit_random_number);
            $input = $request->all();
            $input['otp'] = $four_digit_random_number;

            $otp_log = OtpLog::where('mobile', $request->get('mobile'))->first();

            if($otp_log){

                $otp_log->otp = $four_digit_random_number;
                $otp_log->save();

            }else{

                OtpLog::create($input);

            }

            return $this->sendResponse(['token' => $token, 'otp' => $four_digit_random_number, 'demo' => env('Demo_OTP')], 'successfully access otp');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }
}
