<?php

namespace App\Callbacks;

use App\Action\CreateProductPriceAction;
use App\Contracts\LinkParserServiceInterface;
use App\Contracts\RabbitMqServiceInterface;
use App\Services\MessageHandlerService;
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

        ($this->productPriceAction)($link, $data['price']);
        foreach ($this->getSubscribedUserEmailsByProductLink($link) as $email){
            $this->rabbitMqService->sendMessage(MessageHandlerService::encodeMessage($email, $link));
        }
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
