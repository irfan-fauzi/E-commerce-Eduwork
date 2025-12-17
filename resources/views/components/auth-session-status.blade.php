@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-700 text-base font-medium w-full']) }}>
        {{ $status }}
    </div>
@endif
