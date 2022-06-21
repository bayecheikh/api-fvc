<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneModeInvestissement extends Model
{
    use HasFactory;
     /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'libelle','slug','predefini','status'
     ];

    public function dimension() {
        return $this->belongsToMany(Dimension::class,'dimensions_ligne_modes','dimension_id','ligne_mode_id');          
    }
}
