<?php

namespace App\Services\Telegram;

class UpdateHandlerManager
{
    private const SERVICE = 'telegram-handler';

    public function startProcess(): void
    {
        exec('sudo /usr/bin/systemctl start ' . self::SERVICE);
    }

    public function stopProcess(): void
    {
        exec('sudo /usr/bin/systemctl stop ' . self::SERVICE);
    }

    public function restartProcess(): void
    {
        exec('sudo /usr/bin/systemctl restart ' . self::SERVICE);
    }

    public function isProcessRunning(): bool
    {
        exec('/usr/bin/systemctl is-active ' . self::SERVICE, $out, $code);
        return $code === 0;
    }

    public function getProcessCount(): int
    {
        return $this->isProcessRunning() ? 1 : 0;
    }

    public function getPid(): ?int
    {
        exec('/usr/bin/systemctl show ' . self::SERVICE . ' --property=MainPID --value', $out);
        $pid = (int) trim($out[0] ?? '0');
        return $pid > 0 ? $pid : null;
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
