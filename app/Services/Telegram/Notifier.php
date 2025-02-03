<?php

namespace App\Services\Telegram;

use Telegram\Bot\Api;

class Notifier
{
    private Api $api;

    public function __construct() {
        $this->api = new Api(config('telegram.bots.notify_bot.token'));
    }

    public function sendMessage(string $message): void
    {
        $this->api->sendMessage([
            'chat_id' => config('telegram.bots.notify_bot.group_id'),
            'text' => $message,
        ]);
    }
}