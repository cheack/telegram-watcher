<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\Notifier;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';
    protected $description = 'Telegram: set webhook';

    public function handle(): void
    {
        $token = config('telegram.bots.notify_bot.token');
        $url = str(config('telegram.bots.notify_bot.webhook_url'))
            ->replace('<token>', $token)->toString();
        $api = new Api(config('telegram.bots.notify_bot.token'));
        $result = $api->setWebhook(['url' => $url]);

        if ($result) {
            $this->info("Telegram: webhook URL $url set successfully.");
        } else {
            $this->error("Telegram: webhook URL $url set failed.");
        }
    }
}
