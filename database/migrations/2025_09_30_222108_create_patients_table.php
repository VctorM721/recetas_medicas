<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('patients', function (Blueprint $t) {
      $t->id();
      $t->uuid('uuid')->unique();
      $t->string('full_name');
      $t->string('dpi')->nullable();        
      $t->date('birthdate')->nullable();
      $t->enum('sex',['M','F','X'])->nullable();
      $t->string('phone')->nullable();
      $t->string('address')->nullable();
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('patients'); }
};