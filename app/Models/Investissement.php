<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investissement extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
       'lib_autremode','mnt_autremode','lib_autremonnaie','status'
    ];
    public function ligne_financements() {
        return $this->belongsToMany(LigneFinancement::class,'ligne_financements_investissements');          
    }
    public function mode_financements() {
        return $this->belongsToMany(ModeFinancement::class,'mode_financements_investissements');          
    }
    public function dimension() {
        return $this->belongsToMany(Dimension::class,'dimensions_investissements');          
    }
    public function structure() {
        return $this->belongsToMany(Structure::class,'structures_investissements');          
    }
    public function region() {
        return $this->belongsToMany(Region::class,'regions_investissements');          
    }
    public function fichiers() {
        return $this->belongsToMany(Fichier::class,'fichiers_investissements');       
    }
    public function monnaie() {
        return $this->belongsToMany(Monnaie::class,'monnaies_investissements');          
    }
    public function annee() {
        return $this->belongsToMany(Annee::class,'annees_investissements');          
    }
}
