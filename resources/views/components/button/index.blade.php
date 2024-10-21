@aware([
    'type',
])

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'inline-flex items-center border font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2',
    ]) }}
>
    {{ $slot }}
</button>
