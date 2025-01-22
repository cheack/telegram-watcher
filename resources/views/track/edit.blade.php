<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($trackedAccount) ? __('Edit Tracked Account') : __('Add Tracked Account') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="max-w-xl">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ isset($trackedAccount) ? __('Edit Tracked Account') : __('Add Tracked Account') }}
            </h3>

            <form method="POST" action="{{ isset($trackedAccount) ? route('track.update', $trackedAccount->id) : route('track.add') }}" class="mt-6 space-y-6">
                @csrf
                @if(isset($trackedAccount))
                    @method('PATCH')
                @endif

                <!-- Service -->
                <div>
                    <x-input-label for="service_id" :value="__('Service')" />
                    <select id="service_id" name="service_id" class="mt-1 block w-full">
                        @foreach(\App\Models\Service::all() as $service)
                            <option value="{{ $service->id }}" {{ isset($trackedAccount) && $trackedAccount->service_id == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bot ID -->
                <div>
                    <x-input-label for="bot_id" :value="__('Bot ID')" />
                    <x-text-input id="bot_id" name="bot_id" type="text" class="mt-1 block w-full" value="{{ $trackedAccount->bot_id ?? '' }}" required />
                </div>

                <!-- Account ID -->
                <div>
                    <x-input-label for="account_id" :value="__('Account ID')" />
                    <x-text-input id="account_id" name="account_id" type="text" class="mt-1 block w-full" value="{{ $trackedAccount->account_id ?? '' }}" required />
                </div>

                <!-- Account Name -->
                <div>
                    <x-input-label for="account_name" :value="__('Account Name')" />
                    <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" value="{{ $trackedAccount->account_name ?? '' }}" />
                </div>

                <!-- Notes -->
                <div>
                    <x-input-label for="notes" :value="__('Notes')" />
                    <x-textarea id="notes" name="notes" class="mt-1 block w-full">{{ $trackedAccount->notes ?? '' }}</x-textarea>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>
                        {{ isset($trackedAccount) ? __('Update Account') : __('Add Account') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
