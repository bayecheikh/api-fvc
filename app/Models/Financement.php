<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financement extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
       'date_debut',
       'date_fin',
       'titre_projet',
       'objectif_global_projet',
       'montant_total_adaptation',
       'montant_total_attenuation',
       'montant_total_execute',
       'montant_total_restant',
       'status',
       'state',
       'motif_rejet', 
       'brouillon'
    ];
    public function annee() {
        return $this->belongsToMany(Annee::class,'annees_financements');          
    }
    public function domaine_financement() {
        return $this->belongsToMany(DomaineFinancement::class,'domaine_financements_financements');          
    }
    public function source_financement() {
        return $this->belongsToMany(SourceFinancement::class,'source_financements_financements');          
    }
    public function objectif_adaptations() {
        return $this->belongsToMany(ObjectifAdaptation::class,'objecti_adaptations_financements');          
    }
    public function objectif_attenuations() {
        return $this->belongsToMany(ObjectifAttenuation::class,'objecti_attenuations_financement');          
    }
    public function objectif_transversals() {
        return $this->belongsToMany(ObjectifTransversal::class,'objecti_transversals_financement');          
    }
    public function agence_acredite() {
        return $this->belongsToMany(AgenceAcredite::class,'agence_acredites_financements');      
    }
    public function ligne_financement_bailleurs() {
        return $this->belongsToMany(LigneFinancementBailleur::class,'ligne_bailleurs_financements');          
    }
    public function ligne_financement_cos() {
        return $this->belongsToMany(LigneFinancementCo::class,'ligne_cos_financements');          
    }
    public function ligne_financement_secteurs() {
        return $this->belongsToMany(LigneFinancementSecteur::class,'ligne_secteurs_financements');          
    }
    public function ligne_financement_zones() {
        return $this->belongsToMany(LigneFinancementZone::class,'ligne_zones_financements');          
    }
    public function structure() {
        return $this->belongsToMany(Structure::class,'structures_financements');          
    }   
    public function resumes() {
        return $this->belongsToMany(Fichier::class,'resumes_financements');       
    }
    public function tableau_budgets() {
        return $this->belongsToMany(Fichier::class,'tableau_budgets_financements');       
    }
}
