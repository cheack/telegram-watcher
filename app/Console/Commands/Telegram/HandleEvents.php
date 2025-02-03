<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\UpdateHandler;
use Illuminate\Console\Command;

class HandleEvents extends Command
{
    protected $signature = 'telegram:handle';
    protected $description = 'Telegram: handle events';

    public function handle(): void
    {
        UpdateHandler::startAndLoop('session.madeline');
    }
}
