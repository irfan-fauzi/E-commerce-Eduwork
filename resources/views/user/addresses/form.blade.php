@extends('layouts.userNavbar')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-8">

            {{-- HEADER --}}
            <div class="flex items-center justify-center mb-4 gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-full border border-red-200 bg-red-50">
                    <i class="bi bi-pin-map-fill text-red-600 text-lg" aria-hidden="true"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">My Address</h2>
            </div>

            <form action="{{ isset($address) ? route('user.addresses.update', $address) : route('user.addresses.store') }}" method="POST">
                @csrf
                @if(isset($address))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2">Address Label</label>
                        <select name="label"
                            @class([
                                'w-full bg-gray-100 px-3 py-3 rounded focus:outline-none focus:ring-2 focus:ring-red-200 border',
                                'border-red-500' => $errors->has('label'),
                                'border-gray-100' => ! $errors->has('label')
                            ])>
                            <option value="Home" {{ (isset($address) && $address->label === 'Home') || old('label') === 'Home' ? 'selected' : '' }}>Home</option>
                            <option value="Office" {{ (isset($address) && $address->label === 'Office') || old('label') === 'Office' ? 'selected' : '' }}>Office</option>
                        </select>
                        @error('label')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2">Recipient Name</label>
                        <input type="text" name="recipient_name"
                            value="{{ $address->recipient_name ?? old('recipient_name') }}"
                            placeholder="Recipient name"
                            @class([
                                'w-full bg-gray-100 px-3 py-3 rounded focus:outline-none focus:ring-2 focus:ring-red-200 border',
                                'border-red-500' => $errors->has('recipient_name'),
                                'border-gray-100' => ! $errors->has('recipient_name')
                            ])>
                        @error('recipient_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2">Number Phone</label>
                        <input type="text" name="phone"
                            value="{{ $address->phone ?? old('phone') }}"
                            placeholder="0812xxxxxxx"
                            @class([
                                'w-full bg-gray-100 px-3 py-3 rounded focus:outline-none focus:ring-2 focus:ring-red-200 border',
                                'border-red-500' => $errors->has('phone'),
                                'border-gray-100' => ! $errors->has('phone')
                            ])>
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2">City</label>
                        <input type="text" name="city"
                            value="{{ $address->city ?? old('city') }}"
                            placeholder="City"
                            @class([
                                'w-full bg-gray-100 px-3 py-3 rounded focus:outline-none focus:ring-2 focus:ring-red-200 border',
                                'border-red-500' => $errors->has('city'),
                                'border-gray-100' => ! $errors->has('city')
                            ])>
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror


                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2">Province</label>
                        <input type="text" name="province"
                            value="{{ $address->province ?? old('province') }}"
                            placeholder="Province"
                            @class([
                                'w-full bg-gray-100 px-3 py-3 rounded focus:outline-none focus:ring-2 focus:ring-red-200 border',
                                'border-red-500' => $errors->has('province'),
                                'border-gray-100' => ! $errors->has('province')
                            ])>
                        @error('province')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror


                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2">Postal Code</label>
                        <input type="text" name="postal_code"
                            value="{{ $address->postal_code ?? old('postal_code') }}"
                            placeholder="12345"
                            @class([
                                'w-full bg-gray-100 px-3 py-3 rounded focus:outline-none focus:ring-2 focus:ring-red-200 border',
                                'border-red-500' => $errors->has('postal_code'),
                                'border-gray-100' => ! $errors->has('postal_code')
                            ])>
                        @error('postal_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 mb-2">Addresses</label>
                    <textarea name="address_text" rows="6" required
                        placeholder="Street address, building, etc."
                        @class([
                            'w-full bg-gray-100 px-4 py-4 rounded focus:outline-none focus:ring-2 focus:ring-red-200 border',
                            'border-red-500' => $errors->has('address_text'),
                            'border-gray-100' => ! $errors->has('address_text')
                        ])>{{ $address->address_text ?? old('address_text') }}</textarea>
                    @error('address_text')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-3 text-sm text-gray-700">
                        <input type="checkbox" name="is_primary" value="1"
                            {{ (isset($address) && $address->is_primary) || old('is_primary') ? 'checked' : '' }}
                            class="accent-red-600 w-4 h-4">
                        <span>Set Primary address</span>
                    </label>

                    <button type="submit" class="px-6 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700">
                        {{ isset($address) ? 'Update Address' : 'Add Address' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
