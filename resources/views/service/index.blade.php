<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Service Status
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
                            <p id="status-text" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Loading...</p>
                            <p id="status-pid" class="text-sm text-gray-500 dark:text-gray-400"></p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="action('start')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                            Start
                        </button>
                        <button onclick="action('restart')" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                            Restart
                        </button>
                        <button onclick="action('stop')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                            Stop
                        </button>
                    </div>
                </div>
            </div>

            <!-- Log Card -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Log</h3>
                    <div class="flex items-center gap-4">
                        <span id="log-meta" class="text-xs text-gray-500 dark:text-gray-400"></span>
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <input type="checkbox" id="auto-refresh" checked class="rounded">
                            Auto-refresh
                        </label>
                    </div>
                </div>
                <pre id="log-output" class="bg-gray-950 text-green-300 text-xs rounded-lg p-4 overflow-x-hidden overflow-y-auto h-96 font-mono" style="color: #86efac; white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere;"></pre>
            </div>

        </div>
    </div>

    <script>
        let refreshTimer = null;

        async function fetchStatus() {
            const res = await fetch('{{ route('service.status') }}');
            const data = await res.json();

            const indicator = document.getElementById('status-indicator');
            const text = document.getElementById('status-text');
            const pidEl = document.getElementById('status-pid');

            if (data.count === 0) {
                indicator.textContent = '🔴';
                text.textContent = 'Not running';
                pidEl.textContent = '';
            } else if (data.count === 1) {
                indicator.textContent = '🟢';
                text.textContent = 'Running';
                pidEl.textContent = `PID: ${data.pids.join(', ')}`;
            } else {
                indicator.textContent = '⚠️';
                text.textContent = `Warning: ${data.count} processes`;
                pidEl.textContent = `PIDs: ${data.pids.join(', ')}`;
            }

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

        async function action(type) {
            if (type === 'stop' && !confirm('Stop the service?')) return;

            const indicator = document.getElementById('status-indicator');
            const text = document.getElementById('status-text');
            indicator.textContent = '⏳';
            text.textContent = type === 'restart' ? 'Restarting...' : (type === 'start' ? 'Starting...' : 'Stopping...');

            await fetch(`{{ url('/service') }}/${type}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            });

            // Poll until status matches expected state
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
