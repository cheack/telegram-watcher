<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\Api;
use Illuminate\Console\Command;

class Webhook extends Command
{
    protected $signature = 'telegram:webhook
                        {operation? : set/info}';
    protected $description = 'Telegram: set webhook / get info';

    public function handle(): void
    {
        $command = $this->argument('operation');
        $api = new Api();

        if (!$command || $command === 'info') {
            $info = $api->getWebhookInfo();
            $this->info($info->toJson(JSON_PRETTY_PRINT));
        } elseif ($command === 'set') {
            $token = config('telegram.bots.notify_bot.token');
            $url = str(config('telegram.bots.notify_bot.webhook_url'))
                ->replace('<token>', $token)->toString();

            $result = $api->setWebhook(['url' => $url]);

            if ($result) {
                $this->info("Telegram: webhook URL $url set successfully.");
            } else {
                $this->error("Telegram: webhook URL $url set failed.");
            }
        } else {
            $this->fail('Usage:' . $this->signature);
        }
    }
}
