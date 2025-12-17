<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Show normal navigation for non-admin pages -->
            @if (!request()->routeIs('admin.*'))
                @include('layouts.navigation')
            @else
                <!-- Admin navigation bar -->
                <nav class="bg-gray-900  shadow">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex items-center space-x-8">
                                <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg text-white">Admin Panel</a>
                                <div class="hidden md:flex space-x-4">
                                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded text-white hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }} ">Dashboard</a>
                                    <a href="{{ route('admin.products.index') }}" class="px-3 py-2 rounded text-white hover:bg-gray-800 {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : '' }} ">Products</a>
                                    <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded text-white hover:bg-gray-800 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }} ">Categories</a>
                                    <a href="{{ route('admin.transactions.index') }}" class="px-3 py-2 rounded text-white hover:bg-gray-800 {{ request()->routeIs('admin.transactions.*') ? 'bg-gray-700' : '' }} ">Transactions</a>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                @auth
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none transition ease-in-out duration-150">
                                                <div>{{ auth()->user()->name }}</div>

                                                <div class="ms-1">
                                                    <svg class="fill-current h-4 w-4 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @if(auth()->user() && auth()->user()->is_admin)
                                                <x-dropdown-link :href="route('admin.profile.edit')">
                                                    {{ __('Profile') }}
                                                </x-dropdown-link>
                                            @else
                                                <x-dropdown-link :href="route('user.profile.edit')">
                                                    {{ __('Profile') }}
                                                </x-dropdown-link>
                                            @endif

                                            <!-- Authentication -->
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf

                                                <x-dropdown-link :href="route('logout')"
                                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </x-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                @else
                                    <div class="space-x-4">
                                        <a href="{{ route('login') }}" class="text-sm text-white hover:text-gray-200">{{ __('Log in') }}</a>
                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="ml-4 text-sm text-white underline">{{ __('Register') }}</a>
                                        @endif
                                    </div>
                                @endauth
                            </div>

                            <!-- Fallback for small screens: simple logout/name -->
                            <div class="flex items-center sm:hidden">
                                @auth
                                    <span class="text-sm text-white mr-3">{{ auth()->user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 rounded bg-gray-800 text-white text-sm">{{ __('Logout') }}</button>
                                    </form>
                                @endauth
                            </div>
                        </div>
                    </div>
                </nav>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                <div class="w-full py-4">
                    <x-flash-messages />
                </div>
                @yield('content')
                </main>
            </div>
        </body>
    </html>
