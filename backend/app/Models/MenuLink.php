<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuLink extends Model
{
    protected $fillable = ['title', 'url', 'order', 'active', 'description', 'category', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(MenuLink::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuLink::class, 'parent_id');
    }
}
