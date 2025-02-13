<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Notifier;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function webhook(string $token, Request $request): void
    {
        new Notifier()->sendMessage(var_export($request->all(), true));
    }
}
