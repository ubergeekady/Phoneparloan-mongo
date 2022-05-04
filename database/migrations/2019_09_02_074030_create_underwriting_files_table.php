<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnderwritingFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('underwriting_files', function (Blueprint $collection) {

            $collection->bigIncrements('id');
            $collection->string('name');
            $collection->string('extension');
            $collection->string('mimetype');
            $collection->string('weblink');
            $collection->string('filepath');
            $collection->string('size');
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
        Schema::dropIfExists('underwriting_files');
    }
}
