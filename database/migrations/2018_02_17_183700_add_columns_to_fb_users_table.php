<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToFbUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fb_users', function (Blueprint $table) {
            $table->boolean('tested')->default(0)->nullable();
            $table->boolean('first_timer')->default(0)->nullable();
            $table->string('bought_from',200)->nullable();
            $table->string('kit_used',100)->nullable();
            $table->string('results',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_users', function (Blueprint $table) {
            $table->dropColumn(['tested','first_timer','bought_from','kit_used','test_result']);
        });
    }
}
