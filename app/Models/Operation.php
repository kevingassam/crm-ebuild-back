<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'devis_id',
        'nature',
        'quantité',
        'montant_ht',
        'taux_tva',
        'montant_ttc',
    ];
    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }


}
