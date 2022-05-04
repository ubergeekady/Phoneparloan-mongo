<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use App\Models\UnderwritingFile;
use Illuminate\Http\Request;
use Validator;

class UnderwritingUploadController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Upload json file that we receive from android application
     * after uploading json to s3 we fire a event to call
     * job that process json file and extract
     * meta data of user
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            // putting some validation rules for file
            $validator = Validator::make($request->all(), [
                'json_file' => 'required|file'
            ]);


            if ($validator->fails()) {

                return $this->sendError('Validation Error.', $validator->errors());

            }

            $file_data = $request->file('json_file');

            if ($file_data->getClientOriginalExtension() === 'json') {

                // store data to local and database
                UnderwritingFile::storeFile($file_data, $path = 'import', $provider = 'local', $store = true);

                return $this->sendResponse([], 'file uploaded successfully');

            } else {
                return $this->sendError('Validation Error.', array('file_type' => 'File type will be json file'));
            }

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }
}
