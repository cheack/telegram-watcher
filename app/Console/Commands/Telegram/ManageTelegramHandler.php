<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\UpdateHandlerManager;
use Illuminate\Console\Command;

class ManageTelegramHandler extends Command
{
    protected $signature = 'telegram:manage 
                            {--start : Start the Telegram handler in the background}
                            {--stop : Stop the Telegram handler process}
                            {--restart : Restart the Telegram handler process}
                            {--status : Check if the Telegram handler is running}';

    protected $description = 'Manage the Telegram handler process (start, check, restart)';

    protected UpdateHandlerManager $manager;

    public function handle(): int
    {
        $this->manager = new UpdateHandlerManager();

        if ($this->option('start')) {
            $this->startProcess();
        } elseif ($this->option('status')) {
            $this->checkProcess();
        } elseif ($this->option('restart')) {
            $this->restartProcess();
        } elseif ($this->option('stop')) {
            $this->stopProcess();
        } else {
            $this->error('Please specify an option: --start, --stop, --restart, or --status');
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

        $this->showLogInfo();
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

    protected function showLogInfo(): void
    {
        $info = $this->manager->getLogFileInfo();
        if (!$info) {
            $this->error("Can't get log file info.");
        }

        $this->info("Log file: {$info['path']}");
        $this->line("Size: {$info['size']} bytes.");
        $this->line("Last Modified: {$info['last_modified']}");
        $this->info("\nLast 25 lines of the log:");
        $this->line(implode("\n", $info['last_lines']));
    }
}
