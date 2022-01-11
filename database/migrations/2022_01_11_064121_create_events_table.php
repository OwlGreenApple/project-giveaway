<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('url_link');
            $table->string('title');
            $table->text('desc');
            $table->DateTime('start');
            $table->string('end')->nullable();
            $table->DateTime('award');
            $table->boolean('unlimited')->default(0);
            $table->Integer('winners');
            $table->string('owner');
            $table->string('owner_url');
            $table->string('prize_name');
            $table->Integer('prize_value');
            $table->boolean('media')->default(0);
            $table->boolean('tw')->default(1);
            $table->boolean('fb')->default(0);
            $table->boolean('wa')->default(1);
            $table->boolean('ln')->default(0);
            $table->boolean('mail')->default(0);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('events');
    }
}
