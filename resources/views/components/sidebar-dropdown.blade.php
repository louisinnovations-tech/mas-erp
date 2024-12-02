@props(['active' => false, 'title' => '', 'icon' => null])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-1">
    <!-- Dropdown Trigger -->
    <button 
        @click="open = !open"
        class="w-full flex items-center justify-between p-2 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out"
        :class="{ 'text-blue-700 bg-blue-50': open, 'text-gray-700 hover:text-blue-700 hover:bg-blue-50': !open }"
    >
        <div class="flex items-center">
            @if ($icon)
                <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-5 h-5 mr-3" />
            @endif
            <span>{{ $title }}</span>
        </div>
        <svg 
            :class="{'transform rotate-180': open}"
            class="w-4 h-4 transition-transform duration-200"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
        >
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Dropdown Content -->
    <div 
        x-show="open"
        x-transition:enter="transition-all duration-300 ease-in-out"
        x-transition:enter-start="opacity-0 max-h-0"
        x-transition:enter-end="opacity-100 max-h-96"
        x-transition:leave="transition-all duration-300 ease-in-out"
        x-transition:leave-start="opacity-100 max-h-96"
        x-transition:leave-end="opacity-0 max-h-0"
        class="pl-8 overflow-hidden"
    >
        <div class="py-2 space-y-1">
            {{ $slot }}
        </div>
    </div>
</div>