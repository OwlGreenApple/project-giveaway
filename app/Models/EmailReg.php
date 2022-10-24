<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailReg extends Model
{
    use HasFactory;

    protected $table = "email_regs";

    /* 
        is_error : 
        0 -- pass email
        1 -- error email
        2 -- email not checked yet
    */
}
