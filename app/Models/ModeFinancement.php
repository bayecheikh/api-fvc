<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeFinancement extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'libelle','montant','status'
    ];
    public function investissements() {
        return $this->belongsToMany(Investissement::class,'mode_financements_investissements');          
    }
}
