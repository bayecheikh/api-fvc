<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneFinancementSecteur extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['id_investissement','id_secteur','id_sous_secteur',
       'montant_adaptation','montant_attenuation','montant_total','status'
    ];
}