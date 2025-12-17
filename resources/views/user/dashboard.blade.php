@extends('layouts.userNavbar')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6">{{ __('Welcome Back, ') }}{{ auth()->user()->name }}</h1>

                <!-- User Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Cart Items -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">{{ __('Items in Cart') }}</p>
                                <p class="text-3xl font-bold text-blue-600">{{ $cartCount }}</p>
                            </div>
                            <a href="{{ route('user.cart.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">{{ __('Orders Placed') }}</p>
                                <p class="text-3xl font-bold text-green-600">{{ $orderCount }}</p>
                            </div>
                            <svg class="w-12 h-12 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                    </div>


                </div>

                <!-- Recent Orders -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Recent Orders') }}</h2>

                    @if($recentOrders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">{{ __('Order ID') }}</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">{{ __('Date') }}</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">{{ __('Status') }}</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">{{ __('Total') }}</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr class="border-t border-gray-200 hover:bg-gray-100">
                                            <td class="px-4 py-3 text-sm text-gray-900">#{{ $order->id }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'paid' => 'bg-blue-100 text-blue-800',
                                                        'shipped' => 'bg-purple-100 text-purple-800',
                                                        'completed' => 'bg-green-100 text-green-800',
                                                    ];
                                                    $sc = $statusClasses[$order->status] ?? 'bg-red-100 text-red-800';
                                                @endphp

                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                Rp {{ number_format($order->total_amount + $order->shipping_fee, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('user.transactions.show', $order->id) }}" class="text-blue-600 hover:text-blue-800">
                                                        {{ __('View') }}
                                                    </a>
                                                    @if($order->status === 'pending')
                                                        <form method="POST" action="{{ route('user.transactions.cancel', $order->id) }}" onsubmit="return confirm('Cancel this order?');">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">{{ __('Cancel') }}</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">{{ __('No orders yet. Start shopping now!') }}</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="mt-8 bg-white rounded-lg p-6 border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Quick Actions') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('user.products.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-center transition">
                            {{ __('Continue Shopping') }}
                        </a>
                        <a href="{{ route('user.addresses.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded text-center transition">
                            {{ __('Manage Addresses') }}
                        </a>
                        @if(auth()->user() && auth()->user()->is_admin)
                            <a href="{{ route('admin.profile.edit') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded text-center transition">
                                {{ __('Edit Profile') }}
                            </a>
                        @else
                            <a href="{{ route('user.profile.edit') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded text-center transition">
                                {{ __('Edit Profile') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
