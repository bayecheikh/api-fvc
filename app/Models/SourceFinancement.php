<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceFinancement extends Model
{
    use HasFactory;
    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'libelle_source','status'
  ];

  public function type_sources() {
    return $this->belongsToMany(TypeSource::class,'source_type_sources','source_id','type_sources_id');          
  }

  public function structures() {
    return $this->belongsToMany(Structure::class,'source_structures','source_id','structure_id');          
  }
  public function investissements() {
    return $this->belongsToMany(SourceFinancement::class,'sources_investissements','source_id','investissement_id');          
  }
}
