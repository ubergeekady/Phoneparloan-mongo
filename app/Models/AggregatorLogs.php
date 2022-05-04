<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class AggregatorLogs extends Eloquent
{
    protected  $collection = 'aggregator_logs';

    protected  $fillable = [
        'user_id', 'aggregator_id', 'input','response','status_code', 'end_point'
    ];

}
