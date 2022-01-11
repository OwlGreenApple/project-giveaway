<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contestants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->bigInteger('ref_id')->default(0);
            $table->string('c_name');
            $table->string('c_email');
            $table->string('wa_number');
            $table->string('ref_code');
            $table->Integer('entries')->default(0);
            $table->Integer('referrals')->default(0);
            $table->string('ip')->nullable();
            $table->DateTime('date_enter');
            $table->boolean('confirmed')->default(0);
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
        Schema::dropIfExists('contestants');
    }
}
