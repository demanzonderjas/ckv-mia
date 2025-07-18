<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = ['page_id', 'type', 'content', 'order', 'title'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
