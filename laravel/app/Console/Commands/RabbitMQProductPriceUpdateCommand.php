<?php

namespace App\Console\Commands;

use App\Services\RabbitMqService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RabbitMQProductPriceUpdateCommand extends Command
{

    /**
     * @var RabbitMqService
     */
    protected RabbitMqService $rabbitMqService;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->rabbitMqService = new RabbitMqService(
            rabbitHost: getenv('RABBITMQ_DEFAULT_HOST'),
            rabbitPort: getenv('RABBITMQ_DEFAULT_PORT'),
            rabbitUser: getenv('RABBITMQ_DEFAULT_USER'),
            rabbitPassword: getenv('RABBITMQ_DEFAULT_PASS'),
            queueName: 'update-product'
        );
        parent::__construct();
    }

    /**
     * @var string
     */
    protected $signature = 'app:rabbit-m-q-product-price-update-command';

    /**
     * @var string
     */
    protected $description = 'Regular sending command to update product prices';


    /**
     * @throws Exception
     */
    public function handle(): void
    {
        foreach (DB::table('products')->get('link')->all() as $product){
            $message = json_encode(['link' => $product->link]);
            $this->rabbitMqService->sendMessage($message);
        }
    }

}
