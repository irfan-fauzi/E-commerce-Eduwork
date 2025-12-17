@props(['size' => 'h-24 sm:h-28 md:h-32 w-auto object-contain flex-shrink-0'])

<img
    src="{{ asset('images/Logo.png') }}"
    alt="{{ config('app.name', 'Logo') }}"
    {{ $attributes->merge(['class' => $size]) }}
/>
