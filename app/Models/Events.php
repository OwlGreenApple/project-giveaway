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
        0 --> not
        1 --> yes
        in case of unlimited then the date set to null

        media :
        0 --> image
        1 --> youtube video

        tw,fb,wa,ln,mail :
        0 --> unset on contest
        1 --> set on contest

        status :
        0 --> event finish / active
        1 --> event running

    */
}
