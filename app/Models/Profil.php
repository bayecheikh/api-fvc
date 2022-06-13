<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory;
      
    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'libelle', 'status'
  ];

  public function demandes() {
    return $this->belongsToMany(Demande::class,'demandes_profils');          
  }
}
