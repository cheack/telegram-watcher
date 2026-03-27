<?php

namespace App\Services\Telegram;

class SessionManager
{
    private string $sessionsPath;

    public function __construct()
    {
        $this->sessionsPath = base_path('telegram_sessions');
    }

    public function getSessions(): array
    {
        $dirs = glob($this->sessionsPath . '/*', GLOB_ONLYDIR);

        return array_map(fn($dir) => $this->getSessionInfo($dir), $dirs);
    }

    private function getSessionInfo(string $dir): array
    {
        $name = basename($dir);
        $safeFile = $dir . '/safe.php';
        $lightStateFile = $dir . '/lightState.php';

        $loggedIn = file_exists($safeFile) && filesize($safeFile) > 0;
        $size = $loggedIn ? filesize($safeFile) : 0;
        $updatedAt = $loggedIn ? date('Y-m-d H:i:s', filemtime($safeFile)) : null;

        $eventHandler = null;
        if (file_exists($lightStateFile)) {
            $content = file_get_contents($lightStateFile);
            if (preg_match('/eventHandler#(\S+)/', $content, $matches)) {
                $eventHandler = $matches[1];
            }
        }

        return [
            'name' => $name,
            'logged_in' => $loggedIn,
            'size' => $size,
            'updated_at' => $updatedAt,
            'event_handler' => $eventHandler,
        ];
    }
}
