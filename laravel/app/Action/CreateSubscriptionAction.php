<?php

namespace App\Action;

use App\Models\Subscription;

class CreateSubscriptionAction
{

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

        return $subscription;
    }

}

