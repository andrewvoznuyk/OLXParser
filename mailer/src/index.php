<?php

use PhpAmqpLib\Message\AMQPMessage;
use Root\App\Services\MailerService;
use Root\App\Services\RabbitMqService;

require __DIR__ . '/../vendor/autoload.php';

$rabbitMqService = new RabbitMqService(
    getenv('RABBITMQ_DEFAULT_HOST'),
    getenv('RABBITMQ_DEFAULT_PORT'),
    getenv('RABBITMQ_DEFAULT_USER'),
    getenv('RABBITMQ_DEFAULT_PASS')
);


$callback = function (AMQPMessage $message) {
    echo "Received message: " . $message->body . "\n";
    $mailer = new MailerService();
    $mailer->sendEmail("OLX price subscription", $message->body);
};

$rabbitMqService->consumeMessages($callback);
