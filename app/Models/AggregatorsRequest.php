<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class AggregatorsRequest extends Eloquent
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $collection = 'aggregators_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','aggregator_id','status','message'
    ];





    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
