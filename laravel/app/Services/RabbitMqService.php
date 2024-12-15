<?php

namespace App\Services;

use App\Contracts\RabbitMqServiceInterface;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqService implements RabbitMqServiceInterface
{

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private string $queueName;

    /**
     * @param $rabbitHost
     * @param $rabbitPort
     * @param $rabbitUser
     * @param $rabbitPassword
     * @param string $queueName
     * @throws Exception
     */
    public function __construct($rabbitHost, $rabbitPort, $rabbitUser, $rabbitPassword, string $queueName = 'send-email')
    {
        $this->queueName = $queueName;

        $this->connection = new AMQPStreamConnection($rabbitHost, $rabbitPort, $rabbitUser, $rabbitPassword);
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
    }

    /**
     * @param callable $callback
     * @return void
     * @throws Exception
     */
    public function consumeMessages(callable $callback): void
    {
        $this->channel->basic_consume($this->queueName, '', false, true, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @param string $message
     * @return void
     * @throws Exception
     */
    public function sendMessage(string $message): void
    {
        $amqpMessage = new AMQPMessage($message, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);

        $this->channel->basic_publish($amqpMessage, '', $this->queueName);
    }

}
