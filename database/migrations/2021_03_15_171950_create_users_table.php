<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('user_id');
            $table->string('phoneNumber')->unique();
            $table->string('socialEmail')->unique();
            $table->string('deviceType');
            $table->string('deviceId');
            $table->string('socialType');
            $table->string('socialName');
            $table->string('gender');
            $table->string('countryCode');
            $table->string('userLocale');
            $table->string('advertisingId');
            $table->string('versionName');
            $table->string('versionCode');
            $table->string('api_token');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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