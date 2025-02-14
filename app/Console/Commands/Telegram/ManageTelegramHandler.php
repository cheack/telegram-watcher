<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\UpdateHandler;
use Illuminate\Console\Command;

class ManageTelegramHandler extends Command
{
    protected $signature = 'telegram:manage 
                            {--start : Start the Telegram handler in the background}
                            {--check : Check if the Telegram handler is running}
                            {--restart : Restart the Telegram handler process}';

    protected $description = 'Manage the Telegram handler process (start, check, restart)';

    public function handle()
    {
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
    protected function startProcess()
    {
        $command = 'nohup php artisan telegram:handle > /tmp/telegram.log 2>&1 &';
        exec($command);

        $this->info('Telegram handler started in the background.');
    }

    /**
     * Check if the Telegram handler process is running.
     */
    protected function checkProcess()
    {
        if (!$this->isProcessRunning()) {
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
        if ($this->isProcessRunning()) {
            $this->info('Stopping the Telegram handler...');
            $this->stopProcess();
        }

        $this->info('Starting the Telegram handler...');
        $this->startProcess();
    }

    /**
     * Stop the Telegram handler process.
     */
    protected function stopProcess(): void
    {
        $processName = 'telegram:handle';
        $command = "ps aux | grep '[a]rtisan $processName' | awk '{print $2}' | xargs kill -9";
        exec($command);

        $this->info('Telegram handler stopped.');
    }

    /**
     * Check if the Telegram handler process is running.
     *
     * @return bool
     */
    protected function isProcessRunning(): bool
    {
        $processName = 'telegram:handle';
        $command = "ps aux | grep '[a]rtisan $processName'";
        exec($command, $output, $returnVar);

        return !empty($output);
    }
}