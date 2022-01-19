<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $table = "bonuses";

    /*
        type :
        Social follow 
        0 -- facebook like
        1 -- instagram follow
        2 -- twitter follow
        3 -- youtube subscribe
        4 -- podcast subscribe

        others 
        5 -- daily entries
        6 -- click a link
        7 -- watching youtube
    */ 
}
