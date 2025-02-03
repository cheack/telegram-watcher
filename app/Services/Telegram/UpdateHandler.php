<?php declare(strict_types=1);

namespace App\Services\Telegram;

use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\Channel\UpdateChannel;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\SimpleEventHandler;

class UpdateHandler extends SimpleEventHandler
{
    #[Handler]
    public function handleMessage(UpdateChannel $message): void
    {
        $settings = new AppInfo()
            ->setApiId((int)config('telegram.app.id'))
            ->setApiHash(config('telegram.app.hash'));
        $MadelineProto = new API('session.madeline', $settings);

        $chat = $MadelineProto->getInfo($message->chatId);
        new Notifier()->sendMessage('Новый канал - ' . $chat['Chat']['title']);
    }
}