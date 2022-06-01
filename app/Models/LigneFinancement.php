<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneFinancement extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
       'mnt_prevu','mnt_mobilise', 'mnt_execute', 'status'
    ];
    public function pilier() {
        return $this->belongsToMany(Pilier::class,'piliers_ligne_financements');          
    }
    public function axe() {
        return $this->belongsToMany(Axe::class,'axes_ligne_financements');          
    }
    public function type_ligne() {
        return $this->belongsToMany(TypeLigne::class,'ligne_financements_type_lignes');          
    }
    public function investissement() {
        return $this->belongsToMany(Investissement::class,'ligne_financements_investissements');          
    }
}
