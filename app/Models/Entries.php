<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entries extends Model
{
    use HasFactory;

    protected $table = 'entries';

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

        share : 
        8 -- share twitter
        9 -- share facebook
        10 -- share whatsapp
        11 -- share linkedin
        12 -- share email
    */ 
}
