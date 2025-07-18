<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SideMenuLink extends Model
{
    protected $fillable = ['title', 'url', 'order', 'active', 'description'];
}
