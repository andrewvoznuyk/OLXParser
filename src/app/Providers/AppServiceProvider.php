<?php

namespace App\Providers;

use App\Contracts\LinkParserServiceInterface;
use App\Contracts\LinkValidatorInterface;
use App\Services\LinkParserService;
use App\Services\LinkValidatorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(LinkParserServiceInterface::class, LinkParserService::class);
        $this->app->bind(LinkValidatorInterface::class, LinkValidatorService::class);

    }

    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }

}
