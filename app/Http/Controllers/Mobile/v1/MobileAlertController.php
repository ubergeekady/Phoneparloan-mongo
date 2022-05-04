<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MobileAlertController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }


    /**
     * Send alert popup to mobile
     * Application
     */
    public function getAlert()
    {
        try {

            $popup['popup']['image_url'] = null;
            $popup['popup']['heading'] = "Welcome to Commercial Application";
            $popup['popup']['content'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent risus leo, dictum in vehicula sit amet,feugiat tempus tellus. Duis quis sodales risus.Etiam euismod ornare consequat.";
            $popup['popup']['can_close'] = true;
            $popup['popup']['buttons'][] = [
                'text' => 'Cancel',
                'bg_color' => '#A52A2A',
                'action' => [
                    'type' => 'SCREEN',
                    'type_content' => 'Register',
                ]
            ];
            $popup['popup']['buttons'][] = [
                'text' => 'Continue',
                'bg_color' => '#008000',
                'action' => [
                    'type' => 'URL',
                    'type_content' => 'http://www.realangels.in/',
                ]
            ];

            return $this->sendResponse($popup, 'success');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }
}
