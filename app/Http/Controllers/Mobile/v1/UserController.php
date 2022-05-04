<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use App\Http\Resources\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;


class UserController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth:mobile');
    }


    /**
     * list user information
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            JWTAuth::parseToken()->toUser();


            return $this->sendResponse(['user'=> $request->user()], 'user fetch successful');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }

    /**
     * update user information
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->toUser();

            unset($request['mobile']);

            $input = $request->all();

            // insert user request data
            DB::collection('users')->where('_id', $user->id)->update($input);

            return $this->sendResponse(['user'=> $request->user()], 'update user successful');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }

}
