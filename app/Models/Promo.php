<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $table = "promos";

    /* 
        fb = facebook share
        tw = twitter share
        mail = email share
        wa = whatsapp share
        tg = telegram share
        copy = copy link
        wd = embed widget on website
    */
}
