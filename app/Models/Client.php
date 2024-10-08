<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        /*'name',
        'email',*/
        'phone_number',
        'address',
        'social_reason',
        'RNE',
        'confirmation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function meets()
{
    return $this->belongsToMany(Meet::class,'guestsmeet');
}
}
