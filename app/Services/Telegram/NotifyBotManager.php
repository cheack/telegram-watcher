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
        $message = match(true) {
            $count === 0 => 'Not running',
            $count === 1 => 'Running',
            default => "WARNING: $count processes running (zombie?)",
        };
        $message .= "\nLog:" . var_export($logInfo, true);
        $this->sendMessage($message);
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
        new UpdateHandlerManager()->restartProcess();
        $this->sendMessage('Restarted');
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

    private function sendMessage(string $message): void {
        $this->api->sendMessage($message, $this->chatId);
    }
}