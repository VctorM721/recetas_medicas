<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            
            if (!Schema::hasColumn('prescriptions', 'doctor_name')) {
                $table->string('doctor_name')->nullable()->after('issued_at');
            }
            if (!Schema::hasColumn('prescriptions', 'issued_at')) {
                $table->dateTime('issued_at')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (Schema::hasColumn('prescriptions', 'doctor_name')) {
                $table->dropColumn('doctor_name');
            }
            if (Schema::hasColumn('prescriptions', 'issued_at')) {
                $table->dropColumn('issued_at');
            }
        });
    }
};