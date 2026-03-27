<?php

namespace App\Services\Telegram;

use Telegram\Bot\Api as BotApi;
use Telegram\Bot\Objects\Update as UpdateObject;
use Telegram\Bot\Objects\WebhookInfo;

class Api
{
    private BotApi $api;

    public function __construct() {
        $this->api = new BotApi(config('telegram.bots.notify_bot.token'));
    }

    public function sendMessage(string $message, int $chatId): int
    {
        $result = $this->api->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return $result->messageId;
    }

    public function editMessage(string $message, int $chatId, int $messageId): void
    {
        $this->api->editMessageText([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $message,
        ]);
    }

    public function getWebhookInfo(): WebhookInfo {
        return $this->api->getWebhookInfo();
    }

    public function getWebhookUpdate(): UpdateObject {
        return $this->api->getWebhookUpdate();
    }

    public function setWebhook(array $params): bool {
        return $this->api->setWebhook($params);
    }
}