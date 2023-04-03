<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class Category extends Model
{
    use HasApiTokens,HasFactory,SoftDeletes;
    protected $fillable = [
        'category_name',
        'status',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
