<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('national_id')->unique();
            $table->string('mobile');
            $table->string('address');
            $table->date('date_of_birth')->format('d/m/y');
            $table->string('blood_type');  // [A+ , O+ , B+ , AB+ , A- , O- , B- , AB-]
            $table->string('sex'); // [male , female , other]
            $table->string('social_status'); // [single , married]
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
