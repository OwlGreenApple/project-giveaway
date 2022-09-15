<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Know extends Model
{
    use HasFactory;

    protected $table = 'knows';

    public function events()
    {
        $this->belongsTo('events');
    }
}
