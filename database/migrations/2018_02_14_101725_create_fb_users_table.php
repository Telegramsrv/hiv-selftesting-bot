<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFbUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('language',100)->nullable();
            $table->string('timezone',100)->nullable();
            $table->string('gender',100)->nullable();
            $table->string('user_id',100)->nullable();
            $table->string('user_name',100)->nullable();
            $table->string('age',100)->nullable();
            $table->string('user_gender',100)->nullable();
            $table->boolean('followed')->default(0)->nullable();
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
        Schema::dropIfExists('fb_users');
    }
}
