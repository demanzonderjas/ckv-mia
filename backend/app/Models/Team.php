<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'category', 'type'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function image()
    {
        return $this->morphOne(\App\Models\Upload::class, 'entity')->where('type', 'image');
    }
}
