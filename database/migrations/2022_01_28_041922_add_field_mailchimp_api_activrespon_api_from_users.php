<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldMailchimpApiActivresponApiFromUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership',30)->default('free')->after('remember_token');
            $table->string('end_membership',30)->nullable()->after('membership');
            $table->string('activrespon_api')->nullable()->after('lang');
            $table->string('mailchimp_api')->nullable()->after('activrespon_api');
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
            //
        });
    }
}
