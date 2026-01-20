@extends('layouts.adminNavbar')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Welcome Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="bi bi-hand-thumbs-up text-indigo-600"></i> Welcome back, {{ auth()->user()->name }}!
            </h1>
            <p class="text-gray-600 mt-2 text-base">Here's a summary of your store today.</p>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

            <!-- Total Revenue -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-2xl hover:-translate-y-1 transition p-6 border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Total Revenue</p>
                        <p class="text-lg font-bold text-gray-900 mt-1"> Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg">
                        <i class="bi bi-cash-stack text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-2xl hover:-translate-y-1 transition p-6 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Total Orders</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $totalOrders }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white shadow-lg">
                        <i class="bi bi-bag-check text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-2xl hover:-translate-y-1 transition p-6 border-l-4 border-amber-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Pending Orders</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $pendingOrders }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-lg">
                        <i class="bi bi-clock-history text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-2xl hover:-translate-y-1 transition p-6 border-l-4 border-purple-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Total Products</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                        <i class="bi bi-box-seam text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- CHART SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

            <!-- Revenue Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md hover:shadow-lg transition p-8">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <i class="bi bi-graph-up-arrow text-blue-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Revenue Analytics</h2>
                    </div>
                    <select id="revenueFilter"
                        class="px-7 py-2 border border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <canvas id="revenueChart" height="100"></canvas>
            </div>

            <!-- Order Status -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="bi bi-pie-chart-fill text-indigo-600 text-lg"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Order Status</h2>
                </div>
                <canvas id="orderStatusChart" height="140"></canvas>
            </div>

        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Recent Orders -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
                <div class="p-8 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                            <i class="bi bi-receipt text-orange-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Order ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentOrders ?? [] as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-blue-600">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $order->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    Rp {{ number_format($order->total_amount + $order->shipping_fee, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                    $status = $order->status;
                                    $statusClasses = [
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'paid' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                    ];
                                    $class = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $class }}">{{ ucfirst($status) }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-gray-400">
                                    <i class="bi bi-inbox text-4xl mb-2 block"></i>
                                    No orders yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 mb-8">

                <!-- Store Statistics -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                            <i class="bi bi-bar-chart text-teal-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Store Statistics</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-tags text-indigo-600"></i>
                                <span class="text-gray-700">Categories</span>
                            </div>
                            <span class="font-bold text-lg text-indigo-600">{{ $totalCategories }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-people text-blue-600"></i>
                                <span class="text-gray-700">Users</span>
                            </div>
                            <span class="font-bold text-lg text-blue-600">{{ $totalUsers }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-check-circle text-green-600"></i>
                                <span class="text-gray-700">Completed Orders</span>
                            </div>
                            <span class="font-bold text-lg text-green-600">{{ $completedOrders }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-percent text-green-600"></i>
                                <span class="text-gray-700 font-medium">Conversion Rate</span>
                            </div>
                            <span class="font-bold text-lg text-green-600">
                                {{ $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- CHART SCRIPT --}}
<script>
const revenueData = {
    weekly: { labels: {!! json_encode($weeklyRevenue->keys()) !!}, data: {!! json_encode($weeklyRevenue->values()) !!} },
    monthly: { labels: {!! json_encode($monthlyRevenue->keys()) !!}, data: {!! json_encode($monthlyRevenue->values()) !!} },
    yearly: { labels: {!! json_encode($yearlyRevenue->keys()) !!}, data: {!! json_encode($yearlyRevenue->values()) !!} },
};

const revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: revenueData.monthly.labels,
        datasets: [{
            data: revenueData.monthly.data,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.15)',
            tension: 0.4,
            fill: true
        }]
    },
    options: { plugins: { legend: { display: false } } }
});

document.getElementById('revenueFilter').addEventListener('change', e => {
    const type = e.target.value;
    revenueChart.data.labels = revenueData[type].labels;
    revenueChart.data.datasets[0].data = revenueData[type].data;
    revenueChart.update();
});

    // Fixed status order: Pending, Paid, Cancelled, Completed
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending','Paid','Cancelled','Completed'],
            datasets: [{
                data: [
                    {!! json_encode($orderStatus->get('pending', 0)) !!},
                    {!! json_encode($orderStatus->get('paid', 0)) !!},
                    {!! json_encode($orderStatus->get('cancelled', 0)) !!},
                    {!! json_encode($orderStatus->get('completed', 0)) !!}
                ],
                backgroundColor: ['#facc15','#3b82f6','#ef4444','#22c55e']
            }]
        },
        options: { cutout: '65%' }
    });
</script>
@endsection
