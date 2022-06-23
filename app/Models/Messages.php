<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Phone;

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
        5 == case winner if user winning_run = 1 but user's phone inactive
    */

    public static function sender()
    {
        $ph = Phone::where('status',3)->inRandomOrder()->first();
        $sender = $ph->number;
        return $sender;
    }
}
