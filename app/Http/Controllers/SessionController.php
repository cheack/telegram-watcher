<?php

namespace App\Http\Controllers;

use App\Services\Telegram\SessionManager;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(): View
    {
        $sessions = (new SessionManager())->getSessions();

        return view('session.index', compact('sessions'));
    }
}
