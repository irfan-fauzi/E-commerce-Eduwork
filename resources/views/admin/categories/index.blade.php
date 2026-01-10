@extends('layouts.adminNavbar')

@section('content')
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
        <div class="flex items-center gap-3">
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Export</a>
            <a href="{{ route('admin.categories.create') }}" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-medium flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Category
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mb-6 p-4 flex items-center gap-4">
        <div class="flex-1 relative">
            <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
            <input id="searchInput" type="text" name="search" placeholder="Search by category name..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <!-- no extra buttons -->
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Slug</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Parent</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $category->slug }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $category->parent?->name ?? 'None' }}</td>
                        <td class="px-6 py-4 text-sm flex items-center gap-1">
                            <a href="{{ route('admin.categories.edit', $category) }}" title="Edit" class="p-1 hover:bg-gray-100 rounded-lg text-gray-500 transition">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
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
                        <td colspan="4" class="px-6 py-8 text-center text-gray-600">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>
</div>

<script>
document.getElementById('searchInput')?.addEventListener('input', function() {
    let searchValue = this.value;
    let url = new URL(window.location);
    if (searchValue) url.searchParams.set('search', searchValue); else url.searchParams.delete('search');
    window.location.href = url.toString();
});
</script>
@endsection
