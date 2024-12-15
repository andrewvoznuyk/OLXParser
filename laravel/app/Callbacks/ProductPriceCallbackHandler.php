<?php

namespace App\Callbacks;

use App\Action\CreateProductPriceAction;
use App\Contracts\LinkParserServiceInterface;
use App\Contracts\RabbitMqServiceInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use PhpAmqpLib\Message\AMQPMessage;

class ProductPriceCallbackHandler
{
    public function __construct(
        protected LinkParserServiceInterface $linkParserService,
        protected CreateProductPriceAction $productPriceAction,
        protected RabbitMqServiceInterface $rabbitMqService
    )
    {
    }

    /**
     * @param AMQPMessage $message
     * @return void
     */
    public function handle(AMQPMessage $message): void
    {
        $link = json_decode($message->body, true)['link'];
        echo $link . "\n";
        if (!$link){
            return;
        }

        $data = $this->linkParserService->getLinkData($link);
        if (!$data['isSucceed']){
            return;
        }

        $product = $this->getTableData('prices', $link);
        if ((float)$product->value('price') === (float)$data['price']){
            return;
        }
        echo "Price changed for $link" . "\n";
        ($this->productPriceAction)($link, $data['price']);
        foreach ($this->getSubscribedUserEmailsByProductLink($link) as $user){
            $this->rabbitMqService->sendMessage($this->encodeMessage($user, $link));
        }
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
            'message' => "Product with name: : " . $product->value('name') . " has benn updated",
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

    /**
     * @param string $productLink
     * @return array
     */
    protected function getSubscribedUserEmailsByProductLink(string $productLink): array
    {
        return DB::table('subscriptions')
            ->join('products', 'subscriptions.link', '=', 'products.link')
            ->where('products.link', $productLink)
            ->pluck('subscriptions.email')
            ->toArray();
    }

}
