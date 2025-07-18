<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
