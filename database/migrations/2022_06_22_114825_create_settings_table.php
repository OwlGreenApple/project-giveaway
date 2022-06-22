<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('percentage');
            $table->string('sponsor')->nullable();
            $table->bigInteger('changed_by')->default(0);
            $table->boolean('maintenance')->default(0);
            $table->timestamps();
        });

        DB::table('settings')->insert(
            ['id'=>1,'percentage'=>0,'sponsor'=>'Powered by topleads.app']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
