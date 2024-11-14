<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Comment extends Model
{
        // Add 'news_id' and 'comment' to the fillable array
        protected $fillable = ['news_id', 'user_id', 'comment'];

        public function user()
    {
        return $this->belongsTo(User::class);
    }
}
