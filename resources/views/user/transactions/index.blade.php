@extends('layouts.userNavbar')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Top card / header -->
        <div class="flex justify-center mb-6">
            <div class="bg-white rounded-lg shadow-sm px-6 py-6 text-center w-full md:w-1/2 border border-gray-200">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 text-red-700 rounded-full mx-auto mb-3">
                    <i class="bi bi-shop-window text-xl"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">My Order</h2>
                <p class="text-gray-600 text-sm mt-1">
                    This page displays all your orders. You can view order details, delivery status, and transaction history here.
                </p>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-black">
            <div class="p-6">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <h3 class="flex items-center gap-3">
                        <i class="bi bi-bag-fill text-red-700"></i>
                        <span class="text-base font-medium text-gray-900">My order</span>
                    </h3>

                    <!-- Filter -->
                    <form method="GET"
                          action="{{ route('user.transactions.index') }}"
                          class="flex items-center gap-2 flex-wrap justify-end">

                        <div class="relative">
                            <i class="bi bi-funnel absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="status"
                                    id="status"
                                    onchange="this.form.submit()"
                                    class="pl-9 pr-8 py-1.5 text-sm border border-gray-300 rounded-md
                                           bg-white focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">All statuses</option>
                                @foreach($allowedStatuses as $st)
                                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                        {{ ucfirst($st) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Clear -->
                        @if(request()->filled('status'))
                            <a href="{{ route('user.transactions.index') }}"
                               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition">
                                <i class="bi bi-x-circle text-red-500"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="px-4 py-3 text-left">Order ID</th>
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Total</th>
                                <th class="px-4 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'paid' => 'bg-blue-100 text-blue-800',
                                        'success' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $sc = $statusClasses[$t->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">#{{ $t->id }}</td>
                                    <td class="px-4 py-3">{{ $t->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $sc }}">
                                            {{ ucfirst($t->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        Rp {{ number_format(($t->total_amount ?? 0) + ($t->shipping_fee ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('user.transactions.show', $t) }}"
                                           class="inline-flex items-center gap-2 text-gray-800 hover:text-black">
                                            <i class="bi bi-eye"></i>
                                            <span class="underline">View</span>
                                        </a>

                                        @if($t->status === 'pending')
                                            <form method="POST"
                                                  action="{{ route('user.transactions.cancel', $t) }}"
                                                  class="inline ml-3"
                                                  onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                @csrf
                                                <button type="submit"
                                                        class="text-sm text-red-600 hover:text-red-800">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-600">
                                        You haven't placed any orders yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-center">
                    {{ $transactions->onEachSide(1)->links('components.pagination') }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
