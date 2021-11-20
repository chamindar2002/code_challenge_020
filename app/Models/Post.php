<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'uuid', 'user_id','title', 'content', 'tags', 'medium_response_payload'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
