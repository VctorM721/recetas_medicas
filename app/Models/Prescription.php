<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'patient_id',
        'diagnosis',
        'notes',
        'doctor_name',
        'issued_at',  
        'doctor_id'
    ];


    protected $casts = [
        'issued_at'  => 'date',   
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function items(){ return $this->hasMany(PrescriptionItem::class); }
    public function doctor() { return $this->belongsTo(\App\Models\Doctor::class); }
    public function patient() { return $this->belongsTo(\App\Models\Patient::class); }

    public function getRouteKeyName(){ return 'uuid'; }
}