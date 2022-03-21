<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColTokenTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    function __construct()
    {
        $this->myTable = 'users';
        $this->column = 'token';
    }

    public function up()
    {
        if (Schema::hasColumn($this->myTable, $this->column) == false) //check the column
        {
            Schema::table($this->myTable, function (Blueprint $table)
            {
                $table->string($this->column)->nullable()->after('wamate_id'); //drop it
            });
        }
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
