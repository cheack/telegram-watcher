<?php

namespace App\Services\Telegram;

use Telegram\Bot\Objects\Update;

class CallbackHandler
{
    private Api $api;
    private string $callbackQueryId;
    private string $data;
    private int $chatId;

    public function __construct(Update $update)
    {
        $this->api = new Api();
        $this->callbackQueryId = $update->callbackQuery->id;
        $this->data = $update->callbackQuery->data ?? '';
        $this->chatId = $update->callbackQuery->message->chat->id;
    }

    public function handle(): void
    {
        match ($this->data) {
            'panel:status' => $this->handleStatus(),
            'panel:start' => $this->handleStart(),
            'panel:stop' => $this->handleStop(),
            'panel:restart' => $this->handleRestart(),
            default => $this->api->answerCallbackQuery($this->callbackQueryId, '❓ Unknown action'),
        };
    }

    private function handleStatus(): void
    {
        $this->api->answerCallbackQuery($this->callbackQueryId);
        $message = (new UpdateHandlerManager())->buildStatusMessage();
        $this->api->sendMessage($message, $this->chatId, 'HTML');
    }

    private function handleStart(): void
    {
        $manager = new UpdateHandlerManager();
        if ($manager->isProcessRunning()) {
            $this->api->answerCallbackQuery($this->callbackQueryId, '⚠️ Already running', true);
            return;
        }
        $manager->startProcess();
        $this->api->answerCallbackQuery($this->callbackQueryId, '✅ Started');
    }

    private function handleStop(): void
    {
        $manager = new UpdateHandlerManager();
        if (!$manager->isProcessRunning()) {
            $this->api->answerCallbackQuery($this->callbackQueryId, '⚠️ Already stopped', true);
            return;
        }
        $manager->stopProcess();
        $this->api->answerCallbackQuery($this->callbackQueryId, '✅ Stopped');
    }

    private function handleRestart(): void
    {
        $this->api->answerCallbackQuery($this->callbackQueryId, '⏳ Restarting...');
        (new UpdateHandlerManager())->restartProcess();
        $this->api->sendMessage('✅ Handler restarted', $this->chatId);
    }
}
