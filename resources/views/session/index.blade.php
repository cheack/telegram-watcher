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
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">{{ __('Sessions') }}</h3>
                    <button onclick="openAddSession()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">
                        + {{ __('Add Session') }}
                    </button>
                </div>
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

    <!-- Add Session Modal -->
    <div id="add-session-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 id="modal-title" class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('Add Session') }}</h3>
                <button onclick="closeAddSession()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl leading-none">&times;</button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <!-- Error -->
                <div id="modal-error" class="hidden bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 text-sm rounded-md px-4 py-3"></div>

                <!-- Step 1: name + phone -->
                <div id="step-start">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Session Name') }}</label>
                            <input id="input-session-name" type="text" placeholder="e.g. alice"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <p class="mt-1 text-xs text-gray-400">{{ __('Letters, digits, hyphens and underscores only') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Phone Number') }}</label>
                            <input id="input-phone" type="tel" placeholder="+79001234567"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button onclick="closeAddSession()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">{{ __('Cancel') }}</button>
                        <button onclick="submitStart()" id="btn-start" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">{{ __('Send Code') }}</button>
                    </div>
                </div>

                <!-- Step 2: SMS code -->
                <div id="step-code" class="hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('SMS Code') }}</label>
                        <input id="input-code" type="text" placeholder="12345" maxlength="10"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        <p class="mt-1 text-xs text-gray-400">{{ __('Enter the code sent to your phone') }}</p>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button onclick="resetModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">{{ __('Back') }}</button>
                        <button onclick="submitCode()" id="btn-code" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">{{ __('Verify') }}</button>
                    </div>
                </div>

                <!-- Step 3: 2FA password -->
                <div id="step-2fa" class="hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('2FA Password') }}</label>
                        <input id="input-password" type="password"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        <p id="2fa-hint" class="mt-1 text-xs text-gray-400"></p>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button onclick="closeAddSession()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">{{ __('Cancel') }}</button>
                        <button onclick="submit2fa()" id="btn-2fa" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">{{ __('Login') }}</button>
                    </div>
                </div>

                <!-- Done -->
                <div id="step-done" class="hidden text-center py-4">
                    <div class="text-green-600 dark:text-green-400 text-4xl mb-3">✓</div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('Session added successfully!') }}</p>
                    <div class="mt-6">
                        <button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">{{ __('Done') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSession = null;
        let refreshTimer = null;
        const logUrl = '{{ route('sessions.log') }}';
        const csrfToken = '{{ csrf_token() }}';

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

        // Add Session Modal
        function openAddSession() {
            resetModal();
            document.getElementById('add-session-modal').classList.remove('hidden');
        }

        function closeAddSession() {
            document.getElementById('add-session-modal').classList.add('hidden');
        }

        function resetModal() {
            showStep('start');
            document.getElementById('modal-error').classList.add('hidden');
            document.getElementById('input-session-name').value = '';
            document.getElementById('input-phone').value = '';
            document.getElementById('input-code').value = '';
            document.getElementById('input-password').value = '';
        }

        function showStep(name) {
            ['start', 'code', '2fa', 'done'].forEach(s => {
                document.getElementById('step-' + s).classList.add('hidden');
            });
            document.getElementById('step-' + name).classList.remove('hidden');
        }

        function showError(msg) {
            const el = document.getElementById('modal-error');
            el.textContent = msg;
            el.classList.remove('hidden');
        }

        function setLoading(btnId, loading) {
            const btn = document.getElementById(btnId);
            btn.disabled = loading;
            btn.style.opacity = loading ? '0.6' : '';
        }

        async function apiPost(url, data) {
            document.getElementById('modal-error').classList.add('hidden');
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });
            const json = await res.json();
            // Normalize Laravel validation errors ({ errors: {...}, message: '...' }) to { error: '...' }
            if (!res.ok && !json.error) {
                if (json.errors) {
                    json.error = Object.values(json.errors).flat().join(' ');
                } else if (json.message) {
                    json.error = json.message;
                }
            }
            return json;
        }

        async function submitStart() {
            const name = document.getElementById('input-session-name').value.trim();
            const phone = document.getElementById('input-phone').value.trim();
            if (!name || !phone) { showError(@json(__('Please fill in all fields.'))); return; }

            setLoading('btn-start', true);
            const data = await apiPost('{{ route('sessions.login.start') }}', { session_name: name, phone });
            setLoading('btn-start', false);

            if (data.error) { showError(data.error); return; }
            showStep('code');
            setTimeout(() => document.getElementById('input-code').focus(), 50);
        }

        async function submitCode() {
            const code = document.getElementById('input-code').value.trim();
            if (!code) { showError(@json(__('Please enter the code.'))); return; }

            setLoading('btn-code', true);
            const data = await apiPost('{{ route('sessions.login.verify') }}', { code });
            setLoading('btn-code', false);

            if (data.error) { showError(data.error); return; }
            if (data.step === '2fa') {
                document.getElementById('2fa-hint').textContent = data.hint ? @json(__('Hint:')) + ' ' + data.hint : '';
                showStep('2fa');
                setTimeout(() => document.getElementById('input-password').focus(), 50);
            } else {
                showStep('done');
            }
        }

        async function submit2fa() {
            const password = document.getElementById('input-password').value;
            if (!password) { showError(@json(__('Please enter your password.'))); return; }

            setLoading('btn-2fa', true);
            const data = await apiPost('{{ route('sessions.login.2fa') }}', { password });
            setLoading('btn-2fa', false);

            if (data.error) { showError(data.error); return; }
            showStep('done');
        }

        document.getElementById('add-session-modal').addEventListener('click', function(e) {
            if (e.target === this) closeAddSession();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const modal = document.getElementById('add-session-modal');
                if (modal.classList.contains('hidden')) return;
                if (!document.getElementById('step-start').classList.contains('hidden')) submitStart();
                else if (!document.getElementById('step-code').classList.contains('hidden')) submitCode();
                else if (!document.getElementById('step-2fa').classList.contains('hidden')) submit2fa();
            }
            if (e.key === 'Escape') closeAddSession();
        });
    </script>
</x-app-layout>
