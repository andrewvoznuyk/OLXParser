<?php

namespace App\Providers;

use App\Contracts\LinkParserServiceInterface;
use App\Contracts\LinkValidatorInterface;
use App\Contracts\RabbitMqServiceInterface;
use App\Services\LinkParserService;
use App\Services\LinkValidatorService;
use App\Services\RabbitMqService;
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
        $this->app->bind(RabbitMqServiceInterface::class, function () {
            $rabbitHost = getenv('RABBITMQ_DEFAULT_HOST') ?: 'localhost';
            $rabbitPort = getenv('RABBITMQ_DEFAULT_PORT') ?: 5672;
            $rabbitUser = getenv('RABBITMQ_DEFAULT_USER') ?: 'guest';
            $rabbitPassword = getenv('RABBITMQ_DEFAULT_PASS') ?: 'guest';
            $queueName = 'send-email';

            return new RabbitMqService($rabbitHost, $rabbitPort, $rabbitUser, $rabbitPassword, $queueName);
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }

}
