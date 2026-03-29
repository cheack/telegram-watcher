@php $locales = config('app.available_locales'); $currentLocale = app()->getLocale(); @endphp
<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button @click="open = !open"
            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        {{ $locales[$currentLocale] }}
        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
    </button>
    <div x-show="open" x-transition
         class="absolute top-full right-0 mt-1 w-28 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg z-50 py-1"
         style="display: none;">
        @foreach($locales as $code => $label)
            <a href="{{ route('lang.switch', $code) }}"
               class="flex items-center px-3 py-1.5 text-xs {{ $currentLocale === $code ? 'font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-600' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>
