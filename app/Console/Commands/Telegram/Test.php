<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\Notifier;
use Illuminate\Console\Command;

class Test extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Telegram: test message';

    public function handle(): void
    {
        new Notifier()->sendMessage('test');
    }
}
