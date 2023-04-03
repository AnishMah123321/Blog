<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;


class Post extends Model
{
    use HasApiTokens,HasFactory,SoftDeletes;
    protected $fillable = [
        'customer_id',
        'category_id',
        'title',
        'post',
        'upvotes',
        'downvotes',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','customer_id');
    }
}
