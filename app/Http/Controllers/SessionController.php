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
        $logInfo = (new UpdateHandlerManager())->getLogFileInfo();

        if (!$logInfo) {
            return response()->json(['lines' => []]);
        }

        $lines = array_filter(
            $logInfo['last_lines'],
            fn($line) => !$session || str_contains($line, "telegram_sessions/{$session}")
        );

        return response()->json(['lines' => array_values($lines)]);
    }
}
