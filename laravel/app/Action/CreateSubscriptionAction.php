<?php

namespace App\Action;

use App\Contracts\RabbitMqServiceInterface;
use App\Models\Subscription;
use App\Services\MessageHandlerService;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionAction
{

    /**
     * @param RabbitMqServiceInterface $rabbitMqService
     */
    public function __construct(protected RabbitMqServiceInterface $rabbitMqService)
    {
    }

    /**
     * @param string $email
     * @param string $link
     * @return Subscription
     * @throws Exception
     */
    public function __invoke(string $email, string $link): Subscription
    {
        $subscription = new Subscription();
        $subscription->fill([
            'email' => $email,
            'link'  => $link
        ]);

        $subscription->save();

        $this->rabbitMqService->sendMessage(MessageHandlerService::encodeMessage($email, $link));

        return $subscription;
    }

}

