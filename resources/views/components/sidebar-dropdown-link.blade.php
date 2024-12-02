@props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'block px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg'
    : 'block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>