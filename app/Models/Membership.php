<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    /*
        status :
        0 -- membership belum terpakai
        1 -- membership sudah terpakai
    */

    protected $table = 'memberships';
}
