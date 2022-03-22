<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contestants extends Model
{
    use HasFactory;

    protected $table = 'contestants';

    /*
        confirmed : 
        0 -- user not confimed yet
        1 -- user confimed
      
        status:
        0 -- not winner / still running
        1 -- winner -- awarded
        2 -- contestant has removed from winner

    */
}
