<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $table = "broadcasts";

    /*
        event_id :
        0 -> all
        
        status :
        0 -> pending
        1 -> executed
    */ 
}
