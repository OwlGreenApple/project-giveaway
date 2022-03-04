<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';

    /*
        status :
        0 == message not send
        1 == message sent
        2 == message delivered
        3 == message READ
        4 == message FAILED
    */
}
