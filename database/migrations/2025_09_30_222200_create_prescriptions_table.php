<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('prescriptions', function (Blueprint $t) {
      $t->id();
      $t->uuid('uuid')->unique();
      $t->foreignId('patient_id')->constrained()->cascadeOnDelete();
      $t->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
      $t->text('diagnosis')->nullable();
      $t->text('notes')->nullable();
      $t->date('issued_at')->default(now());
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('prescriptions'); }
};