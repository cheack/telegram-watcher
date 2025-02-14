<?php

namespace App\Services\Telegram;


use Telegram\Bot\Api as BotApi;

class Api
{
    private BotApi $api;

    public function __construct() {
        $this->api = new BotApi(config('telegram.bots.notify_bot.token'));
    }

    public function sendMessage(string $message, int $chatId): void
    {
        $this->api->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }

    public function setWebhook(array $params): bool {
        return $this->api->setWebhook($params);
    }
}