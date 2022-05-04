<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Aggregators extends Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $collection = 'aggregators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','logo','status'
    ];

    static $cvErrorCode = [
        'CV-330' => 'pincode_rejected',
        'CV-331' => 'salary_rejected',
        'CV-332' => 'age_rejected',
        'CV-203' => 'success',
        'CV-204' => 'success'
    ];
}
