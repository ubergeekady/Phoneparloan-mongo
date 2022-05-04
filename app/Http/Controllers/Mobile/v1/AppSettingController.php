<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppSettingController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Provide application setting
     * to application
     */
    public function getAppSetting()
    {
        try {

            $setting['setting']['app_name'] = 'PhoneParLoan';
            $setting['setting']['app_version'] = 1.1;
            $setting['setting']['under_maintenance'] = 0;
            $setting['setting']['force_update'] = 0;
            $setting['setting']['colors'][] = [
                'primary' => '#A52A2A'
            ];
            $setting['setting']['colors'][] = [
                'secondary' => '#008000'
            ];
            $setting['setting']['logo'][] = [
                'for' => 'app_icon',
                'link' => 'http://www.realangels.in/'
            ];
            $setting['setting']['logo'][] = [
                'for' => 'splash_screen',
                'link' => 'http://www.realangels.in/'
            ];

            return $this->sendResponse($setting, 'success');

        } catch (\Exception $e) {

            return $this->sendError('Server Error.', $e->getMessage());

        }
    }
}
