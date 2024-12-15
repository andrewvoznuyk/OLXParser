<?php

namespace App\Console\Commands;

use App\Callbacks\ProductPriceCallbackHandler;
use App\Services\RabbitMqService;
use Exception;
use Illuminate\Console\Command;

class ParsingConsumeCommand extends Command
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
    protected $signature = 'app:parsing-consume-command';

    /**
     * @var string
     */
    protected $description = 'Parsing products and updates the price if needed!';


    /**
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        echo "Consuming links to update...";
        $this->rabbitMqService->consumeMessages([app(ProductPriceCallbackHandler::class),'handle']);
    }

}
