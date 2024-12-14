<?php

namespace App\Action;

use App\Contracts\RabbitMqServiceInterface;
use App\Models\Subscription;

class CreateSubscriptionAction
{

    public function __construct(protected RabbitMqServiceInterface $rabbitMqService)
    {
    }

    /**
     * @param string $email
     * @param string $link
     * @return Subscription
     */
    public function __invoke(string $email, string $link): Subscription
    {
        $subscription = new Subscription();
        $subscription->fill([
            'email' => $email,
            'link'  => $link
        ]);
        $subscription->save();

        $this->rabbitMqService->sendMessage(json_encode([
            'message' => "You have subscribed on product with link: " . $link . "successfully",
            'email'   => $email
        ]));

        return $subscription;
    }

}

