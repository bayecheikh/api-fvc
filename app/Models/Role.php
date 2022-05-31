<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'description','status'
  ];

  public function permissions() {

    return $this->belongsToMany(Permission::class,'roles_permissions');
          
  }
   
   public function users() {
   
    return $this->belongsToMany(User::class,'role_user');
          
   }
}
