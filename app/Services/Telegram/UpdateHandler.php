<?php declare(strict_types=1);

namespace App\Services\Telegram;

use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\Channel\UpdateChannel;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\SimpleEventHandler;

class UpdateHandler extends SimpleEventHandler
{
    #[Handler]
    public function handleChannel(UpdateChannel $message): void
    {
//        \File::append(storage_path('telegram.log'), json_encode($message->jsonSerialize(), JSON_PRETTY_PRINT) . "\n\n");

        $chat = $this->getChatInfo($message->chatId);
        new Notifier()->sendMessage('Новый канал - ' . $chat['Chat']['title']);
//        \File::append(storage_path('telegram.log'), json_encode($updates, JSON_PRETTY_PRINT) . "\n\n");
//        $chat = $MadelineProto->getInfo($message->chatId);
//        new Notifier()->sendMessage('Новый канал - ' . $chat['Chat']['title']);
    }

    #[Handler]
    public function handlePrivateMessage(Incoming & PrivateMessage $message): void
    {
//        \File::append(storage_path('telegram.log'), json_encode($message->jsonSerialize(), JSON_PRETTY_PRINT) . "\n\n");

        $chat = $this->getChatInfo($message->chatId);

        if (!$chat['User']['contact']) {
            $contact = $chat['User']['first_name'] . ' @' . $chat['User']['username'];
            $notifier = new Notifier();
            if ($message->media) {
                $notifier->sendMessage("Новое медиа от неизвестного контакта $contact");
            } else {
                $notifier->sendMessage("Новое сообщение от неизвестного контакта $contact:\n\n" . $message->message);
            }
        }
    }

    private function getChatInfo(int $chatId): array
    {
        $settings = new AppInfo()
            ->setApiId((int)config('telegram.app.id'))
            ->setApiHash(config('telegram.app.hash'));
        $MadelineProto = new API('session.madeline', $settings);

        return $MadelineProto->getInfo($chatId);
    }
}