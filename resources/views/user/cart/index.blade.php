@extends('layouts.userNavbar')

@section('content')
<div class="py-10">
    <div class="max-w-6xl mx-auto px-4">

        @if($cartItems->count() > 0)

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <div class="lg:col-span-2 bg-white rounded-lg shadow border border-gray-200">

                {{-- HEADER --}}
                <div class="grid grid-cols-4 py-4 px-6 font-semibold text-gray-700 text-sm border-b bg-gray-50">
                    <span>Product</span>
                    <span>Price</span>
                    <span>Quantity</span>
                    <span class="text-right">Subtotal</span>
                </div>

                {{-- ITEMS --}}
                @foreach($cartItems as $item)
                <div class="grid grid-cols-4 py-6 px-6 border-b hover:bg-gray-50 transition">

                    {{-- PRODUCT --}}
                    <div class="flex gap-4">
                        <div class="w-20 h-20 rounded overflow-hidden bg-gray-200">
                            @if($item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->url) }}"
                                     class="w-full h-full object-cover">
                            @endif
                        </div>

                        <div class="flex flex-col justify-center">
                            <a href="{{ route('user.products.show', $item->product->slug) }}"
                               class="font-semibold text-gray-900 text-[15px] hover:text-blue-600">
                                {{ $item->product->name }}
                            </a>
                        </div>
                    </div>

                    {{-- PRICE --}}
                    <div class="flex items-center text-[15px]">
                        Rp {{ number_format($item->product->price) }}
                    </div>

                    {{-- QUANTITY --}}
                    <div class="flex items-center">
                        <form method="POST"
                              action="{{ route('user.cart.update', $item->id) }}"
                              class="flex items-center gap-3">
                            @csrf
                            @method('PATCH')

                           <input type="number"
       name="quantity"
       value="{{ $item->quantity }}"
       class="border border-gray-300 text-sm rounded  py-1 w-16 bg-white" />


                            <button class="text-blue-600 text-sm hover:text-blue-800">Update</button>
                        </form>
                    </div>

                    {{-- SUBTOTAL --}}
                    <div class="flex flex-col items-end justify-center">
                        <p class="font-semibold text-gray-900 text-[15px]">
                            Rp {{ number_format($item->product->price * $item->quantity) }}
                        </p>

                        <form action="{{ route('user.cart.destroy', $item->id) }}"
                              method="POST"
                              class="mt-2">
                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 hover:text-red-800 text-sm">
                                Remove
                            </button>
                        </form>
                    </div>

                </div>
                @endforeach

                {{-- FOOTER BUTTONS --}}
                <div class="flex justify-between px-6 py-5">
                    <a href="{{ route('user.products.index') }}"
                       class="px-4 py-2 border border-gray-400 rounded text-sm hover:bg-gray-100">
                        Return To Shop
                    </a>
                </div>

            </div>


            {{-- ORDER SUMMARY (RIGHT)--}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-6 h-fit">

                <h3 class="text-lg font-bold text-gray-900 mb-6">Cart Total</h3>

                <div class="space-y-4 mb-6 pb-6 border-b border-gray-300">
                    <div class="flex justify-between text-gray-700 text-sm">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($total) }}</span>
                    </div>

                </div>

                {{-- TOTAL --}}
                <div class="flex justify-between text-[17px] font-bold text-gray-900 mb-6">
                    <span>Total:</span>
                    <span>Rp {{ number_format($total) }}</span>
                </div>

                <a href="{{ route('user.checkout.index') }}"
                    class="w-full block text-center bg-red-500 px-4 py-3 text-white rounded-lg font-semibold hover:bg-red-600">
                    Proceed to checkout
                </a>
            </div>

        </div>

        @else

        {{-- EMPTY CART --}}
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Your cart is empty</h3>
            <p class="text-gray-600 mb-6">Add some items to get started!</p>
            <a href="{{ route('user.products.index') }}"
               class="inline-block px-6 py-3 bg-red-500 text-white rounded hover:bg-red-600">
               Continue Shopping
            </a>
        </div>

        @endif

    </div>
</div>
@endsection
