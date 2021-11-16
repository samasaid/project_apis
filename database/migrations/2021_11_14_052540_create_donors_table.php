<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('national_id')->unique();
            $table->string('mobile')->unique();
            $table->string('address');
            $table->string('blood_type');  // [A+ , O+ , B+ , AB+ , A- , O- , B- , AB-]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donors');
    }
}
