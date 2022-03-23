<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    protected $table = 'events';

    /*
        unlimited : 
        cancelled by boss -- march 23 - 2022

        media :
        0 --> image
        1 --> youtube video

        tw,fb,wa,ln,mail :
        0 --> unset on contest
        1 --> set on contest

        status :
        1 --> event running
        2 --> event finish / end
        3 --> time for user giving prize

    */
}
