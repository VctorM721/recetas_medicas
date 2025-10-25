<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Crea la columna doctor_id si no existe
        if (! Schema::hasColumn('prescriptions', 'doctor_id')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('doctor_id')->nullable()->index()->after('id');
            });
        }

        // 2) Elimina cualquier FK previo sobre doctor_id (nombre clásico y por arreglo)
        try {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->dropForeign('prescriptions_doctor_id_foreign');
            });
        } catch (\Throwable $e) {}
        try {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->dropForeign(['doctor_id']);
            });
        } catch (\Throwable $e) {}

        // 3) Crea el FK correcto hacia doctors(id) con ON DELETE SET NULL
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreign('doctor_id')
                ->references('id')->on('doctors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Borra el FK; deja la columna (o elimínala si quieres)
        try {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->dropForeign(['doctor_id']);
            });
        } catch (\Throwable $e) {}

        // Si quieres eliminar la columna en down(), descomenta:
        // Schema::table('prescriptions', function (Blueprint $table) {
        //     $table->dropColumn('doctor_id');
        // });
    }
};