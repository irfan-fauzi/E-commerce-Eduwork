@extends('layouts.adminNavbar')

@section('content')
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Customers</h1>
        <div class="flex items-center gap-3">
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Export</a>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 flex items-center gap-4">
        <div class="flex-1 relative">
            <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
            <input id="searchInput" type="text" placeholder="Search by name or email..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Orders</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Joined</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm font-medium">{{ $user->transactions_count ?? $user->transactions->count() }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm flex items-center gap-1">
                            <a href="{{ route('admin.users.edit', $user) }}" title="Edit" class="p-1 hover:bg-gray-100 rounded-lg text-gray-500 transition">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" class="p-1 hover:bg-gray-100 rounded-lg text-gray-500 transition" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash-fill text-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-600">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function() {
    let searchValue = this.value;
    let url = new URL(window.location);
    if (searchValue) url.searchParams.set('search', searchValue); else url.searchParams.delete('search');
    window.location.href = url.toString();
});
</script>
@endsection
