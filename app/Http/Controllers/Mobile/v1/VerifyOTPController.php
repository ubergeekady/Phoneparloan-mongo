<?php

namespace App\Http\Controllers\Mobile\v1;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\v1\Base\BaseController as BaseController;
use Validator;
use App\Models\User;
use App\Utils\Helpers;

class VerifyOTPController extends BaseController
{


    public function __construct()
    {
        $this->middleware('auth:mobile');
    }
    /**
     * Verify otp from user input on basis of mobile number
     * use msg91 for otp service
     * and otp log table
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function verifyOtp(Request $request)
    {
        try {



            // putting some validation rules for User input
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|regex:/[0-9]{10}/|digits:10'
            ]);

            $user_id = '';

            if($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            //validating users by mobile number to get user id
            $user = User::where('mobile', $request->get('mobile'))->first();

            // get user id from user
            if ($user) {

                $user_id = $user->_id;
            }


            // add country code with mobile
            $mobile = '+91' . $request->get('mobile');

            //send using msg91
            $phone = substr($mobile, 1);

            $four_digit_random_number = mt_rand(1000, 9999);

            $message = sendSMSmessageotp($full_name = 'Guest', $four_digit_random_number);
            sendmessage($phone, $message, $four_digit_random_number);
            $input = $request->all();
            $input['password'] =  \Illuminate\Support\Facades\Hash::make($four_digit_random_number);

            // insert user request data or update
            $mobile = $request->get('mobile');
            $newuserid = User::where('mobile',$mobile)->update(
                $input
            );


            return $this->sendResponse(['id' => $user_id, 'otp' => $four_digit_random_number, 'demo' => env('Demo_OTP')], 'successfully access otp');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }

    /**
     * Retry otp from user input on basis of mobile number
     * use msg91 for Retry otp service
     * and otp log table
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function RetryOtp(Request $request)
    {
        try {

            // putting some validation rules for User input
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|regex:/[0-9]{10}/|digits:10'
            ]);

            $user_id = '';

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            //validating users by mobile number to get user id
            $user = User::where('mobile', $request->get('mobile'))->first();

            // add country code with mobile
            $mobile = '+91' . $request->get('mobile');

            //send using msg91
            $phone = substr($mobile, 1);
            sendRetrymessage($phone);
            $input = $request->all();
            $previous_otp = User::where('mobile', $request->get('mobile'))->first();

            $four_digit_random_number = $previous_otp->password;
            $input['password'] = $four_digit_random_number;


            return $this->sendResponse(['id' => $previous_otp->_id,  'demo' => env('Demo_OTP')], 'successfully access otp');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }
}
