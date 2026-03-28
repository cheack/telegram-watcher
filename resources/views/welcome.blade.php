<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Telegram Watcher</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-950 text-white font-sans antialiased">

    <div class="min-h-full flex flex-col">

        <!-- Nav -->
        <header class="px-6 py-5 flex items-center justify-between max-w-5xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8l-1.68 7.92c-.12.55-.45.68-.91.42l-2.52-1.86-1.22 1.17c-.13.13-.25.25-.51.25l.18-2.57 4.65-4.2c.2-.18-.04-.28-.31-.1L8.91 14.3l-2.49-.78c-.54-.17-.55-.54.11-.8l9.72-3.74c.45-.17.85.11.39.82z"/>
                </svg>
                <span class="font-semibold text-lg tracking-tight">Telegram Watcher</span>
            </div>
            <a href="{{ route('login') }}"
               class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-500 text-white rounded-lg border border-blue-500 shadow-sm transition">
                Sign in
            </a>
        </header>

        <!-- Hero -->
        <main class="flex-1 flex flex-col items-center justify-center px-6 py-20 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-950 border border-blue-800 text-blue-300 text-xs font-medium mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                Monitoring active
            </div>

            <h1 class="text-4xl sm:text-5xl font-bold text-white tracking-tight leading-tight max-w-2xl">
                Track Telegram channels<br>
                <span class="text-blue-400">in real time</span>
            </h1>

            <p class="mt-6 text-gray-400 text-lg max-w-xl leading-relaxed">
                Monitor account activity across multiple Telegram sessions. Get instant notifications when users join or leave channels.
            </p>

            <a href="{{ route('login') }}"
               class="mt-10 inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl border border-blue-500 shadow-lg transition text-base">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M14 12H3"/></svg>
                Go to dashboard
            </a>

            <!-- Features -->
            <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl w-full text-left">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <div class="w-9 h-9 rounded-lg bg-blue-950 border border-blue-900 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-blue-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8l-1.68 7.92c-.12.55-.45.68-.91.42l-2.52-1.86-1.22 1.17c-.13.13-.25.25-.51.25l.18-2.57 4.65-4.2c.2-.18-.04-.28-.31-.1L8.91 14.3l-2.49-.78c-.54-.17-.55-.54.11-.8l9.72-3.74c.45-.17.85.11.39.82z"/></svg>
                    </div>
                    <h3 class="font-semibold text-white text-sm mb-1">Multi-session</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Manage multiple Telegram accounts simultaneously from one place.</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <div class="w-9 h-9 rounded-lg bg-green-950 border border-green-900 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-green-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-semibold text-white text-sm mb-1">Instant alerts</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Real-time bot notifications on channel joins, leaves, and messages from unknown contacts.</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <div class="w-9 h-9 rounded-lg bg-purple-950 border border-purple-900 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <h3 class="font-semibold text-white text-sm mb-1">Service control</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Start, stop and monitor the handler process from the web panel.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-8 text-center text-gray-700 text-xs">
            Telegram Watcher &mdash; private admin panel
        </footer>

    </div>

</body>
</html>
