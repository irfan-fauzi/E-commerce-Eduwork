@extends('layouts.userNavbar')

@section('content')
<div class="border">
  <section class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-4 gap-6">
  
  <!-- Sidebar -->
  <aside class="hidden lg:block col-span-1 border-r pr-4">
    <ul class="space-y-3 text-sm">
      <li class="font-medium">Woman’s Fashion</li>
      <li class="font-medium">Men’s Fashion</li>
      <li class="text-gray-600">Electronics</li>
      <li class="text-gray-600">Home & Lifestyle</li>
      <li class="text-gray-600">Medicine</li>
      <li class="text-gray-600">Sports & Outdoor</li>
      <li class="text-gray-600">Baby’s & Toys</li>
      <li class="text-gray-600">Groceries & Pets</li>
      <li class="text-gray-600">Health & Beauty</li>
    </ul>
  </aside>

  <!-- Banner -->
  <div class="lg:col-span-3 bg-black rounded-lg p-8 flex items-center justify-between text-white">
    <div>
      <p class="text-sm opacity-80">iPhone 14 Series</p>
      <h2 class="text-3xl font-bold mt-2">Up to 10% <br> off Voucher</h2>
      <a href="#" class="inline-block mt-4 underline">Shop Now →</a>
    </div>

    <img src="/images/iphone.png" alt=""
      class="hidden md:block w-60">
  </div>
</section>


</div>
@endsection