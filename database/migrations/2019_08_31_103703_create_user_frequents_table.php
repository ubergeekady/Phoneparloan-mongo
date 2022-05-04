<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFrequentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_location', function (Blueprint $collection) {

            $collection->bigIncrements('id');
            $collection->string('latitude');
            $collection->string('longitude');
            $collection->string('address')->nullable();
            $collection->string('currentDateTime')->nullable();
            $collection->string('user_id');
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
        Schema::dropIfExists('user_location');
    }
}
