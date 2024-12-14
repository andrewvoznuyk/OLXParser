<?php

namespace Root\App\Contracts;


interface RabbitMqServiceInterface
{
    public function consumeMessages(callable $callback): void;
    public function sendMessage(string $message): void;
}