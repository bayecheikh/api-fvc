<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Axe extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom_axe','status'
    ];

    public function pilier() {
        return $this->belongsToMany(Pilier::class,'piliers_axes');          
    }

    public function ligne_financements() {
        return $this->belongsToMany(LigneFinancement::class,'axes_ligne_financements');          
    }
    public function investissements() {
        return $this->belongsToMany(Investissement::class,'axes_investissements');          
    }
}
