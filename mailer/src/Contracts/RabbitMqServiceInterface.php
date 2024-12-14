<?php

namespace Root\App\Contracts;
interface RabbitMqServiceInterface
{

    /**
     * @param callable $callback
     * @return void
     */
    public function consumeMessages(callable $callback): void;

    /**
     * @param string $message
     * @return void
     */
    public function sendMessage(string $message): void;

}