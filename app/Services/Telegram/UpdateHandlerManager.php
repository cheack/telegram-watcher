<?php

namespace App\Services\Telegram;

class UpdateHandlerManager
{
    private const PROGRAM = 'telegram-handler';
    private const CTL = 'supervisorctl -c /etc/supervisor/supervisord.conf';

    public function startProcess(): void
    {
        exec(self::CTL . ' start ' . self::PROGRAM);
    }

    public function stopProcess(): void
    {
        exec(self::CTL . ' stop ' . self::PROGRAM);
    }

    public function restartProcess(): void
    {
        exec(self::CTL . ' restart ' . self::PROGRAM);
    }

    public function isProcessRunning(): bool
    {
        exec(self::CTL . ' status ' . self::PROGRAM, $out);
        return str_contains($out[0] ?? '', 'RUNNING');
    }

    public function getProcessCount(): int
    {
        return $this->isProcessRunning() ? 1 : 0;
    }

    public function getPid(): ?int
    {
        exec(self::CTL . ' status ' . self::PROGRAM, $out);
        // Output: telegram-handler         RUNNING   pid 42, uptime 0:01:23
        if (preg_match('/pid (\d+)/', $out[0] ?? '', $m)) {
            return (int) $m[1];
        }
        return null;
    }

    public function getProcessIds(): array
    {
        $pid = $this->getPid();
        return $pid ? [$pid] : [];
    }

    public function getLogFileInfo(): array|false
    {
        $logPath = config('telegram.log_path');

        if (!\File::exists($logPath)) {
            return false;
        }

        return [
            'path'          => $logPath,
            'size'          => \File::size($logPath),
            'last_modified' => date('Y-m-d H:i:s', \File::lastModified($logPath)),
            'last_lines'    => $this->readLastLines($logPath, 25),
        ];
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
