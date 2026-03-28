<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Api;
use App\Services\Telegram\CallbackHandler;
use App\Services\Telegram\NotifyBotManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramController extends Controller
{
    public function webhook(string $token, Request $request): Response
    {
        $api = new Api();
        $update = $api->getWebhookUpdate();

        if ($update->callbackQuery) {
            (new CallbackHandler($update))->handle();
        } elseif ($update->message?->hasCommand()) {
            (new NotifyBotManager($update))->executeCommand();
        }

        return response(200);
    }
}
