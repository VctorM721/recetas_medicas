<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (Schema::hasTable('prescriptions') && !Schema::hasColumn('prescriptions','doctor_id')) {
      Schema::table('prescriptions', function (Blueprint $table) {
        $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete()->index();
      });
    }
    if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices','doctor_id')) {
      Schema::table('invoices', function (Blueprint $table) {
        $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete()->index();
      });
    }
  }
  public function down(): void {
    if (Schema::hasTable('prescriptions') && Schema::hasColumn('prescriptions','doctor_id')) {
      Schema::table('prescriptions', fn(Blueprint $t) => $t->dropConstrainedForeignId('doctor_id'));
    }
    if (Schema::hasTable('invoices') && Schema::hasColumn('invoices','doctor_id')) {
      Schema::table('invoices', fn(Blueprint $t) => $t->dropConstrainedForeignId('doctor_id'));
    }
  }
};