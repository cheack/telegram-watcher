<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Notifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramController extends Controller
{
    public function webhook(string $token, Request $request): Response
    {
        new Notifier()->sendMessage(var_export($request->all(), true));
        return response(200);
    }
}
