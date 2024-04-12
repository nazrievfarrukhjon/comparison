<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RabbitMqConsumerCommand extends Command
{

    protected $signature = 'consume';

    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            RabbitMQConsumer::init()
                ->consumeFromRegularQueue();
        } catch (Exception $e) {
            Log::info(['temp_start_rabbitmq', $e->getMessage()]);
        }

    }

}
