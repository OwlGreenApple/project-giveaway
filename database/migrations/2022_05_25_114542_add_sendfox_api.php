<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendfoxApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('sendfox_api')->nullable()->after('mailchimp_api');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('sdf_api_id')->default(0)->after('mlc_api_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sendfox_api');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('sdf_api_id');
        });
    }
}
