<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'user_id', 'published_at'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
    
    public function likes(){
        return $this->morphMany(Like::class, 'likeable');
    }

    public function likedByUsers(){
        return $this->belongsToMany(User::class, 'likes');
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

}
