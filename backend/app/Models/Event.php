<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name', 'description', 'date', 'location', 'team_id'
    ];
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function uploads()
    {
        return $this->morphMany(\App\Models\Upload::class, 'entity');
    }
    public function image()
    {
        return $this->morphOne(\App\Models\Upload::class, 'entity')->where('type', 'image');
    }
}
