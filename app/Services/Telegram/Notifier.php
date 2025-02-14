<?php

namespace App\Services\Telegram;

class Notifier
{
    private Api $api;

    public function __construct() {
        $this->api = new Api();
    }

    public function sendMessage(string $message): void
    {
        $this->api->sendMessage($message, config('telegram.bots.notify_bot.group_id'));
    }
}