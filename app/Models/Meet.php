<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meet extends Model
{
    protected $table = 'meet';
    protected $fillable = [
        'description',
        'url',
        'title',
        'start',
        'end',
        'allday'
    ];
    public function personnel()
    {
        return $this->belongsToMany(Personnel::class,'guestsmeet');
    }
    public function client()
    {
        return $this->belongsToMany(Client::class,'guestsmeet');
    }
}
