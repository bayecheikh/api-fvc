<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monnaie extends Model
{
    use HasFactory;
    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'libelle','status'
  ];

  public function investissements() {
      return $this->belongsToMany(Investissement::class,'monnaies_investissements');          
  }

}
