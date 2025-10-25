<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::before(fn(User $user, string $ability) => $user->role === 'admin' ? true : null);

        Gate::define('admin-only', fn(User $user) => $user->role === 'admin');

        Gate::define('crear-documentos', fn(User $user) =>
            in_array($user->role, ['doctor','admin'], true)
        );
    }
}