<?php

namespace App\Http\Controllers;

use App\Services\Telegram\SessionManager;
use App\Services\Telegram\UpdateHandlerManager;
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
