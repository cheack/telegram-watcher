<?php declare(strict_types=1);

namespace App\Services\Telegram;

use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\Channel\UpdateChannel;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\SimpleEventHandler;

class UpdateHandler extends SimpleEventHandler
{
    #[Handler]
    public function handleChannel(UpdateChannel $message): void
    {
        $chat = $this->getChatInfo($message->chatId);
        $left = !empty($chat['Chat']['left']);

        $cacheKey = "channel_update_{$message->chatId}_" . ($left ? 'left' : 'joined');
        if (\Cache::has($cacheKey)) {
            return;
        }
        \Cache::put($cacheKey, true, 10);

        $title = $chat['Chat']['title'];
        $username = $chat['Chat']['username'] ?? null;
        $link = $username ? "https://t.me/{$username}" : null;
        $titleText = $link ? "<a href=\"{$link}\">{$title}</a>" : "<b>{$title}</b>";

        if ($left) {
            $this->notifyHtml("🚪 Покинут канал — {$titleText}");
        } else {
            $this->notifyHtml("📡 Новый канал — {$titleText}");
        }
    }

    #[Handler]
    public function handlePrivateMessage(Incoming & PrivateMessage $message): void
    {
//        \File::append(storage_path('telegram.log'), json_encode($message->jsonSerialize(), JSON_PRETTY_PRINT) . "\n\n");

        $chat = $this->getChatInfo($message->chatId);

        if (!$chat['User']['contact']) {
            $contact = $chat['User']['first_name'] . ' @' . $chat['User']['username'];
            if ($message->media) {
                $this->notify("Новое медиа от неизвестного контакта $contact");
            } else {
                $this->notify("Новое сообщение от неизвестного контакта $contact:\n\n" . $message->message);
            }
        }
    }

    private function getChatInfo(int $chatId): array
    {
        return $this->getInfo($chatId);
    }

    private function getSession(): string
    {
        return 'telegram_sessions/' . basename($this->getSessionName());
    }

    private function notify(string $message): void
    {
        $session = $this->getSession();
        try {
            new Notifier()->sendMessage("$session: $message");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Notifier failed: ' . $e->getMessage());
        }
    }

    private function notifyHtml(string $message): void
    {
        $session = $this->getSession();
        try {
            new Notifier()->sendMessage("<code>{$session}</code>: $message", 'HTML');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Notifier failed: ' . $e->getMessage());
        }
    }
}