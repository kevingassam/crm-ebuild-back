<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $fillable = ['client',
        'client_email',
        'date_creation',
        'nombre_operations',
        'invoiced'
          ];
    protected static function booted()
    {
        static::creating(function ($devis) {
            $devis->date_creation = now();
        });
    }
    public function getFormattedIdAttribute()
    {
        $month = date('M', strtotime($this->date_creation));
        $year = date('Y', strtotime($this->date_creation));
        $factureNumber = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return 'DVI ' . $month . '.' . $year . ' - ' . $factureNumber;
    }

    /* public function getFormattedIdAttribute()
     {
         return 'Devis N° ' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
     }*/
    protected $appends = ['formatted_id'];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
   /* public function invoiced()
    {
        return $this->belongsTo(Invoiced::class);
    }
     public function note()
        {
            return $this->belongsTo(Note::class);
        }*/
}

