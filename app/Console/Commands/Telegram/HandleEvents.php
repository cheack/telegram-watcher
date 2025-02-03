<?php

namespace App\Console\Commands\Telegram;

use App\Jobs\HandleTelegramUpdates;
use Illuminate\Console\Command;

class HandleEvents extends Command
{
    protected $signature = 'telegram:handle';
    protected $description = 'Telegram: handle events';

    public function handle(): void
    {
        HandleTelegramUpdates::dispatch();
    }
}
