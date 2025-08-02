<?php
namespace App\Console\Commands\Telegram;

use danog\MadelineProto\API;
use Illuminate\Console\Command;

class Logout extends Command
{
    protected $signature = 'telegram:logout
                           {session_name : Session name}';

    protected $description = 'Telegram: logout';

    public function handle(): void
    {
        $sessionName = $this->argument('session_name');
        new API($sessionName)->logout();
    }
}
