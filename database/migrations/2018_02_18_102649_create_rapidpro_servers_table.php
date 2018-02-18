<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRapidproServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapidpro_servers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Name',100)->nullable();
            $table->string('Host',100)->nullable();
            $table->string('Url',200)->nullable();
            $table->boolean('Status')->default(0);
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
        Schema::dropIfExists('rapidpro_servers');
    }
}
