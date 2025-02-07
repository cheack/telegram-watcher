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

                <!-- Trusted Resources -->
                <div>
                    <x-input-label :value="__('Trusted Resources')" />
                    <div id="trusted-resources">
                        @if(isset($trackedAccount) && $trackedAccount->trustedResources->isNotEmpty())
                            @foreach($trackedAccount->trustedResources as $resource)
                                <div class="trusted-resource mt-2">
                                    <x-input-label for="resource_id" :value="__('Resource ID')" />
                                    <x-text-input name="trusted_resources[{{ $loop->index }}][resource_id]" type="text" class="mt-1 block w-full" value="{{ $resource->resource_id }}" />
                                    <x-input-label for="resource_name" :value="__('Resource Name')" />
                                    <x-text-input name="trusted_resources[{{ $loop->index }}][resource_name]" type="text" class="mt-1 block w-full" value="{{ $resource->resource_name }}" />
                                    <input type="hidden" name="trusted_resources[{{ $loop->index }}][id]" value="{{ $resource->id }}">
                                    <button type="button" class="remove-resource text-red-500 mt-2">Remove</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-resource" class="mt-2 text-blue-500">Add Resource</button>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>
                        {{ isset($trackedAccount) ? __('Update Account') : __('Add Account') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('add-resource').addEventListener('click', function() {
            const resourceContainer = document.getElementById('trusted-resources');
            const index = resourceContainer.querySelectorAll('.trusted-resource').length;
            const newResource = document.createElement('div');
            newResource.classList.add('trusted-resource', 'mt-2');
            newResource.innerHTML = `
            <x-input-label for="resource_id" :value="__('Resource ID')" />
            <x-text-input name="trusted_resources[${index}][resource_id]" type="text" class="mt-1 block w-full" />
            <x-input-label for="resource_name" :value="__('Resource Name')" />
            <x-text-input name="trusted_resources[${index}][resource_name]" type="text" class="mt-1 block w-full" />
            <button type="button" class="remove-resource text-red-500 mt-2">Remove</button>
        `;
            resourceContainer.appendChild(newResource);
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-resource')) {
                event.target.closest('.trusted-resource').remove();
            }
        });
    </script>
</x-app-layout>
