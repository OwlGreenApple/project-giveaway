<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knows', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ev_id')->unsigned();
            $table->string('notes',30);
            $table->timestamps();
            $table->foreign('ev_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::table('contestants', function (Blueprint $table) 
        {
            $table->bigInteger('knows_id')->default(0)->after('referrals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knows');
    }
}
