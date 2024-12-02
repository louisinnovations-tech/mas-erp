@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
    ? 'flex items-center p-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg'
    : 'flex items-center p-2 text-sm font-medium text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-5 h-5 mr-3" />
    @endif

    {{ $slot }}
</a>