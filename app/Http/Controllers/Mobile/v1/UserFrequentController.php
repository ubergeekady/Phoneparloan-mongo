<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use App\Http\Requests\JsonBodyRequest;
use App\Models\User;
use App\Models\UserFrequent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class UserFrequentController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }


    /**
     * save user lat long
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function frequent(Request $request)
    {
        try {

            // putting some validation rules for file
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            // validate user from database
            $user = JWTAuth::parseToken()->toUser();
            $user->user_fcm_token = $request->get('user_fcm_token');
            $user->app_version = $request->get('app_version');
            $user->save();

            if ($validator->fails()) {

                return $this->sendError('Validation Error.', $validator->errors());

            }

            $input = $request->all();
            $input['user_id'] = $user->id;
            // insert user request data
            UserFrequent::create($input);

            return $this->sendResponse([], 'latitude and longitude save succcessfully');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }

}
