<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 0) Asegúrate de que la tabla doctors exista (por orden de timestamps ya debería)
        if (! Schema::hasTable('doctors')) {
            throw new \RuntimeException('La tabla doctors no existe aún. Corre primero la migración create_doctors_table.');
        }

        // 1) Crear columna doctor_id si no existe (SQL crudo con try/catch)
        try {
            DB::statement("ALTER TABLE `prescriptions` ADD COLUMN `doctor_id` BIGINT UNSIGNED NULL AFTER `id`");
            DB::statement("CREATE INDEX `prescriptions_doctor_id_index` ON `prescriptions` (`doctor_id`)");
        } catch (\Throwable $e) {
            // si ya existe, seguimos
        }

        // 2) Quitar cualquier FK anterior sobre doctor_id (haya apuntado a users o a lo que sea)
        try { DB::statement("ALTER TABLE `prescriptions` DROP FOREIGN KEY `prescriptions_doctor_id_foreign`"); } catch (\Throwable $e) {}
        // algunos MySQL crean nombres distintos; por si acaso:
        try { DB::statement("ALTER TABLE `prescriptions` DROP FOREIGN KEY `prescriptions_doctor_id_foreign_1`"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE `prescriptions` DROP FOREIGN KEY `prescriptions_doctor_id_foreign_2`"); } catch (\Throwable $e) {}

        // 3) Crear el FK correcto hacia doctors(id)
        try {
            DB::statement("
                ALTER TABLE `prescriptions`
                ADD CONSTRAINT `prescriptions_doctor_id_foreign`
                FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE SET NULL
            ");
        } catch (\Throwable $e) {
            // si el constraint ya existe, lo ignoramos
        }
    }

    public function down(): void
    {
        // Quita el FK; deja la columna (o elimínala si quieres)
        try { DB::statement("ALTER TABLE `prescriptions` DROP FOREIGN KEY `prescriptions_doctor_id_foreign`"); } catch (\Throwable $e) {}
        // opcional: borrar columna
        // try { DB::statement("ALTER TABLE `prescriptions` DROP COLUMN `doctor_id`"); } catch (\Throwable $e) {}
    }
};