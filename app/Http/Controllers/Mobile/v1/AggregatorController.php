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

class AggregatorController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * list aggregators information
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
               $aggregators = Aggregators::all();

            return $this->sendResponse(['aggregators'=> $aggregators], 'aggregators fetch successful');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }

    /*
    * Api wrapper for credit vidya registeration
    */
    public function  registerAggregatorCv(Request $request){

        try {

            // putting some validation rules for file
            $validator = Validator::make($request->all(), [
                'env' => 'required',
            ]);

            if ($validator->fails()) {

                return $this->sendError('Validation Error.', $validator->errors());

            }

            $aggregators = Aggregators::where('name', 'Credit Vidya')->first();
            $output = cvregisterValidate($request);
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
                'message' =>'',
                'status' => $output->getStatusCode());

            DB::collection('aggregators_requests')->where(['user_id'=>$user->_id,'aggregator_id'=>$aggregators->_id])->update($data , array('upsert' => true));


            return $this->sendResponse($response, 'Successfully get response.');

        } catch(\Exception $e)  {

            return $this->sendResponse([], $e->getMessage());
        }
    }

    public function SmsToCV(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'data' => 'required',
                'env' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $environment = $request->env;

            $CV_UAT_API_KEY = env('CV_UAT_API_KEY');
            $CV_PROD_API_KEY = env('CV_PROD_API_KEY');

            if ($environment == 'uat') {
                $API_KEY = $CV_UAT_API_KEY;
                $endpoint = 'https://api-uat.creditvidya.com/mw/api/post/data';
            } else {
                $API_KEY = $CV_PROD_API_KEY;
                $endpoint = 'https://api.creditvidya.com/mw/api/post/data';
            }

            $header = [
                'Content-Type' => 'application/json',
                'apikey' => $API_KEY
            ];
            $requestData = json_decode($request->data, true);

            $requestContent = [
                'headers' => $header,
                'json' => $requestData
            ];

            $client = new Client();

            $apiRequest = $client->request('POST', $endpoint, $requestContent);

            $response = json_decode($apiRequest->getBody());

            return $this->sendResponse($response, 'Successfully get response.');


        } catch (\Exception $e) {

            return $this->sendResponse([], $e->getMessage());

        }

    }


    /**
     * Fill web view of credit vidya
     * from our api end points
     */

    public function cvFillWebView( Request $request ){

        try
        {
            $validator = Validator::make($request->all(),[
                'data' => 'required',
                'env' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $environment = $request->env;
            $CV_UAT_API_KEY = env('CV_UAT_API_KEY');
            $CV_PROD_API_KEY = env('CV_PROD_API_KEY');

            if ($environment == 'uat') {
                $API_KEY = $CV_UAT_API_KEY;
                $endpoint = 'https://phoenix-uat.creditvidya.com/marketplace/loans/mw/application-details';
            } else {
                $API_KEY = $CV_PROD_API_KEY;
                $endpoint = 'https://api.creditvidya.com/marketplace/loans/mw/application-details';
            }

            $header = [
                'Content-Type' => 'application/json',
                'apikey' => $API_KEY
            ];

            $requestData = json_decode($request->data, true);

            $requestContent = [
                'headers' =>$header,
                'json' => $requestData
            ];

            $client = new Client();

            $apiRequest = $client->request('POST', $endpoint, $requestContent);

            $response = json_decode($apiRequest->getBody());
            $aggregator_status = $apiRequest->getStatusCode();
            // update aggregator lead status behalf of CV instant decision
            if(isset($response->status)){

                if(isset($response->errors)){
                    $errors = $response->errors;
                    $responseErrorCode =  $errors[0]->errorCode;
                    $errorCode = Aggregators::$cvErrorCode;
                    $aggregator_status = @$errorCode[$responseErrorCode];
                }

                if($response->status == 'success'){

                    $aggregator_status =   'success';
                }
            }

            $aggregators = Aggregators::where('name', 'Credit Vidya')->first();
            $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->toUser();

            $data = array( 'user_id'=>$user->_id,
                'aggregator_id'=>$aggregators->_id,
                'message' =>'',
                'status' => $aggregator_status);

            DB::collection('aggregators_requests')->where(['user_id'=>$user->_id,'aggregator_id'=>$aggregators->_id])->update($data);


            return $this->sendResponse($response, 'Data pushed successfully');

        }catch (\Exception $e) {

            return $this->sendResponse([], $e->getMessage());

        }
    }


    public function casheLead( Request $request ){

        try
        {
            $validator = Validator::make($request->all(),[
                'env' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $environment = $request->env;

            if ($environment == 'uat') {
                $API_KEY = BaseController::$url['UT_CSHE_KEY'];
                $endpoint = BaseController::$url['UT_CREAT_LEAD'];
            } else {
                $API_KEY = BaseController::$url['UT_CSHE_KEY'];
                $endpoint = BaseController::$url['UT_CREAT_LEAD'];
            }

            $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->toUser();

            $sample_data = array (
                'partner_name' => 'PhoneParLoan_Partner',
                'reference_Id' => '123',
                'applicant_id' => '234',
                'loan_amount' => '5000',
                'product_type_name' => 'CASHe_30',
                'Personal Information' =>
                    array (
                        'First Name' => 'ashwini',
                        'Last Name' => 'mahajan',
                        'DOB' => '14-02-1989',
                        'Gender' => 'Female',
                        'Address Line 1' => 'vasai',
                        'Address Line 2' => 'ambadi',
                        'Landmark (Address Line 3)' => 'near hdfc',
                        'Pincode' => '401202',
                        'City' => 'Mumbai',
                        'State' => 'Maharashtra',
                        'Type of Accommodation' => 'Own',
                        'PAN' => 'CBEPM2486L',
                        'Aadhaar' => '192839138392',
                        'Highest Qualification' => 'Postgraduate',
                    ),
                'Applicant Information' =>
                    array (
                        'Company Name' => '3M ELECTRO & COMMUNICATION PRIVATE LIMITED',
                        'Office Phone no' => '04023456789',
                        'Designation' => 'Supervisor',
                        'Monthly Income' => '80000',
                        'Number of Years in Current Work' => '10',
                        'Official Email' => 'user@company.com',
                        'Office Address 1' => 'sakinaka',
                        'Office Address 2' => 'andheri',
                        'Landmark(Office)' => 'near sbi',
                        'Office Pincode' => '410256',
                        'Office City' => ' Adilabad ',
                        'Office State' => ' TELANGANA',
                        'Working Since' => '29-07-2019',
                        'Employment Type' => 'Salaried',
                        'Salary ReceivedTypeId' => '1',
                    ),
                'Financial Information' =>
                    array (
                        'Primary Existing Bank Name' => 'HDFC Bank',
                        'Account number' => '23982983239832',
                        'IFSC Code' => 'KKBK0000963',
                    ),
                'Contact Information' =>
                    array (
                        'Mobile' => '8456120125',
                        'Email Id' => 'a.mahajna1994@yahoo.com',
                    ),
                'e-KYC Customer' =>
                    array (
                        'poa' =>
                            array (
                                'co' => 'S/O: Subir Samanta',
                                'street' => 'Middle Campus',
                                'house' => 'C-2/22',
                                'lm' => 'BIT',
                                'vtc' => 'Mesra',
                                'subdist' => 'Kanke',
                                'dist' => 'Ranchi',
                                'state' => 'Jharkhand',
                                'pc' => '835215',
                                'po' => 'Mesra',
                            ),
                        'aadhar_no' => '589023993834',
                        'name' => 'Anupam Samanta',
                        'dob' => '19-12-1991',
                        'gender' => 'M',
                        'compressed-address' => 'S/O: Subir Samanta, C-2/22, Middle Campus, BIT, Mesra, Kanke, Ranchi, Jharkhand - 835215, post-office: Mesra',
                    ),
            );

            $nameArr = explode(' ', @$user->name);


            $address = @$user->address;

            $address1 = $address;
            $address2 = $address;
            $address3 = $address;

            if ($address) {

                $addressArr = explode(',', $address);

                if (count($addressArr) >= 2) {

                    $address1 = @$addressArr[0];
                    $address2 = @$addressArr[1];
                    $address3 = @$addressArr[2];
                } else {
                    $address1 = $address;
                    $address2 = $address;
                    $address3 = $address;
                }

            }

        $modeOfSalaryArr = ['cash' => 1, 'cheque' => 2, 'inBank' => 3, 'bank' => 3];
        $modeOfSalary = @$modeOfSalaryArr[@$user->modeofsalary];

        $employmentTypeArr = ['SALARIED' => 'Salaried', 'SELF_EMPLOYED' => 'Self Employed'];
        $employmentType = @$employmentTypeArr[@$user->type_of_employment];

        $stayTypeArr = ['rented' => 'Rent', 'owned' => 'Own'];
        $stayType = @$stayTypeArr[@$user->stay_type];





            $APIData = array (
                'partner_name' => 'PhoneParLoan_Partner',
                'reference_Id' => '123',
                'applicant_id' => '234',
                'loan_amount' => @$user->loan_amt,
                'product_type_name' => 'CASHe_30',
                'Personal Information' =>
                    array (
                        'First Name' => @$nameArr[0],
                        'Last Name' => @$nameArr[1],
                       'DOB' => date('d-m-Y', strtotime(@$user->dob)),
                        'Gender' => @$user->gender,
                        'Address Line 1' => $address1,
                        'Address Line 2' => $address2,
                        'Landmark (Address Line 3)' => $address3,
                        'Pincode' => @$user->pincode,
                        'State' => @$user->state,
                        'Type of Accommodation' => $stayType,
                        'PAN' => @$user->pan_card
                    ),
                'Applicant Information' =>
                    array (
                        'Company Name' => @$user->company,
                       // 'Designation' => @$user->designation?$user->designation:'',
                        'Monthly Income' =>  @$user->salary,
                        'Number of Years in Current Work' => @$user->experience,
                        'Official Email' => @$user->work_email_id,
                        'Employment Type' => $employmentType,
                        'Salary ReceivedTypeId' => $modeOfSalary,
                    ),

                'Contact Information' =>
                    array (
                        'Mobile' => @$user->mobile,
                        'Email Id' => @$user->email,
                    )
            );

         //  dd(json_encode($data));

            $check_sum_params = [
                'key' => $API_KEY,
                'data' => json_encode($APIData)
            ];

            $checksum = getCasheCheckSum($check_sum_params);

           // dd($checksum);

            $header = [
                'Content-Type' => 'application/json',
                'Check-Sum' => $checksum
            ];

            $requestData = $APIData;

            $requestContent = [
                'headers' =>$header,
                'json' => $requestData
            ];

            $client = new Client();

            $apiRequest = $client->request('POST', $endpoint, $requestContent);

            $response = json_decode($apiRequest->getBody());

            // dd($response);

            // update aggregator lead status behalf of CV instant decision
            if(isset($response->status)){

                if($response->status == 'OK'){
                    $aggregator_status =   'success';
                }
            }



            $aggregator = Aggregators::where('name', 'Cashe')->first();

            $data = array( 'user_id'=>$user->_id,
                'aggregator_id'=>$aggregator->_id,
                'message' =>'',
                'status' => @$aggregator_status
            );



            DB::collection('aggregators_requests')->where(['user_id'=>$user->_id,'aggregator_id'=>$aggregator->_id])->update($data , array('upsert' => true));

            AggregatorLogs::create([
                'user_id'=>$user->_id,
                'aggregator_id'=>$aggregator->_id,
                'end_point' => $endpoint,
                'input' =>$APIData,
                'response' =>$response,
                'status_code' => $apiRequest->getStatusCode()
            ]);

            return $this->sendResponse($response, 'Data pushed successfully');

        }catch (\Exception $e) {

            return $this->sendResponse([], $e->getMessage());

        }
    }
}
