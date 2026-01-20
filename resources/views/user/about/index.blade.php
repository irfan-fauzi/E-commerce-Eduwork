@extends('layouts.userNavbar')

@section('content')
    <section>
        <div class="bg-gray-50 lg:py-8">
            <div class="max-w-7xl mx-auto border px-4">

                {{-- Breadcrumb --}}
                <div class="text-sm text-gray-500 mb-10 py-2">
                    Home / <span class="text-black">About Us</span>
                </div>
                <div class="flex flex-col lg:flex-row">
                  <img src="{{ asset('img/side-about.png') }}" alt="about image">
                  <article class="mt-5">
                    <h1 class="text-3xl font-bold">Our Story</h1>
                    <p class="mt-5 font-light">
                      Launced in 2015, Exclusive is South Asia’s premier online shopping makterplace with an active presense in Bangladesh. Supported by wide range of tailored marketing, data and service solutions, Exclusive has 10,500 sallers and 300 brands and serves 3 millioons customers across the region. 
                    </p>
                    <p class="mt-5 font-light">
                      Exclusive has more than 1 Million products to offer, growing at a very fast. Exclusive offers a diverse assotment in categories ranging  from consumer.
                    </p>
                  </article>
                </div>

            </div>
        </div>
    </section>
@endsection
