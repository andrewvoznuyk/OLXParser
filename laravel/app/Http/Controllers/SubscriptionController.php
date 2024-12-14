<?php

namespace App\Http\Controllers;

use App\Action\CreateProductAction;
use App\Action\CreateSubscriptionAction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController
{

    /**
     * @param CreateSubscriptionAction $subscriptionAction
     * @param CreateProductAction $productAction
     */
    public function __construct(
        protected CreateSubscriptionAction $subscriptionAction,
        protected CreateProductAction      $productAction,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function subscribe(Request $request): JsonResponse
    {
        $link = $request->input('link');
        $email = $request->input('email');

        if ($this->subscriptionExists($email, $link)) {
            return new JsonResponse(['message' => 'Subscription already exists.'], 201);
        }

        ($this->subscriptionAction)($email, $link);

        return new JsonResponse(['message' => 'Subscription created successfully.'], 201);
    }

    /**
     * @param string $email
     * @param string $link
     * @return bool
     */
    protected function subscriptionExists(string $email, string $link): bool
    {
        return DB::table('subscriptions')->where([
            'email' => $email,
            'link'  => $link
        ])->exists();
    }

}

