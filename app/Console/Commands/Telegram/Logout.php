<?php
namespace App\Console\Commands\Telegram;

use App\Services\Settings;
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
        new API('telegram_sessions/' . $sessionName)->logout();

        $sessions = Settings::get('telegram.sessions', []);
        unset($sessions[$sessionName]);
        Settings::set('telegram.sessions', $sessions);
    }
}
