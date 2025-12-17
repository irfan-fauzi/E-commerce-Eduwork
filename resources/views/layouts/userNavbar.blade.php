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

        <!-- Scripts & Styles (Vite) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased overflow-x-hidden">

        <div class="min-h-screen flex flex-col bg-gray-100 overflow-x-hidden">

            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

                    <div class="flex items-center flex-shrink-0">
                        <a href="{{ route('user.dashboard') }}" class="flex items-center ">
                            <x-site-logo class="mr-2" />
                            <span class="sr-only">{{ config('app.name', 'Logo') }}</span>
                        </a>
                    </div>

                    <!-- MENU  -->
                    <div class="hidden md:flex gap-10 text-sm items-center">
                        <a href="{{ route('user.dashboard') }}"
                           class="pb-1 {{ request()->routeIs('user.dashboard') ? 'border-b-2 border-black text-black font-medium' : 'text-gray-600 hover:text-black' }}">
                            Home
                        </a>

                        <a href="{{ route('user.products.index') }}"
                           class="pb-1 {{ request()->routeIs('user.products.*') ? 'border-b-2 border-black text-black font-medium' : 'text-gray-600 hover:text-black' }}">
                            Product
                        </a>

                        <a href="#" class="text-gray-600 hover:text-black pb-1">About</a>

                        <a href="#" class="text-gray-600 hover:text-black pb-1">Contact</a>
                    </div>
                    <div class="flex items-center gap-6">

                        <!-- SEARCH (desktop only) -->
                    <div class="hidden md:flex items-center bg-gray-100 rounded-md px-3 py-2 w-40 lg:w-64">
                    <form method="GET" action="{{ route('user.products.index') }}" class="flex items-center w-full">

                    <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="What are you looking for?"
                    class="w-full bg-transparent text-sm outline-none border-0 focus:ring-0"
                    >

                    <button
                    type="submit"
                    class="ml-2 p-0 bg-transparent border-0"
                    aria-label="Search"
                    >
                    <i class="bi bi-search text-gray-700 text-base"></i>
                    </button>

                    </form>
                    </div>

                        <!-- HEART -->
                        <button aria-label="Wishlist" class="flex items-center">
                            <i class="bi bi-heart" style="font-size:1.25rem;"></i>
                        </button>

                        <!-- CART -->
                        <a href="{{ route('user.cart.index') }}" class="relative" aria-label="Cart">
                            <i class="bi bi-cart" style="font-size:1.25rem;"></i>

                            @auth
                            <span id="cart-badge"
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                                {{ auth()->user()->cartItems()->count() }}
                            </span>
                            @endauth
                        </a>

                        <!-- USER  -->
                        @auth
                        <div id="userTrigger" class="flex items-center gap-2 ml-2 cursor-pointer select-none">
                            <i class="bi bi-person-circle" style="font-size:1.4rem;"></i>
                            <span class="text-sm font-medium text-gray-800 hidden md:inline-block">{{ auth()->user()->name }}</span>
                        </div>
                        @endauth

                        @guest
                            <div class="hidden md:flex items-center gap-4">
                                <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Register</a>
                                @endif
                            </div>
                        @endguest

                        <!-- HAMBURGER -->
                        <button id="mobileMenuBtn" class="ml-2 md:hidden focus:outline-none" aria-label="Open menu">
                            <i class="bi bi-list" style="font-size:1.6rem;"></i>
                        </button>
                    </div>
                </div>

                <!-- USER DROPDOWN (DESKTOP)  -->
                <div id="userDropdown" class="hidden absolute right-4 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                    <div class="py-1">
                        @if(auth()->user() && auth()->user()->is_admin)
                            <a href="{{ route('admin.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        @else
                            <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</button>
                        </form>
                    </div>
                </div>

                <!-- MOBILE MENU COLLAPSE -->
                <div id="mobileMenu" class="md:hidden hidden px-6 pb-4 mt-2 border-t border-gray-100 bg-white">
                    <div class="flex flex-col gap-3 text-sm pt-4">
                        <a href="{{ route('user.dashboard') }}"
                           class="{{ request()->routeIs('user.dashboard') ? 'text-black font-medium underline' : 'text-gray-700' }}">
                            Home
                        </a>

                        <a href="{{ route('user.products.index') }}"
                           class="{{ request()->routeIs('user.products.*') ? 'text-black font-medium underline' : 'text-gray-700' }}">
                            Product
                        </a>

                        <a href="#" class="text-gray-700">About</a>
                        <a href="#" class="text-gray-700">Contact</a>
                    </div>
                </div>
            </nav>


            <!-- Flash Messages (full width) -->
            <div class="w-full py-2">
                <x-flash-messages />
            </div>

            <!-- Page Content -->
            <main>
                @yield('content')
                @include('layouts.footer')
            </main>
        </div>

        <!-- SCRIPTS -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Mobile menu toggle
                const mobileBtn = document.getElementById('mobileMenuBtn');
                const mobileMenu = document.getElementById('mobileMenu');
                mobileBtn && mobileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    mobileMenu.classList.toggle('hidden');
                });


                const userTrigger = document.getElementById('userTrigger');
                const userDropdown = document.getElementById('userDropdown');
                if (userTrigger) {
                    userTrigger.addEventListener('click', function (e) {
                        e.stopPropagation();
                        userDropdown.classList.toggle('hidden');
                    });

                    // Dropdown
                    document.addEventListener('click', function () {
                        if (userDropdown && !userDropdown.classList.contains('hidden')) {
                            userDropdown.classList.add('hidden');
                        }
                    });
                }


                if (userDropdown) {
                    userDropdown.addEventListener('click', function (e) {
                        e.stopPropagation();
                    });
                }

                // Update cart badge count
                @auth
                    fetch('{{ route("user.cart.count") }}')
                        .then(response => response.json())
                        .then(data => {
                            const cartBadge = document.getElementById('cart-badge');
                            if (cartBadge && data && typeof data.count !== 'undefined') {
                                cartBadge.textContent = data.count;
                            }
                        })
                        .catch(e => console.log('Cart badge error:', e));
                @endauth
            });
        </script>
    </body>
</html>
