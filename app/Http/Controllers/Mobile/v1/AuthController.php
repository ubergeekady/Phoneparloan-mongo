<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends BaseController
{
    public function register(Request $request)
    {


        // putting some validation rules for User input
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|regex:/[0-9]{10}/|digits:10|unique:users',
            'password' => 'required|digits:4',
        ]);

        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' =>  Hash::make($request->password),
        ]);

        $token = auth('mobile')->login($user);

        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['mobile', 'password']);

        if (!$token = auth('mobile')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function refresh() {
        return $this->respondWithToken(Auth::guard('mobile')->refresh());
    }

    protected function respondWithToken($token)
    {
        $id = \user('mobile')->id;

        return response()->json([
            'user_id' => $id,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('mobile')->factory()->getTTL() * 60
        ]);
    }

}
