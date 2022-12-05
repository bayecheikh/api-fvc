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
        return $this->belongsToMany(Annee::class,'annees_fines');          
    }
    public function domaine_financement() {
        return $this->belongsToMany(DomaineFinancement::class,'domaine_fines_fines');          
    }
    public function source_financement() {
        return $this->belongsToMany(SourceFinancement::class,'source_fines_fines');          
    }
    public function objectif_adaptations() {
        return $this->belongsToMany(ObjectifAdaptation::class,'objectif_adaptations_fines');          
    }
    public function objectif_attenuations() {
        return $this->belongsToMany(ObjectifAttenuation::class,'objectif_attenuations_fines');          
    }
    public function objectif_transversals() {
        return $this->belongsToMany(ObjectifTransversal::class,'objectif_transversals_fines');          
    }
    public function agence_acredite() {
        return $this->belongsToMany(AgenceAcredite::class,'agence_acredites_fines');      
    }
    public function ligne_financement_bailleurs() {
        return $this->belongsToMany(LigneFinancementBailleur::class,'ligne_fine_bailleurs_fines');          
    }
    public function ligne_financement_cos() {
        return $this->belongsToMany(LigneFinancementCo::class,'ligne_fine_cos_fines');          
    }
    public function ligne_financement_secteurs() {
        return $this->belongsToMany(LigneFinancementSecteur::class,'ligne_fine_secteurs_fines');          
    }
    public function ligne_financement_zones() {
        return $this->belongsToMany(LigneFinancementZone::class,'ligne_fine_zones_fines');          
    }
    public function structure() {
        return $this->belongsToMany(Structure::class,'structures_fines');          
    }   
    public function resumes() {
        return $this->belongsToMany(Fichier::class,'resumes_fines');       
    }
    public function tableau_budgets() {
        return $this->belongsToMany(Fichier::class,'tableau_budgets_fines');       
    }
}
