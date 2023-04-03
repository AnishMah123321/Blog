<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;


class Subscriber extends Model
{
    use HasApiTokens,HasFactory,SoftDeletes;
    protected $fillable = [
        'customer_id',
        'subscriber_user_id',
    ];
}
