<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailWamateTokenRefreshToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_wamate')->nullable()->after('mailchimp_api');
            $table->BigInteger('wamate_id')->default(0)->after('email_wamate');
            $table->string('refresh_token')->nullable()->after('wamate_id');
            $table->string('ip_server')->nullable()->after('refresh_token');
            $table->string('date_counter')->nullable()->after('ip_server');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
