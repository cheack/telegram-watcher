<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Tracked Accounts') }}
                        <a href={{route('track.show-add-form')}}>
                            <x-primary-button>{{ __('Add') }}</x-primary-button>
                        </a>
                    </h3>

                    <div class="mt-6 space-y-6">
                        @foreach($trackedAccounts as $account)
                            <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <p class="text-gray-900 dark:text-gray-100">
                                    <strong>{{ $account->account_name ?? 'No Name' }}</strong> ({{ $account->account_id }})
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $account->notes }}</p>
                                <a href="{{ route('track.show-edit-form', $account->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
