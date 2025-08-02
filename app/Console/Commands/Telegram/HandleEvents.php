<?php

namespace App\Console\Commands\Telegram;

use App\Services\Settings;
use danog\MadelineProto\API;
use App\Services\Telegram\UpdateHandler;
use Illuminate\Console\Command;

class HandleEvents extends Command
{
    protected $signature = 'telegram:handle';
    protected $description = 'Telegram: handle events';

    public function handle(): void
    {
        $sessions = Settings::get('telegram.sessions', []);
        if (!$sessions) {
            $this->error('No active Telegram sessions found. Please log in first.');
            return;
        }

        $MadelineProtos = [];
        foreach ($sessions as $session) {
            $MadelineProtos[] = new API('telegram_sessions/' . $session);
        }
        API::startAndLoopMulti($MadelineProtos, UpdateHandler::class);
    }
}
