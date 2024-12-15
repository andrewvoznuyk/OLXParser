<?php

use Root\App\Callbacks\EmailCallbackHandler;
use Root\App\Services\RabbitMqService;

require __DIR__ . '/../vendor/autoload.php';

$rabbitMqService = new RabbitMqService(
    getenv('RABBITMQ_DEFAULT_HOST'),
    getenv('RABBITMQ_DEFAULT_PORT'),
    getenv('RABBITMQ_DEFAULT_USER'),
    getenv('RABBITMQ_DEFAULT_PASS')
);

$rabbitMqService->consumeMessages([
    (new EmailCallbackHandler()),
    'handle'
]);