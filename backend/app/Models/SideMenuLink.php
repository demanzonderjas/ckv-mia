<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SideMenuLink extends Model
{
    protected $fillable = ['title', 'url', 'order', 'active', 'description', 'category', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(SideMenuLink::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SideMenuLink::class, 'parent_id');
    }
}
