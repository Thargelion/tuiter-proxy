<?php

namespace App\Providers;

use App\Services\DefaultTokenCrud;
use App\Services\DefaultTokenGenerator;
use App\Services\DefaultUserCrud;
use App\Services\TokenCrud;
use App\Services\TokenGenerator;
use App\Services\UserCrud;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    public $singletons = [
        TokenGenerator::class => DefaultTokenGenerator::class,
        UserCrud::class => DefaultUserCrud::class,
        TokenCrud::class => DefaultTokenCrud::class,
    ];
}
