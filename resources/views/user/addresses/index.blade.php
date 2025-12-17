@extends('layouts.userNavbar')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 relative">

        {{-- HEADER --}}
        <div class="relative mb-10">
            <div class="flex justify-center">
                <div class="bg-white rounded-lg shadow p-6 w-80 text-center">
                    <div class="flex items-center justify-center mb-3">
                        {{-- ICON (FIXED) --}}
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border border-red-200 bg-red-50 mr-3">
                            <i class="bi bi-person-fill text-red-600 text-base" aria-hidden="true"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">My Address</h2>
                    </div>
                    <p class="text-sm text-gray-600">
                        This address will be used as your default delivery location.
                        Make sure all information is correct and up to date
                    </p>
                </div>
            </div>

            <a href="{{ route('user.addresses.create') }}"
               class="absolute right-0 top-0 md:-mr-6 inline-flex items-center gap-3 bg-black text-white rounded-full px-4 py-2 shadow-lg hover:opacity-95">
                <span class="inline-flex items-center justify-center bg-white text-black rounded-full w-8 h-8">
                    <i class="bi bi-plus text-black text-base" aria-hidden="true"></i>
                </span>
                <span class="text-sm font-medium">Add Address</span>
            </a>
        </div>

        {{-- ADDRESS LIST --}}
        @if($addresses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 justify-items-center">
            @foreach($addresses as $address)
            <div class="w-96 border border-gray-300 rounded bg-white shadow-sm">

                {{-- CARD HEADER --}}
                <div class="flex items-center gap-3 border-b border-gray-200 px-4 py-3">
                    {{-- ICON (FIXED) --}}
                    <div class="flex items-center justify-center w-9 h-9 rounded-full border border-red-200 bg-red-50">
                        @if(isset($address->label) && $address->label === 'Office')
                            <i class="bi bi-building-fill text-red-600 text-sm" aria-hidden="true"></i>
                        @else
                            <i class="bi bi-house-door-fill text-red-600 text-sm" aria-hidden="true"></i>
                        @endif
                    </div>

                    <div class="flex-1">
                        @if($address->label)
                            <h3 class="text-base font-semibold text-gray-900">{{ $address->label }}</h3>
                        @endif
                    </div>
                </div>

                {{-- CARD BODY --}}
                <div class="px-4 py-4 text-sm text-gray-700">
                    @if($address->recipient_name)
                        <p class="mb-1">Recipient Name : {{ $address->recipient_name }}</p>
                    @endif
                    @if($address->phone)
                        <p class="mb-1">Number Phone : {{ $address->phone }}</p>
                    @endif

                    <p class="mb-3">Address :<br>{{ $address->address_text }}</p>

                    <p class="text-sm text-gray-600">
                        @if($address->city){{ $address->city }}{{ $address->province ? ', ' : '' }}@endif
                        @if($address->province){{ $address->province }}@endif
                        @if($address->postal_code) {{ $address->postal_code }}@endif
                    </p>
                </div>

                {{-- CARD FOOTER --}}
                <div class="px-4 py-3 flex items-center justify-between">
                    <form action="{{ route('user.addresses.update', $address) }}" method="POST" class="inline-flex items-center">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_primary" value="1">
                        <input type="hidden" name="label" value="{{ $address->label }}">
                        <input type="hidden" name="recipient_name" value="{{ $address->recipient_name }}">
                        <input type="hidden" name="phone" value="{{ $address->phone }}">
                        <input type="hidden" name="address_text" value="{{ $address->address_text }}">
                        <input type="hidden" name="city" value="{{ $address->city }}">
                        <input type="hidden" name="province" value="{{ $address->province }}">
                        <input type="hidden" name="postal_code" value="{{ $address->postal_code }}">

                        <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                            <input type="radio" name="primary_radio"
                                   {{ $address->is_primary ? 'checked' : '' }}
                                   onchange="this.closest('form').submit()"
                                   class="accent-red-600">
                            <span>Set Primary address</span>
                        </label>
                    </form>

                    <div class="flex items-center gap-2">
                        @if($address->is_primary)
                            <span class="text-sm text-red-600 font-semibold mr-2">Primary</span>
                        @endif

                        <a href="{{ route('user.addresses.edit', $address) }}"
                           class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50">
                            <i class="bi bi-pencil-square text-gray-600 text-sm"></i>
                        </a>

                        <form action="{{ route('user.addresses.destroy', $address) }}" method="POST"
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50">
                                <i class="bi bi-trash text-gray-600 text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-12 text-center mt-10">
            <p class="text-gray-600 mb-4">No addresses added yet.</p>
            <a href="{{ route('user.addresses.create') }}"
               class="inline-block px-6 py-3 bg-red-600 text-white rounded hover:bg-red-700">
                Add Your First Address
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
