<?php

namespace App\Http\Controllers;

use App\Services\Telegram\UpdateHandlerManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('service.index');
    }

    public function status(): JsonResponse
    {
        $manager = new UpdateHandlerManager();
        $logInfo = $manager->getLogFileInfo();
        $count = $manager->getProcessCount();

        return response()->json([
            'count' => $count,
            'log' => $logInfo,
        ]);
    }

    public function start(): JsonResponse
    {
        $manager = new UpdateHandlerManager();
        $manager->startProcess();

        return response()->json(['ok' => true]);
    }

    public function stop(): JsonResponse
    {
        $manager = new UpdateHandlerManager();
        $manager->stopProcess();

        return response()->json(['ok' => true]);
    }

    public function restart(): JsonResponse
    {
        $manager = new UpdateHandlerManager();
        $pid = $manager->restartProcess();

        return response()->json(['ok' => true, 'pid' => $pid]);
    }
}
