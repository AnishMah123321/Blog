<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;


class Comment extends Model
{
    use HasApiTokens,HasFactory,SoftDeletes;
    protected $fillable = [
        'customer_id',
        'post_id',
        'comment',
        'upvotes',
        'downvotes',
    ];

     public function user()
    {
        return $this->hasOne(User::class,'id','customer_id');
    }
}
