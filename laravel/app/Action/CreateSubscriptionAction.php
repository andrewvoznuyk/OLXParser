<?php

namespace App\Action;

use App\Contracts\RabbitMqServiceInterface;
use App\Models\Subscription;
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

        $this->rabbitMqService->sendMessage($this->encodeMessage($email, $link));

        return $subscription;
    }

    /**
     * @param $email
     * @param $link
     * @return false|string
     */
    protected function encodeMessage($email, $link): false|string
    {
        $productPrice = $this->getTableData('prices', $link);
        $product = $this->getTableData('products', $link);

        return json_encode([
            'message' => "You have subscribed on product with link: " . $product->value('name') . " successfully",
            'price'   => $productPrice->value('price'),
            'name'    => $product->value('name'),
            'email'   => $email
        ]);
    }

    /**
     * @param string $table
     * @param string $link
     * @return Builder
     */
    protected function getTableData(string $table, string $link): Builder
    {
        return DB::table($table)->where('link', $link)->latest();
    }

}

