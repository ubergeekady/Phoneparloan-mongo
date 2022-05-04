<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAggregatorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregator_logs', function (Blueprint $collection) {
            $collection->bigIncrements('id');
            $collection->string('user_id');
            $collection->string('aggregator_id');
            $collection->longText('input');
            $collection->longText('response');
            $collection->string('status_code');
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
        Schema::dropIfExists('aggregator_logs');
    }
}
