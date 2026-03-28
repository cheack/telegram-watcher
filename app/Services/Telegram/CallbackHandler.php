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
        $manager = new UpdateHandlerManager();
        $count = $manager->getProcessCount();
        $logInfo = $manager->getLogFileInfo();

        $status = match (true) {
            $count === 0 => '🔴 Not running',
            $count === 1 => '🟢 Running',
            default => "⚠️ {$count} processes",
        };

        $lines = ["<b>Handler:</b> {$status}"];

        if ($logInfo) {
            $size = number_format($logInfo['size'] / 1024, 1) . ' KB';
            $lines[] = "📦 {$size} · 🕐 {$logInfo['last_modified']}";

            $lastLines = implode("\n", array_slice(
                array_filter($logInfo['last_lines'], fn($l) => trim($l) !== ''),
                -8
            ));
            $lastLines = preg_replace('/\x1B\[[0-9;]*m/', '', $lastLines);
            $lines[] = '<pre>' . htmlspecialchars($lastLines) . '</pre>';
        }

        $this->api->answerCallbackQuery($this->callbackQueryId);
        $this->api->sendMessage(implode("\n", $lines), $this->chatId, 'HTML');
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
