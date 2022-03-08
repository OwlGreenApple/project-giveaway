<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedeemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeems', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('user_id');
            $table->String('name')->nullable();
            $table->Integer('total')->nullable();
            $table->String('account')->nullable();
            $table->Boolean('is_paid')->default(0);
            $table->string('account_name')->nullable();
            $table->String('withdrawal_method')->default('DANA')->nullable();
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
        Schema::dropIfExists('messages');
    }
}
