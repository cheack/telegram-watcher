<?php

namespace App\Console\Commands\Telegram;

use App\Services\Settings;
use App\Services\Telegram\Notifier;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Tools;
use Illuminate\Console\Command;

class Login extends Command
{
    protected $signature = 'telegram:login
                           {session_name : Session name}';

    protected $description = 'Telegram: login';

    public function handle(): void
    {
        $sessionName = $this->argument('session_name');
        $settings = new AppInfo()
            ->setApiId((int)config('telegram.app.id'))
            ->setApiHash(config('telegram.app.hash'));
        $MadelineProto = new API('telegram_sessions/' . $sessionName, $settings);

        $MadelineProto->phoneLogin(Tools::readLine('Enter your phone number: '));
        $authorization = $MadelineProto->completePhoneLogin(Tools::readLine('Enter the phone code: '));
        if ($authorization['_'] === 'account.password') {
            $MadelineProto->complete2faLogin(Tools::readLine('Please enter your password (hint '.$authorization['hint'].'): '));
        }

        $sessions = Settings::get('telegram.sessions', []);
        $sessions[] = $sessionName;
        Settings::set('telegram.sessions', $sessions);
    }
}
