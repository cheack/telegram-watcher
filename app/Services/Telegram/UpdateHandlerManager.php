<?php

namespace App\Services\Telegram;

class UpdateHandlerManager
{
    public function getLogFileInfo(): array|false
    {
        $logPath = config('telegram.log_path');

        if (!\File::exists($logPath)) {
            return false;
        }

        return [
            'path' => $logPath,
            'size' => \File::size($logPath),
            'last_modified' => date('Y-m-d H:i:s', \File::lastModified($logPath)),
            'last_lines' => $this->readLastLines($logPath, 25),
        ];
    }

    /**
     * Start the Telegram handler process in the background.
     */
    public function startProcess(): void
    {
        if ($this->isProcessRunning()) {
            return;
        }

        $logPath = config('telegram.log_path');
        $command = "nohup php artisan telegram:handle > $logPath 2>&1 &";
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

    protected function readLastLines(string $filePath, int $numLines): array
    {
        $file = new \SplFileObject($filePath, 'r');
        $file->seek(PHP_INT_MAX);
        $lineCount = $file->key();
        $startLine = max(0, $lineCount - $numLines);

        $lines = [];
        $file->seek($startLine);
        while (!$file->eof()) {
            $lines[] = rtrim($file->current());
            $file->next();
        }

        return $lines;
    }
}