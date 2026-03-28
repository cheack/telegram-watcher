<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Telegram Watcher</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>(function(){var d=localStorage.theme==='dark'||(!('theme'in localStorage)&&window.matchMedia('(prefers-color-scheme: dark)').matches);document.documentElement.classList.toggle('dark',d);})();</script>
</head>
<body x-data class="h-full bg-white dark:bg-gray-950 text-gray-900 dark:text-white font-sans antialiased transition-colors duration-200">

    <div class="min-h-full flex flex-col">

        <!-- Nav -->
        <header class="px-6 py-5 flex items-center justify-between max-w-5xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8l-1.68 7.92c-.12.55-.45.68-.91.42l-2.52-1.86-1.22 1.17c-.13.13-.25.25-.51.25l.18-2.57 4.65-4.2c.2-.18-.04-.28-.31-.1L8.91 14.3l-2.49-.78c-.54-.17-.55-.54.11-.8l9.72-3.74c.45-.17.85.11.39.82z"/>
                </svg>
                <span class="font-semibold text-lg tracking-tight">Telegram Watcher</span>
            </div>
            <div class="flex items-center gap-2">

                <!-- Theme Toggle -->
                <button @click="$store.theme.toggle()"
                        class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg x-show="$store.theme.dark" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    <svg x-show="!$store.theme.dark" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>

                <!-- Language Switcher -->
                @php $locales = config('app.available_locales'); $currentLocale = app()->getLocale(); @endphp
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = !open"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        {{ $locales[$currentLocale] }}
                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="open" x-transition
                         class="absolute right-0 mt-1 w-28 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50 py-1"
                         style="display: none;">
                        @foreach($locales as $code => $label)
                            <a href="{{ route('lang.switch', $code) }}"
                               class="flex items-center px-3 py-1.5 text-xs {{ $currentLocale === $code ? 'font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('login') }}"
                   class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-500 text-white rounded-lg border border-blue-500 shadow-sm transition">
                    {{ __('Sign in') }}
                </a>
            </div>
        </header>

        <!-- Hero -->
        <main class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center relative overflow-hidden">

            <!-- glow -->
            <div class="absolute inset-0 flex justify-center pointer-events-none" aria-hidden="true">
                <div class="w-[900px] h-[500px] rounded-full bg-blue-500/10 dark:bg-blue-600/10 blur-3xl mt-10"></div>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800/60 text-blue-600 dark:text-blue-300 text-xs font-medium mb-10 relative">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse"></span>
                Live
            </div>

            <h1 class="text-6xl sm:text-8xl font-black text-gray-900 dark:text-white tracking-tight leading-[0.9] max-w-4xl relative">
                {{ __('Strangers. Bad channels.') }}<br>
                <span class="text-blue-600 dark:text-blue-400">{{ __("You'll know first.") }}</span>
            </h1>

            <p class="mt-8 text-gray-500 dark:text-gray-400 text-lg max-w-lg leading-relaxed relative">
                {{ __('Monitor who contacts your children and what channels they join — before it becomes a problem.') }}
            </p>

            <a href="{{ route('login') }}"
               class="mt-10 inline-flex items-center gap-2 px-7 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl border border-blue-500 shadow-lg shadow-blue-500/20 dark:shadow-blue-950/60 transition text-base relative">
                {{ __('Sign in') }}
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>

            <!-- Features -->
            <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-2xl w-full text-left relative">
                <div class="border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 rounded-xl p-5 transition">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-100 dark:border-blue-900 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8l-1.68 7.92c-.12.55-.45.68-.91.42l-2.52-1.86-1.22 1.17c-.13.13-.25.25-.51.25l.18-2.57 4.65-4.2c.2-.18-.04-.28-.31-.1L8.91 14.3l-2.49-.78c-.54-.17-.55-.54.11-.8l9.72-3.74c.45-.17.85.11.39.82z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">{{ __('Multi-session') }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ __('Multiple Telegram accounts, one panel.') }}</p>
                </div>

                <div class="border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 rounded-xl p-5 transition">
                    <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-950 border border-green-100 dark:border-green-900 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">{{ __('Instant alerts') }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ __('Channel joins, leaves, and messages from unknown senders.') }}</p>
                </div>

                <div class="border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 rounded-xl p-5 transition">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950 border border-purple-100 dark:border-purple-900 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">{{ __('Service control') }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ __('Start, stop and monitor the handler from the web panel.') }}</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-8 text-center text-gray-400 dark:text-gray-700 text-xs">
            Telegram Watcher &mdash; {{ __('private admin panel') }}
        </footer>

    </div>

</body>
</html>
