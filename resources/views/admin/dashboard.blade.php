@extends('layouts.adminNavbar')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Selamat datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-gray-600 mt-2">Berikut adalah ringkasan toko Anda hari ini.</p>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-blue-100 p-2 rounded">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalOrders }}</p>
                    </div>
                    <div class="bg-green-100 p-2 rounded">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending Orders</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingOrders }}</p>
                    </div>
                    <div class="bg-yellow-100 p-2 rounded">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
                    </div>
                    <div class="bg-purple-100 p-2 rounded">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10L4 11"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Orders - Wider Column -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">Order Terbaru</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentOrders ?? [] as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-700">{{ $order->user->name ?? 'Unknown' }}</td>
                                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_amount + $order->shipping_fee, 0, ',', '.') }}</td>
                                        <td class="px-6 py-3 text-sm">
                                            @php
                                                $badgeMap = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'paid' => 'bg-blue-100 text-blue-800',
                                                    'shipped' => 'bg-purple-100 text-purple-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                ];
                                                $badgeClass = $badgeMap[$order->status] ?? 'bg-red-100 text-red-800';
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada order</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-200">
                        <a href="{{ route('admin.transactions.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Lihat semua order →</a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h2>
                    <div class="space-y-2">
                        <a href="{{ route('admin.products.create') }}" class="block w-full bg-blue-600 hover:bg-blue-700  font-medium py-2 px-4 rounded text-center transition">
                            ➕ Tambah Produk
                        </a>
                        <a href="{{ route('admin.categories.create') }}" class="block w-full bg-green-600 hover:bg-green-700  font-medium py-2 px-4 rounded text-center transition">
                            ➕ Tambah Kategori
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="block w-full bg-purple-600 hover:bg-purple-700  font-medium py-2 px-4 rounded text-center transition">
                            📦 Kelola Produk
                        </a>
                        <a href="{{ route('admin.transactions.index') }}" class="block w-full bg-indigo-600 hover:bg-indigo-700  font-medium py-2 px-4 rounded text-center transition">
                            📋 Kelola Order
                        </a>
                    </div>
                </div>

                <!-- Store Statistics -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Statistik Toko</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between pb-2 border-b">
                            <span class="text-gray-600">Kategori</span>
                            <span class="font-semibold text-gray-900">{{ $totalCategories }}</span>
                        </div>
                        <div class="flex justify-between pb-2 border-b">
                            <span class="text-gray-600">Pengguna</span>
                            <span class="font-semibold text-gray-900">{{ $totalUsers }}</span>
                        </div>
                        <div class="flex justify-between pb-2 border-b">
                            <span class="text-gray-600">Order Selesai</span>
                            <span class="font-semibold text-gray-900">{{ $completedOrders }}</span>
                        </div>
                        <div class="flex justify-between pt-2">
                            <span class="text-gray-600">Conversion Rate</span>
                            <span class="font-semibold text-green-600">{{ $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
