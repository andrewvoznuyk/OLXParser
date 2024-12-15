<?php

namespace Root\App\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Root\App\Services\MailerService;

class EmailCallbackHandler
{

    /**
     * @param AMQPMessage $message
     * @return void
     */
    public function handle(AMQPMessage $message): void
    {
        echo "Received message: " . $message->body . "\n";
        $mailer = new MailerService();
        $mailer->sendEmail("OLX price subscription", $message->body);
    }
}