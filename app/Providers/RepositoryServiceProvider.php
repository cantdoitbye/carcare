<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use App\Repositories\AdminUserRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\CouponRepository;
use App\Repositories\ProductRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AdminUserRepositoryInterface::class, AdminUserRepository::class);
       $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
           $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);

    }

    public function boot()
    {
        //
    }
}