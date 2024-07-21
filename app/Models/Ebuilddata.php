<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbuildData extends Model
{
    use HasFactory;
    protected $table = 'ebuilddata';

    protected $fillable = [
        'name',
        'mail',
        'phone_number',
        'matriculef',
        'address',
        'logo',
        'rib',
    ];
}