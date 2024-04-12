<?php

namespace App\Console\Commands;

use App\Modules\Telegram\TelegramMessage;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TelegramPusherCommand extends Command
{
    protected $signature = 'sms_telegram';

    protected $description = 'send messages from telegrams table';

    private int $counter = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        while (true) {
            $telegrams = TelegramMessage::query()
                ->limit(10)
                ->get()
                ->each(function ($telegram) {
                    try {
                        $guzzle = new Guzzle(['http_errors' => false]);
                        $response = $guzzle->post(
                            $this->url(),
                            [
                                'form_params' => [
                                    'chat_id' => config(
                                        'environments.BL_CHAT_ID'
                                    ),
                                    'text' => $telegram->body,
                                ]
                            ]
                        );

                        if ($response->getStatusCode() === 200) {
                            $telegram->delete();
                        }
                    } catch (GuzzleException $e) {
                        Log::info('GuzzleException telegram messaging exception: ', [$e->getMessage()]);
                    } catch (\Exception $e) {
                        Log::info('telegram messaging exception: ', [$e->getMessage()]);
                    }
                    if ((($this->counter++) % 4) === 0) {
                        sleep(1);
                    }
                });

            if ($telegrams->isEmpty()) {
                sleep(10);
            }
        }
    }

    private function url(): string
    {
        return 'https://api.telegram.org/bot' . config(
                'environments.TELEGRAM_BOT_TOKEN'
            ) . '/sendMessage';
    }
}
