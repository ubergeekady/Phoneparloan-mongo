<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class OtpLog extends Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $collection = 'otp_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile','otp'
    ];
}
