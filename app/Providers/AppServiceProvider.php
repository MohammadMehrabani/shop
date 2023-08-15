<?php

namespace App\Providers;

use App\Contracts\AdminAuthenticateServiceInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Contracts\ProductServiceInterface;
use App\Contracts\UserAuthenticateServiceInterface;
use App\Contracts\UserRepositoryInterface;
use App\Repositories\MongoProductRepository;
use App\Repositories\MongoUserRepository;
use App\Services\AdminAuthenticateService;
use App\Services\ProductService;
use App\Services\UserAuthenticateService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $singletons = [
            // Repositories
            UserRepositoryInterface::class               => MongoUserRepository::class,
            ProductRepositoryInterface::class               => MongoProductRepository::class,

            // Services
            UserAuthenticateServiceInterface::class      => UserAuthenticateService::class,
            AdminAuthenticateServiceInterface::class      => AdminAuthenticateService::class,
            ProductServiceInterface::class      => ProductService::class,
        ];

        foreach ($singletons as $abstract => $concrete) {
            $this->app->singleton($abstract, $concrete);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data = [], $statusCode = 200) {
            return Response::json([
                'success' => true,
                'data' => $data
            ], $statusCode);
        });

        Response::macro('error', function ($data = [], $statusCode = 400) {
            return Response::json([
                'success' => false,
                'errors' => $data
            ], $statusCode);
        });
    }
}
