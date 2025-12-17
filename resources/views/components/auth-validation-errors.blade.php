@props(['errors'])

@if ($errors && $errors->any())
    <div {{ $attributes->merge(['class' => 'mb-4 p-4 rounded bg-red-50 border border-red-200 text-red-800 w-full']) }}>
        <div class="font-semibold text-base text-red-700">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-base text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
