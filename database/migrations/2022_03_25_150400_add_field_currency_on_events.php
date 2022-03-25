<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCurrencyOnEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events',function(Blueprint $table){
            $table->string('currency',10)->default('usd')->after('prize_value');
        });

        Schema::table('users',function(Blueprint $table){
            $table->dropColumn('currency');
            $table->string('brand_link',50)->nullable()->after('branding');
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
