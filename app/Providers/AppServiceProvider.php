<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // 🔗 Route Model Binding por UUID
        Route::bind('patient', function ($value) {
            return \App\Models\Patient::where('uuid', $value)->firstOrFail();
        });

        Route::bind('prescription', function ($value) {
            return \App\Models\Prescription::where('uuid', $value)->firstOrFail();
        });
    }
}