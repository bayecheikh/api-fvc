<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeSource extends Model
{
    use HasFactory;
    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'libelle_type_source','status'
  ];

  public function sources() {
    return $this->belongsToMany(SourceFinancement::class,'source_type_sources','source_id','type_sources_id');          
  }

  public function structures() {
    return $this->belongsToMany(Structure::class,'structures_type_sources','type_id','structure_id');          
  }
}
