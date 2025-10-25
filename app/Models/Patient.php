<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','full_name','dpi','birthdate','sex','phone','address'];

    protected static function booted(){
        static::creating(fn($m) => $m->uuid = (string)Str::uuid());
    }

    public function getRouteKeyName(): string { return 'uuid'; }

    public function prescriptions(){ return $this->hasMany(Prescription::class); }
}