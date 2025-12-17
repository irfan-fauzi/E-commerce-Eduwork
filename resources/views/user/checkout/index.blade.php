{{-- resources/views/user/checkout/index.blade.php --}}
@extends('layouts.userNavbar')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('user.checkout.pay') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <!-- LEFT: Billing  -->
            <div class="lg:col-span-2">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-8">Billing Details</h2>

                <div class="bg-white rounded-lg shadow p-8">

                    @if($addresses->count() > 0)
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-2">Choose a shipping address</p>
                            <div class="space-y-3">
                                @foreach($addresses as $address)
                                    <label class="flex items-start p-4 border rounded hover:border-gray-300 transition cursor-pointer {{ $loop->first ? 'border-gray-300' : 'border-gray-200' }}">
                                        <input type="radio" name="address_id" value="{{ $address->id }}" class="mt-1" {{ $loop->first ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            @if($address->label)
                                                <p class="font-semibold text-gray-900">{{ $address->label }}</p>
                                            @endif

                                            @if($address->recipient_name)
                                                <p class="text-sm text-gray-700">Recipient: <span class="font-medium">{{ $address->recipient_name }}</span></p>
                                            @endif

                                            @if($address->phone)
                                                <p class="text-sm text-gray-700">Phone: <span class="font-medium">{{ $address->phone }}</span></p>
                                            @endif

                                            <p class="text-gray-700 text-sm">{{ $address->address_text }}</p>
                                            <p class="text-gray-600 text-xs">
                                                {{ $address->city ?? '' }}{{ $address->city && $address->province ? ', ' : '' }}{{ $address->province ?? '' }}
                                                {{ $address->postal_code ? ' ' . $address->postal_code : '' }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Customer message --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pesan untuk toko (opsional)</label>
                        <textarea name="message" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200" placeholder="Tulis pesan untuk toko jika ada...">{{ old('message') }}</textarea>

                    </div>
                </div>
            </div>

            <!-- RIGHT: Order Summary + Payment -->
            <div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Your Order</h3>

                    {{-- Items summary condensed --}}
                    <div class="space-y-3 mb-4">
                        @foreach($cartItems as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @if($item->product && $item->product->images->first())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->url) }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded"></div>
                                    @endif
                                    <div>
                                        <p class="text-sm text-gray-900">{{ $item->product->name }}</p>
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-900">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shipping</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="shipping_fee" id="shippingFee" value="0" min="0" class="w-full px-3 py-2 border border-gray-200 rounded bg-white" />
                            </div>
                            <p id="shippingHint" class="text-xs text-gray-500 mt-1">Enter shipping fee (Rp)</p>
                        </div>

                        <div class="flex justify-between text-lg font-bold text-gray-900 mt-4">
                            <span>Total:</span>
                            <span id="totalAmount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Coupon --}}
                    <div class="mt-6 flex gap-3">
                        <input type="text" name="coupon" placeholder="Coupon Code" class="flex-1 px-4 py-2 border border-gray-200 rounded bg-white" />
                        <button type="button" class="px-4 py-2 bg-red-500 text-white rounded">Apply Coupon</button>
                    </div>

                    {{-- Place order button --}}
                    <div class="mt-6">
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white font-bold rounded hover:bg-red-700">
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const subtotal = {{ $total }};
    const shippingFeeInput = document.getElementById('shippingFee');
    // allow manual shipping input by default
    if (shippingFeeInput) shippingFeeInput.readOnly = false;
    const totalAmount = document.getElementById('totalAmount');

    function formatRp(n) {
        return new Intl.NumberFormat('id-ID').format(n);
    }

    if (shippingFeeInput) {
        shippingFeeInput.addEventListener('input', function() {
            const shippingFee = parseInt(this.value) || 0;
            totalAmount.textContent = 'Rp ' + formatRp(subtotal + shippingFee);
        });
    }
</script>

@endsection
