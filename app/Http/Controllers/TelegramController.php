<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Api;
use App\Services\Telegram\Notifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramController extends Controller
{
    public function webhook(string $token, Request $request): Response
    {
        $api = new Api();
        $update = $api->getWebhookUpdate();
        if ($update->message->hasCommand()) {
            $messageParts = str($update->message->text)->explode(' ');
            $command = $messageParts->first();
            $text = $messageParts->except(0)->implode(' ') ?: '[no text]';

            if ($command === '/test') {
                $api->sendMessage("Test message: $text", $update->message->chat->id);
            } elseif ($command === '/test_notify') {
                new Notifier()->sendMessage("Test notify message: $text");
            }
        }

        return response(200);
    }
}
