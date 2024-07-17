<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $fillable = [
        'type',
        'notifiable',
        'data',
        'read_at',
    ];
}
