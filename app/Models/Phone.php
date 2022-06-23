<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $table = "phones";

    /*
        counter = jumlah counter dalam 1 hari / to limit total message per day
        status :
        0 == disconnect
        1 == connected
        3 == admin phone
    */
}
