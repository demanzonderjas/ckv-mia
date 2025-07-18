<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'team_id', 'gender'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
