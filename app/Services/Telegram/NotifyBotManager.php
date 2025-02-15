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
            '/test' => $this->testCommand(),
            '/test_notify' => $this->testNotifyCommand(),
        };
    }

    private function testCommand(): void
    {
        $this->api->sendMessage("Test message: $this->text", $this->chatId);
    }

    private function testNotifyCommand(): void
    {
        new Notifier()->sendMessage("Test notify message: $this->text");
    }

    private function getCommand(): string {
        return str($this->update->message->text)->explode(' ')->first();
    }

    private function getCommandText(): string {
        $messageParts = str($this->update->message->text)->explode(' ');
        return $messageParts->except(0)->implode(' ') ?: '[no text]';
    }
}