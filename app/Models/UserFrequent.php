<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserFrequent extends Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $collection = 'user_location';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'latitude','longitude','address','currentDateTime','user_id'
    ];
}
