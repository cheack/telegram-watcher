<?php

namespace App\Services\Telegram;

class UpdateHandlerManager
{
    /**
     * Start the Telegram handler process in the background.
     */
    public function startProcess(): void
    {
        if ($this->isProcessRunning()) {
            return;
        }

        $logFile = storage_path('logs/telegram.log');
        $command = "nohup php artisan telegram:handle > $logFile 2>&1 &";
        exec($command);
    }

    /**
     * Restart the Telegram handler process.
     */
    public function restartProcess(): void
    {
        if ($this->isProcessRunning()) {
            $this->stopProcess();
        }

        $this->startProcess();
    }

    /**
     * Stop the Telegram handler process.
     */
    public function stopProcess(): void
    {
        $processName = 'telegram:handle';
        $command = "ps aux | grep '[a]rtisan $processName' | awk '{print $2}' | xargs kill -9";
        exec($command);
    }

    /**
     * Check if the Telegram handler process is running.
     */
    public function isProcessRunning(): bool
    {
        $processName = 'telegram:handle';
        $command = "ps aux | grep '[a]rtisan $processName'";
        exec($command, $output, $returnVar);

        return !empty($output);
    }
}