<?php

namespace App\Http\Controllers;

use App\Services\Settings;
use App\Services\Telegram\SessionManager;
use App\Services\Telegram\UpdateHandlerManager;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings\AppInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(): View
    {
        $sessions = (new SessionManager())->getSessions();

        return view('session.index', compact('sessions'));
    }

    public function loginStart(Request $request): JsonResponse
    {
        $request->validate([
            'session_name' => ['required', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'phone' => ['required', 'string'],
        ], [
            'session_name.required' => __('validation.session_name_required'),
            'session_name.regex'    => __('validation.session_name_format'),
            'phone.required'        => __('validation.phone_required'),
        ]);

        $name = $request->input('session_name');
        $phone = $request->input('phone');
        $path = base_path('telegram_sessions/' . $name);

        if (is_dir($path) && file_exists($path . '/safe.php') && filesize($path . '/safe.php') > 0) {
            return response()->json(['error' => __('Session already exists and is logged in.')], 422);
        }

        try {
            $settings = new AppInfo()
                ->setApiId((int)config('telegram.app.id'))
                ->setApiHash(config('telegram.app.hash'));

            $api = new API($path, $settings);
            $api->phoneLogin($phone);

            session(['pending_session' => $name]);

            return response()->json(['step' => 'code']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function loginVerify(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $name = session('pending_session');
        if (!$name) {
            return response()->json(['error' => __('Session not found. Please start over.')], 422);
        }

        $path = base_path('telegram_sessions/' . $name);

        try {
            $settings = new AppInfo()
                ->setApiId((int)config('telegram.app.id'))
                ->setApiHash(config('telegram.app.hash'));

            $api = new API($path, $settings);
            $authorization = $api->completePhoneLogin($request->input('code'));

            if (isset($authorization['_']) && $authorization['_'] === 'account.password') {
                return response()->json([
                    'step' => '2fa',
                    'hint' => $authorization['hint'] ?? '',
                ]);
            }

            $this->finalizeSession($name);

            return response()->json(['step' => 'done']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function login2fa(Request $request): JsonResponse
    {
        $request->validate(['password' => ['required', 'string']]);

        $name = session('pending_session');
        if (!$name) {
            return response()->json(['error' => __('Session not found. Please start over.')], 422);
        }

        $path = base_path('telegram_sessions/' . $name);

        try {
            $settings = new AppInfo()
                ->setApiId((int)config('telegram.app.id'))
                ->setApiHash(config('telegram.app.hash'));

            $api = new API($path, $settings);
            $api->complete2faLogin($request->input('password'));

            $this->finalizeSession($name);

            return response()->json(['step' => 'done']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function pause(string $name): JsonResponse
    {
        $sessions = Settings::get('telegram.sessions', []);
        $sessions = array_values(array_filter($sessions, fn($s) => $s !== $name));
        Settings::set('telegram.sessions', $sessions);

        $manager = new UpdateHandlerManager();
        if ($manager->isProcessRunning()) {
            $manager->restartProcess();
        }

        return response()->json(['ok' => true]);
    }

    public function resume(string $name): JsonResponse
    {
        $sessions = Settings::get('telegram.sessions', []);
        if (!in_array($name, $sessions)) {
            $sessions[] = $name;
            Settings::set('telegram.sessions', $sessions);
        }

        $manager = new UpdateHandlerManager();
        if ($manager->isProcessRunning()) {
            $manager->restartProcess();
        }

        return response()->json(['ok' => true]);
    }

    public function delete(string $name): JsonResponse
    {
        $sessions = Settings::get('telegram.sessions', []);
        $sessions = array_values(array_filter($sessions, fn($s) => $s !== $name));
        Settings::set('telegram.sessions', $sessions);

        $path = base_path('telegram_sessions/' . $name);
        if (is_dir($path) && str_starts_with(realpath($path), realpath(base_path('telegram_sessions')))) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
            }
            rmdir($path);
        }

        $manager = new UpdateHandlerManager();
        if ($manager->isProcessRunning()) {
            $manager->restartProcess();
        }

        return response()->json(['ok' => true]);
    }

    private function finalizeSession(string $name): void
    {
        $sessions = Settings::get('telegram.sessions', []);
        if (!in_array($name, $sessions)) {
            $sessions[] = $name;
            Settings::set('telegram.sessions', $sessions);
        }
        session()->forget('pending_session');
    }

    public function log(Request $request): JsonResponse
    {
        $session = $request->query('session');
        $manager = new UpdateHandlerManager();

        $allLines = $session
            ? $manager->getLastLines(500)
            : $manager->getLastLines(100);

        if ($session) {
            $sessions = new SessionManager();
            $sessionData = collect($sessions->getSessions())->firstWhere('name', $session);
            $internalName = $sessionData['internal_name'] ?? null;

            $lines = array_filter($allLines, function ($line) use ($session, $internalName) {
                return str_contains($line, "telegram_sessions/{$session}:")
                    || ($internalName && str_contains($line, "{$internalName}:"));
            });
        } else {
            $lines = $allLines;
        }

        return response()->json(['lines' => array_values($lines)]);
    }
}
