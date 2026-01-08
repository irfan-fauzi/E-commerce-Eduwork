@extends('layouts.userNavbar')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-black relative">
            <div class="p-6">
                <!-- Order Header -->
                <div class="mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Order Detail</h1>
                            <p class="text-gray-600 text-sm mt-1">Order ID : #{{ $transaction->id }}</p>
                        </div>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                                'Success' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $sc = $statusClasses[$transaction->status] ?? 'bg-red-100 text-red-800';
                        @endphp

                        <span class="absolute top-4 right-4 px-4 py-1 rounded-md text-sm font-semibold {{ $sc }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>

                <hr class="my-6">

                <!-- Order Items -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Item Purchased</h2>
                        <div class="text-sm text-gray-600">{{ $transaction->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">{{ __('Product') }}</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">{{ __('Quantity') }}</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">{{ __('Price') }}</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->items as $item)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-4">
                                                @if($item->product && $item->product->images->first())
                                                    <img src="{{ asset('storage/' . $item->product->images->first()->url) }}" alt="{{ $item->product_name }}" class="w-8 h-8 object-cover rounded">
                                                @else
                                                    <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                                                        <i class="bi bi-image text-gray-400 text-sm" aria-hidden="true"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-900">
                                            Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="flex justify-end">
                            <div class="w-full md:w-1/2">
                                <div class="border-t border-gray-200 pt-4 space-y-2">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Shipping Fee:</span>
                                        <span>Rp {{ number_format($transaction->shipping_fee, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold text-gray-900">
                                        <span>Total:</span>
                                        <span>Rp {{ number_format($transaction->total_amount + $transaction->shipping_fee, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-6">

                <!-- Shipping Address & Message boxes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Shipping Address -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Address</h2>
                        @if($transaction->address)
                            <div class="bg-white rounded border border-gray-200">
                                <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
                                    <div class="bg-red-100 text-red-700 rounded-full p-2">
                                        @if($transaction->address->label === 'Office')
                                            <i class="bi bi-building-fill text-red-700" aria-hidden="true"></i>
                                        @else
                                            <i class="bi bi-house-door-fill text-red-700" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                    <p class="font-medium text-gray-900">{{ $transaction->address->label }}</p>
                                </div>
                                <div class="px-4 py-4 text-sm text-gray-700">
                                    @if($transaction->address->recipient_name)
                                        <p class="mb-1">Recipient Name : {{ $transaction->address->recipient_name }}</p>
                                    @endif
                                    <p class="mb-1">Number Phone : {{ $transaction->address->phone }}</p>
                                    <p class="mb-3">Address :<br>{{ $transaction->address->address_text }}</p>
                                    <p class="mb-1">@if($transaction->address->district)District: {{ $transaction->address->district }}@endif</p>
                                    <p class="mb-1">@if($transaction->address->subdistrict)Subdistrict: {{ $transaction->address->subdistrict }}@endif</p>
                                    <p class="text-sm text-gray-600">{{ $transaction->address->city }}, {{ $transaction->address->province }} {{ $transaction->address->postal_code }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500">{{ __('Address not available') }}</p>
                        @endif
                    </div>

                    <!-- Message -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Message for the Shop</h2>
                        <div class="bg-white rounded border border-gray-200">
                            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
                                <div class="bg-green-100 text-green-700 rounded-full p-2">
                                    <i class="bi bi-envelope-fill text-green-700" aria-hidden="true"></i>
                                </div>
                                <p class="font-medium text-gray-900">Messages</p>
                            </div>
                            <div class="px-4 py-4 text-sm text-gray-700">
                                <p class="text-sm text-gray-600 mt-2">{{ $transaction->message ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center mt-6">
                    <!-- Back button  -->
                    <a href="{{ route('user.home') }}" class="inline-flex items-center justify-center w-12 h-10 bg-red-600 hover:bg-red-700 text-white rounded">
                        <i class="bi bi-arrow-left text-2xl" aria-hidden="true"></i>
                    </a>

                   <div class="flex-1 flex justify-center gap-6">
        @if($transaction->status === 'pending')
            <a id="tx-pay-button" href="{{ route('user.checkout.payment.show', $transaction) }}" role="button" class="inline-block px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded transition z-10">
                {{ __('Pay Now') }}
            </a>

            <form method="POST" action="{{ route('user.transactions.cancel', $transaction) }}" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                @csrf
                <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded transition">
                    Cancel Order
                </button>
            </form>
        @endif
    </div>
</div>
        </div>
    </div>
</div>
@endsection

