<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Daftar kebijakan (policies) Anda
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Untuk Laravel Passport 10.x atau yang lebih baru
        Passport::enableImplicitGrant();
    }
}


