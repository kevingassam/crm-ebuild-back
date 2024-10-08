<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class FilesTache extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
    ];

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }
     public function getUrlAttribute()
        {
            // Assuming you have a 'path' column that stores the file path
            return Storage::url($this->file_path);
        }
}
