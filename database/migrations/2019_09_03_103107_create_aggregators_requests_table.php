<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAggregatorsRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregators_requests', function (Blueprint $collection) {
            $collection->bigIncrements('id');
            $collection->string('user_id');
            $collection->string('aggregator_id');
            $collection->string('status');
            $collection->string('message');
            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregators_requests');
    }
}
