<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Service Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Status Card -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span id="status-indicator" class="text-3xl">⏳</span>
                        <div>
                            <p id="status-text" class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Loading...') }}</p>
                            <p id="status-pid" class="text-sm text-gray-500 dark:text-gray-400"></p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button id="btn-start" onclick="action('start')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg border border-green-700 shadow-sm transition disabled:bg-gray-100 disabled:dark:bg-gray-700 disabled:text-gray-400 disabled:dark:text-gray-500 disabled:border-gray-300 disabled:dark:border-gray-600 disabled:shadow-none disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                            {{ __('Start') }}
                        </button>
                        <button id="btn-restart" onclick="action('restart')" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg border border-yellow-600 shadow-sm transition disabled:bg-gray-100 disabled:dark:bg-gray-700 disabled:text-gray-400 disabled:dark:text-gray-500 disabled:border-gray-300 disabled:dark:border-gray-600 disabled:shadow-none disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
                            {{ __('Restart') }}
                        </button>
                        <button id="btn-stop" onclick="action('stop')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg border border-red-700 shadow-sm transition disabled:bg-gray-100 disabled:dark:bg-gray-700 disabled:text-gray-400 disabled:dark:text-gray-500 disabled:border-gray-300 disabled:dark:border-gray-600 disabled:shadow-none disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h12v12H6z"/></svg>
                            {{ __('Stop') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Log Card -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('Log') }}</h3>
                    <div class="flex items-center gap-4">
                        <span id="log-meta" class="text-xs text-gray-500 dark:text-gray-400"></span>
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <input type="checkbox" id="auto-refresh" checked class="rounded">
                            {{ __('Auto-refresh') }}
                        </label>
                    </div>
                </div>
                <pre id="log-output" class="bg-gray-950 text-green-300 text-xs rounded-lg p-4 overflow-x-hidden overflow-y-auto h-96 font-mono" style="color: #86efac; white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere;"></pre>
            </div>

        </div>
    </div>

    <script>
        const i18n = {
            running:     @json(__('Running')),
            notRunning:  @json(__('Not running')),
            starting:    @json(__('Starting...')),
            restarting:  @json(__('Restarting...')),
            stopping:    @json(__('Stopping...')),
            stopConfirm: @json(__('Stop the service?')),
            warning:     @json(__('Warning: :count processes')),
        };

        let refreshTimer = null;

        async function fetchStatus() {
            const res = await fetch('{{ route('service.status') }}');
            const data = await res.json();

            const indicator = document.getElementById('status-indicator');
            const text = document.getElementById('status-text');
            const pidEl = document.getElementById('status-pid');

            const running = data.count > 0;

            if (data.count === 0) {
                indicator.textContent = '🔴';
                text.textContent = i18n.notRunning;
                pidEl.textContent = '';
            } else if (data.count === 1) {
                indicator.textContent = '🟢';
                text.textContent = i18n.running;
                pidEl.textContent = `PID: ${data.pids.join(', ')}`;
            } else {
                indicator.textContent = '⚠️';
                text.textContent = i18n.warning.replace(':count', data.count);
                pidEl.textContent = `PIDs: ${data.pids.join(', ')}`;
            }

            document.getElementById('btn-start').disabled = running;
            document.getElementById('btn-restart').disabled = !running;
            document.getElementById('btn-stop').disabled = !running;

            if (data.log) {
                const meta = document.getElementById('log-meta');
                const size = (data.log.size / 1024).toFixed(1) + ' KB';
                meta.textContent = `${data.log.path} · ${size} · ${data.log.last_modified}`;

                const logEl = document.getElementById('log-output');
                const lines = data.log.last_lines.filter(l => l.trim() !== '');
                logEl.textContent = lines.join('\n');
                logEl.scrollTop = logEl.scrollHeight;
            }
        }

        function setButtonsDisabled(disabled) {
            ['btn-start', 'btn-restart', 'btn-stop'].forEach(id => {
                document.getElementById(id).disabled = disabled;
            });
        }

        async function action(type) {
            if (type === 'stop' && !confirm(i18n.stopConfirm)) return;

            setButtonsDisabled(true);
            const indicator = document.getElementById('status-indicator');
            const text = document.getElementById('status-text');
            indicator.textContent = '⏳';
            text.textContent = type === 'restart' ? i18n.restarting : (type === 'start' ? i18n.starting : i18n.stopping);

            await fetch(`{{ url('/service') }}/${type}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            });

            const wantRunning = type !== 'stop';
            const deadline = Date.now() + 15000;
            while (Date.now() < deadline) {
                await new Promise(r => setTimeout(r, 700));
                const res = await fetch('{{ route('service.status') }}');
                const data = await res.json();
                const isRunning = data.count > 0;
                if (isRunning === wantRunning) {
                    await fetchStatus();
                    return;
                }
            }
            await fetchStatus();
        }

        function startRefresh() {
            stopRefresh();
            refreshTimer = setInterval(fetchStatus, 5000);
        }

        function stopRefresh() {
            if (refreshTimer) clearInterval(refreshTimer);
        }

        document.getElementById('auto-refresh').addEventListener('change', function() {
            this.checked ? startRefresh() : stopRefresh();
        });

        fetchStatus();
        startRefresh();
    </script>
</x-app-layout>
