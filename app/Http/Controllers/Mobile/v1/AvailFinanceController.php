<?php


namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use App\Models\AggregatorLogs;
use App\Models\Aggregators;
use App\Models\AggregatorsRequest;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class AvailFinanceController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }


    /*
    * Api to register new user for  avail finance
    */
    public function  registerAvailFinance(Request $request){

        try {

            // putting some validation rules for file
            $validator = Validator::make($request->all(), [
                'env' => 'required',
            ]);

            if ($validator->fails()) {

                return $this->sendError('Validation Error.', $validator->errors());

            }

            $aggregators = Aggregators::where('name', 'Avail Finance')->first();


            $output = availFinanceRegisterValidate($request);


            $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->toUser();

            $response = json_decode($output->getBody());

            AggregatorLogs::create([
                'user_id'=>$user->_id,
                'aggregator_id'=>$aggregators->_id,
                'input' =>$request->all(),
                'response' =>$response,
                'status_code' => $output->getStatusCode()
            ]);

            $data = array( 'user_id'=>$user->_id,
                'aggregator_id'=>$aggregators->_id,
                'avail_user_id'=>$response->user_id,
                'message' =>'',
                'status' => $output->getStatusCode());

            DB::collection('aggregators_requests')->where(['user_id'=>$user->_id,'aggregator_id'=>$aggregators->_id])->update($data , array('upsert' => true));


            return $this->sendResponse($response, 'Successfully get response.');

        } catch(\Exception $e)  {

            return $this->sendResponse([], $e->getMessage());
        }
    }

    //Get the avail finance user id using user id

    public function  getAvailFinanceUser(Request $request){

        try{
                $user_id = $request->user_id;

                $response = AggregatorLogs::where('user_id',$user_id)->get();


                return $this->sendResponse($response, 'Successfully get response.');

        }catch(\Exception $e)  {

                return $this->sendResponse([], $e->getMessage());
        }
    }

    //Update the user information of avail finance
    /*
    * Api to register new user for  avail finance
    */
    public function  updateAvailFinance(Request $request){

        try {

            // putting some validation rules for file
            $validator = Validator::make($request->all(), [
                'env' => 'required',
            ]);

            if ($validator->fails()) {

                return $this->sendError('Validation Error.', $validator->errors());

            }

            $aggregators = Aggregators::where('name', 'Avail Finance')->first();


            $output = availFinanceUpdate($request);


            $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->toUser();

            $response = json_decode($output->getBody());

            AggregatorLogs::create([
                'user_id'=>$user->_id,
                'aggregator_id'=>$aggregators->_id,
                'input' =>$request->all(),
                'response' =>$response,
                'status_code' => $output->getStatusCode()
            ]);

            $data = array( 'user_id'=>$user->_id,
                'aggregator_id'=>$aggregators->_id,
                'avail_user_id'=>$response->user_id,
                'message' =>'',
                'status' => $output->getStatusCode());

            DB::collection('aggregators_requests')->where(['user_id'=>$user->_id,'aggregator_id'=>$aggregators->_id])->update($data , array('upsert' => true));


            return $this->sendResponse($response, 'Successfully get response.');

        } catch(\Exception $e)  {

            return $this->sendResponse([], $e->getMessage());
        }
    }
}
