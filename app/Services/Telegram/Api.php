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

    public function sendMessage(string $message, int $chatId, string $parseMode = ''): int
    {
        $params = ['chat_id' => $chatId, 'text' => $message];
        if ($parseMode) {
            $params['parse_mode'] = $parseMode;
        }

        return $this->api->sendMessage($params)->messageId;
    }

    public function editMessage(string $message, int $chatId, int $messageId, string $parseMode = ''): void
    {
        $params = ['chat_id' => $chatId, 'message_id' => $messageId, 'text' => $message];
        if ($parseMode) {
            $params['parse_mode'] = $parseMode;
        }

        $this->api->editMessageText($params);
    }

    public function getWebhookInfo(): WebhookInfo {
        return $this->api->getWebhookInfo();
    }

    public function getWebhookUpdate(): UpdateObject {
        return $this->api->getWebhookUpdate();
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): void
    {
        $this->api->answerCallbackQuery([
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    public function setWebhook(array $params): bool {
        return $this->api->setWebhook($params);
    }
}