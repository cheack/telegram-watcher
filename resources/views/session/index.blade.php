<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Sessions
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Session</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Size</th>
                            <th class="px-6 py-3">Last updated</th>
                            <th class="px-6 py-3">Event handler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($sessions as $session)
                            <tr class="text-gray-900 dark:text-gray-100">
                                <td class="px-6 py-4 font-medium font-mono">{{ $session['name'] }}</td>
                                <td class="px-6 py-4">
                                    @if($session['logged_in'])
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            🟢 Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            ⚪ Not initialized
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
        </div>
    </div>
</x-app-layout>
