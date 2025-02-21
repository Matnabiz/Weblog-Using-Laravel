<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
    
    public function likes(){
        return $this->hasMany(Like::class);
    }

    public function likedByUsers(){
        return $this->belongsToMany(User::class, 'likes');
    }
}
