<?php

namespace App\Services\Telegram;

use Telegram\Bot\Objects\Update;

class NotifyBotManager
{
    private Api $api;
    private Update $update;
    private string $command;
    private string $text;
    private int $chatId;

    public function __construct(Update $update)
    {
        $this->api = new Api();
        $this->update = $update;
        $this->command = $this->getCommand();
        $this->text = $this->getCommandText();
        $this->chatId = $update->message->chat->id;
    }

    public function executeCommand(): void
    {
        match ($this->command) {
            'handler_get_status' => $this->getHandlerStatusCommand(),
            'handler_stop' => $this->getHandlerStopCommand(),
            'handler_start' => $this->getHandlerStartCommand(),
            'handler_restart' => $this->getHandlerRestartCommand(),
            'test' => $this->testCommand(),
            'test_notify' => $this->testNotifyCommand(),
            default => $this->unknownCommand(),
        };
    }

    private function getHandlerStatusCommand(): void
    {
        $manager = new UpdateHandlerManager();
        $logInfo = $manager->getLogFileInfo();
        $count = $manager->getProcessCount();

        $status = match(true) {
            $count === 0 => '🔴 <b>Not running</b>',
            $count === 1 => '🟢 <b>Running</b>',
            default => "⚠️ <b>WARNING:</b> $count processes running (zombie?)",
        };

        $lines = [];
        $lines[] = $status;

        if ($logInfo) {
            $size = number_format($logInfo['size'] / 1024, 1) . ' KB';
            $lines[] = '';
            $lines[] = "📄 <code>{$logInfo['path']}</code>";
            $lines[] = "📦 {$size} · 🕐 {$logInfo['last_modified']}";

            $lastLines = implode("\n", array_slice(
                array_filter($logInfo['last_lines'], fn($l) => trim($l) !== ''),
                -10
            ));
            $lastLines = preg_replace('/\x1B\[[0-9;]*m/', '', $lastLines);

            $lines[] = '';
            $lines[] = '<pre>' . htmlspecialchars($lastLines) . '</pre>';
        } else {
            $lines[] = "\n📄 Log not found";
        }

        $this->api->sendMessage(implode("\n", $lines), $this->chatId, 'HTML');
    }

    private function getHandlerStopCommand(): void
    {
        new UpdateHandlerManager()->stopProcess();
        $this->sendMessage('Stopped');
    }

    private function getHandlerStartCommand(): void
    {
        new UpdateHandlerManager()->startProcess();
        $this->sendMessage('Started');
    }

    private function getHandlerRestartCommand(): void
    {
        $messageId = $this->sendMessage('⏳ Restarting...');
        new UpdateHandlerManager()->restartProcess();
        $this->api->editMessage('✅ Restarted', $this->chatId, $messageId);
    }

    private function testCommand(): void
    {
        $this->sendMessage("Test message: $this->text");
    }

    private function testNotifyCommand(): void
    {
        new Notifier()->sendMessage("Test notify message: $this->text");
    }

    private function unknownCommand(): void
    {
        $this->sendMessage("Unknown command");
    }

    private function getCommand(): string {
        $command = str($this->update->message->text)->ltrim('/')->explode(' ')->first();
        return str($command)->explode('@')->first();
    }

    private function getCommandText(): string {
        $messageParts = str($this->update->message->text)->explode(' ');
        return $messageParts->except(0)->implode(' ') ?: '[no text]';
    }

    private function sendMessage(string $message): int {
        return $this->api->sendMessage($message, $this->chatId);
    }
}