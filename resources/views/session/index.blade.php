<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sessions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Sessions Table -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">{{ __('Session') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Size') }}</th>
                            <th class="px-6 py-3">{{ __('Last updated') }}</th>
                            <th class="px-6 py-3">{{ __('Event handler') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($sessions as $session)
                            <tr class="text-gray-900 dark:text-gray-100 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                onclick="selectSession('{{ $session['name'] }}')">
                                <td class="px-6 py-4 font-medium font-mono">{{ $session['name'] }}</td>
                                <td class="px-6 py-4">
                                    @if($session['logged_in'])
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            🟢 {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            ⚪ {{ __('Not initialized') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $session['size'] > 0 ? number_format($session['size'] / 1024, 1) . ' KB' : '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $session['updated_at'] ?? '—' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400">
                                    {{ $session['event_handler'] ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Log Viewer -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('Log') }}</h3>
                        <!-- Session Tabs -->
                        <div class="flex gap-1">
                            <button onclick="selectSession(null)"
                                id="tab-all"
                                class="tab-btn px-3 py-1 rounded text-xs font-medium transition bg-indigo-600 text-white">
                                {{ __('All') }}
                            </button>
                            @foreach($sessions as $session)
                                <button onclick="selectSession('{{ $session['name'] }}')"
                                    id="tab-{{ $session['name'] }}"
                                    class="tab-btn px-3 py-1 rounded text-xs font-medium transition bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                                    {{ $session['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <input type="checkbox" id="auto-refresh" checked class="rounded">
                        {{ __('Auto-refresh') }}
                    </label>
                </div>
                <pre id="log-output" class="bg-gray-950 text-xs rounded-lg p-4 overflow-x-hidden overflow-y-auto h-80 font-mono" style="color: #86efac; white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere;"></pre>
            </div>

        </div>
    </div>

    <script>
        let currentSession = null;
        let refreshTimer = null;
        const logUrl = '{{ route('sessions.log') }}';

        async function fetchLog() {
            const url = currentSession ? `${logUrl}?session=${currentSession}` : logUrl;
            const res = await fetch(url);
            const data = await res.json();

            const el = document.getElementById('log-output');
            el.textContent = data.lines.join('\n') || @json(__('(no log lines)'));
            el.scrollTop = el.scrollHeight;
        }

        function selectSession(name) {
            currentSession = name;

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });

            const activeId = name ? `tab-${name}` : 'tab-all';
            const activeBtn = document.getElementById(activeId);
            if (activeBtn) {
                activeBtn.classList.add('bg-indigo-600', 'text-white');
                activeBtn.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            }

            fetchLog();
        }

        document.getElementById('auto-refresh').addEventListener('change', function () {
            if (this.checked) {
                refreshTimer = setInterval(fetchLog, 5000);
            } else {
                clearInterval(refreshTimer);
            }
        });

        fetchLog();
        refreshTimer = setInterval(fetchLog, 5000);
    </script>
</x-app-layout>
