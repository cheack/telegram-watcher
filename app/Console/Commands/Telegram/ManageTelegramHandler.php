<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\UpdateHandlerManager;
use Illuminate\Console\Command;

class ManageTelegramHandler extends Command
{
    protected $signature = 'telegram:manage 
                            {--start : Start the Telegram handler in the background}
                            {--check : Check if the Telegram handler is running}
                            {--restart : Restart the Telegram handler process}';

    protected $description = 'Manage the Telegram handler process (start, check, restart)';

    protected UpdateHandlerManager $manager;

    public function handle(): int
    {
        $this->manager = new UpdateHandlerManager();

        if ($this->option('start')) {
            $this->startProcess();
        } elseif ($this->option('check')) {
            $this->checkProcess();
        } elseif ($this->option('restart')) {
            $this->restartProcess();
        } else {
            $this->error('Please specify an option: --start, --check, or --restart');
            return 1;
        }

        return 0;
    }

    /**
     * Start the Telegram handler process in the background.
     */
    protected function startProcess(): void
    {
        if ($this->manager->isProcessRunning()) {
            $this->info('Telegram handler is already running.');
            return;
        }

        $this->manager->startProcess();

        $this->info('Telegram handler started in the background.');
    }

    /**
     * Check if the Telegram handler process is running.
     */
    protected function checkProcess(): void
    {
        if (!$this->manager->isProcessRunning()) {
            $this->info('Telegram handler is not running.');
        } else {
            $this->info('Telegram handler is running.');
        }
    }

    /**
     * Restart the Telegram handler process.
     */
    protected function restartProcess(): void
    {
        if ($this->manager->isProcessRunning()) {
            $this->stopProcess();
        }

        $this->info('Starting the Telegram handler...');
        $this->manager->startProcess();
    }

    /**
     * Stop the Telegram handler process.
     */
    protected function stopProcess(): void
    {
        $this->info('Stopping the Telegram handler...');
        $this->manager->stopProcess();
        $this->info('Telegram handler stopped.');
    }
}