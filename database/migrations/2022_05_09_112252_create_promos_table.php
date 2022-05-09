<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->boolean('fb')->default(0);
            $table->boolean('tw')->default(0);
            $table->boolean('mail')->default(0);
            $table->boolean('wa')->default(0);
            $table->boolean('tg')->default(0);
            $table->boolean('copy')->default(0);
            $table->boolean('wd')->default(0);
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
        Schema::dropIfExists('promos');
    }
}
