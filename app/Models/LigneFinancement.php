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
    protected $fillable = ['id_investissement','id_structure','id_annee','id_monnaie','id_dimension',
       'id_pilier','id_axe','id_structure_source','id_type_structure_source','id_structure_beneficiaire','id_region', 'montantBienServicePrevus','montantBienServiceMobilises',
       'montantBienServiceExecutes','montantInvestissementPrevus',
       'montantInvestissementMobilises','montantInvestissementExecutes', 'status'
    ];
              
    public function axe() {
        return $this->belongsToMany(Axe::class,'axes_ligne_financements');          
    }
    public function pilier() {
        return $this->belongsToMany(Pilier::class,'piliers_ligne_financements');          
    }
    public function bailleur() {
        return $this->belongsToMany(Bailleur::class,'bailleurs_ligne_financements');          
    }
    public function structure_source() {
        return $this->belongsToMany(Structure::class,'structure_sources_ligne_financements');          
    }
    public function type_structure_source() {
        return $this->belongsToMany(SourceFinancement::class,'type_structure_sources_ligne_financements','type_structure_source_id','ligne_financement_id');          
    }
    public function structure_beneficiaire() {
        return $this->belongsToMany(Structure::class,'structure_beneficiaires_ligne_financements');          
    }
    public function region() {
        return $this->belongsToMany(Region::class,'regions_ligne_financements');          
    }
    public function structure() {
        return $this->belongsToMany(Structure::class,'structures_ligne_financements');          
    }
    public function annee() {
        return $this->belongsToMany(Annee::class,'annees_ligne_financements');          
    }
    public function monnaie() {
        return $this->belongsToMany(Monnaie::class,'monnaies_ligne_financements');          
    }
    public function dimension() {
        return $this->belongsToMany(Dimension::class,'dimensions_ligne_financements');          
    }
    public function type_ligne() {
        return $this->belongsToMany(TypeLigne::class,'ligne_financements_type_lignes');          
    }
    public function investissement() {
        return $this->belongsToMany(Investissement::class,'ligne_financements_investissements');          
    }
}
