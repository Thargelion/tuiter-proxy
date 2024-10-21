@props([
    'type' => 'button',
])

<x-button
    {{ $attributes->merge(['class' => 'border-transparent shadow-sm text-white bg-indigo-600 hover:bg-indigo-700']) }}>
    {{ $slot }}
</x-button>
