<?php

namespace App\Jobs;

use App\Services\Telegram\UpdateHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleTelegramUpdates implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        UpdateHandler::startAndLoop('session.madeline');
    }
}
