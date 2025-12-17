@if (session('success'))
    <div class="mb-4 p-4 rounded bg-green-100 border border-green-200 text-green-700 text-base w-full">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 rounded bg-red-50 border border-red-200 text-red-800 text-base w-full">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 p-4 rounded bg-red-50 border border-red-200 text-red-800 text-base w-full">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
