<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'drug', 'dose', 'frequency', 'duration', 'instructions'
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
