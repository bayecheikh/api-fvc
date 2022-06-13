<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;
    
    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'firstname', 'lastname','email','telephone','subject','message','status'
  ];

  public function profil() {
    return $this->belongsToMany(Profil::class,'demandes_profils');          
  }
  public function struture() {
    return $this->belongsToMany(Structure::class,'demandes_structures');          
  }
}
