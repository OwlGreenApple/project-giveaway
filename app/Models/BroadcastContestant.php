<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastContestant extends Model
{
    use HasFactory;

    protected $table = "broadcast_contestants";

    /*
        status :
        0 -> pending
        1 -> sent
    */ 
}
