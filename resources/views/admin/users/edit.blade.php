@extends('layouts.adminNavbar')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 font-semibold transition">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl border border-gray-100">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Customer</h1>
        <p class="text-gray-600 text-sm mb-6">Update customer information</p>

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-800 mb-2">Name</label>
                    <input type="text" id="name" name="name" value="{{ $user->name }}" class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('name') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:outline-none focus:ring-2 transition text-gray-900 placeholder-gray-400" placeholder="Enter customer name">
                    @error('name')
                        <p class="text-red-600 text-sm mt-2 flex items-center"><span class="mr-1">⚠</span>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:outline-none focus:ring-2 transition text-gray-900 placeholder-gray-400" placeholder="Enter customer email">
                    @error('email')
                        <p class="text-red-600 text-sm mt-2 flex items-center"><span class="mr-1">⚠</span>{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition shadow-sm hover:shadow-md">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition shadow-sm hover:shadow-md">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
