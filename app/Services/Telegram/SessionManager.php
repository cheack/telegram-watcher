<?php

namespace App\Services\Telegram;

use App\Services\Settings;

class SessionManager
{
    private string $sessionsPath;

    public function __construct()
    {
        $this->sessionsPath = base_path('telegram_sessions');
    }

    public function getSessions(): array
    {
        $activeSessions = Settings::get('telegram.sessions', []);
        $dirs = glob($this->sessionsPath . '/*', GLOB_ONLYDIR);
        $sessions = array_map(fn($dir) => $this->getSessionInfo($dir, $activeSessions), $dirs);
        $internalNames = $this->resolveInternalNames($sessions);

        return array_map(fn($session) => array_merge($session, [
            'internal_name' => $internalNames[$session['name']] ?? null,
        ]), $sessions);
    }

    /**
     * Map folder names to MadelineProto internal logger names.
     * Only active (logged-in) sessions are matched; inactive ones are ignored.
     */
    private function resolveInternalNames(array $sessions): array
    {
        $logPath = config('telegram.log_path');
        if (!file_exists($logPath)) {
            return [];
        }

        preg_match_all('/^[^,]+, (\w+):/m', file_get_contents($logPath), $matches);
        $internalNames = array_unique($matches[1]);

        $activeFolders = array_column(array_filter($sessions, fn($s) => $s['logged_in']), 'name');

        $mapping = [];
        $unmatched = [];

        foreach ($activeFolders as $folder) {
            $found = null;
            foreach ($internalNames as $internalName) {
                if ($internalName === $folder || str_starts_with($internalName, $folder . '_')) {
                    $found = $internalName;
                    break;
                }
            }
            if ($found !== null) {
                $mapping[$folder] = $found;
            } else {
                $unmatched[] = $folder;
            }
        }

        $remaining = array_values(array_diff($internalNames, array_values($mapping)));

        foreach ($unmatched as $folder) {
            $name = array_shift($remaining);
            if ($name) {
                $mapping[$folder] = $name;
            }
        }

        return $mapping;
    }

    private function getSessionInfo(string $dir, array $activeSessions = []): array
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
            'internal_name' => null,
            'logged_in' => $loggedIn,
            'active' => in_array($name, $activeSessions),
            'size' => $size,
            'updated_at' => $updatedAt,
            'event_handler' => $eventHandler,
        ];
    }
}
