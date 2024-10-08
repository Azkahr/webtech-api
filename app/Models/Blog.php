<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s', 
    ];

    public function comments() {
        return $this->hasMany(BlogComment::class, 'blog_id', 'id');
    }

    public function author() {
        return $this->belongsTo(User::class, 'author', 'id');
    }
}
