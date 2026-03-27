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
    public function restartProcess(): ?int
    {
        if ($this->isProcessRunning()) {
            $this->stopProcess();

            $attempts = 0;
            while ($this->isProcessRunning() && $attempts < 10) {
                usleep(500_000);
                $attempts++;
            }
        }

        return $this->forceStartProcess();
    }

    private function forceStartProcess(): ?int
    {
        $logPath = config('telegram.log_path');
        $php = PHP_BINARY;
        $artisan = base_path('artisan');
        $command = "nohup $php $artisan telegram:handle > $logPath 2>&1 & echo $!";
        exec($command, $output);

        return isset($output[0]) ? (int) $output[0] : null;
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
        return $this->getProcessCount() > 0;
    }

    public function getProcessCount(): int
    {
        $processName = 'telegram:handle';
        $command = "ps aux | grep '[a]rtisan $processName'";
        exec($command, $output);

        return count($output);
    }

    public function getLastLines(int $numLines): array
    {
        $logPath = config('telegram.log_path');

        if (!\File::exists($logPath)) {
            return [];
        }

        return $this->readLastLines($logPath, $numLines);
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