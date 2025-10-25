<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
  protected $fillable = ['user_id', 'cmp', 'especialidad'];

  public function user(): BelongsTo {
    return $this->belongsTo(User::class);
  }

  public function recetas(): HasMany {
    return $this->hasMany(Prescription::class); // asumiendo modelo existente
  }

  public function facturas() {
     return $this->hasMany(\App\Models\Invoice::class); }
     
  public function prescriptions() {
     return $this->hasMany(\App\Models\Prescription::class); }
}