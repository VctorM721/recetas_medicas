<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('prescription_items', function (Blueprint $t) {
    $t->id();
    $t->foreignId('prescription_id')
      ->constrained('prescriptions')
      ->cascadeOnDelete();
    $t->string('drug');
    $t->string('dose');
    $t->string('frequency');
    $t->string('duration');
    $t->text('instructions')->nullable();
    $t->timestamps();
});
  }
  public function down(): void { Schema::dropIfExists('prescription_items'); }
};