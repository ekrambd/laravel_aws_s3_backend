<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Repositories\Bucket\BucketInterface;
use App\Repositories\Bucket\BucketRepository;
use App\Repositories\Folder\FolderInterface;
use App\Repositories\Folder\FolderRepository;
use App\Repositories\File\FileInterface;
use App\Repositories\File\FileRepository;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BucketInterface::class, BucketRepository::class);
        $this->app->bind(FolderInterface::class, FolderRepository::class);
        $this->app->bind(FileInterface::class, FileRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
    }
}
