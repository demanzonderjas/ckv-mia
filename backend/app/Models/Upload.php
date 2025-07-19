<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'path', 'type', 'entity_type', 'entity_id'
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}
