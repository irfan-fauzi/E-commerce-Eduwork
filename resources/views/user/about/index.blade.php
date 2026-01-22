@extends('layouts.userNavbar')

@section('content')
    <section>
        <div class="bg-gray-50 lg:py-8">
            <div class="max-w-7xl mx-auto border px-4">

                {{-- Breadcrumb --}}
                <div class="text-sm text-gray-500 mb-10 py-2">
                    Home / <span class="text-black">About Us</span>
                </div>
                <div class="flex flex-col lg:flex-row-reverse gap-10 lg:items-center lg:justify-between">
                    <img src="{{ asset('img/side-about.png') }}" alt="about image">
                    <article class="mt-5">
                        <h1 class="text-3xl font-bold">Our Story</h1>
                        <p class="mt-5 font-light">
                            Launced in 2015, Exclusive is South Asia’s premier online shopping makterplace with an active
                            presense in Bangladesh. Supported by wide range of tailored marketing, data and service
                            solutions, Exclusive has 10,500 sallers and 300 brands and serves 3 millioons customers across
                            the region.
                        </p>
                        <p class="mt-5 font-light">
                            Exclusive has more than 1 Million products to offer, growing at a very fast. Exclusive offers a
                            diverse assotment in categories ranging from consumer.
                        </p>
                    </article>
                </div>

                <div class="flex flex-col lg:flex-row mt-5 lg:mt-40 gap-10 lg:items-center lg:justify-between">
                    <div
                        class="flex w-full flex-col justify-center items-center shadow-lg rounded-lg pb-4 hover:bg-red-700  hover:text-white transition-colors">
                        <div
                            class="border-white bg-black rounded-full w-20 h-20 lg:w-40  lg:h-40 mt-10 flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 lg:w-16 lg:h-16" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-store-icon lucide-store">
                                <path d="M15 21v-5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v5" />
                                <path
                                    d="M17.774 10.31a1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.451 0 1.12 1.12 0 0 0-1.548 0 2.5 2.5 0 0 1-3.452 0 1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.77-3.248l2.889-4.184A2 2 0 0 1 7 2h10a2 2 0 0 1 1.653.873l2.895 4.192a2.5 2.5 0 0 1-3.774 3.244" />
                                <path d="M4 10.95V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8.05" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-center mt-4">10.5 K</h2>
                        <p class="text-center">seller active</p>
                    </div>

                    {{-- dollar --}}
                    <div
                        class="flex w-full flex-col justify-center items-center shadow-lg rounded-lg pb-4 hover:bg-red-700  hover:text-white transition-colors">
                        <div
                            class="border-white bg-black rounded-full w-20 h-20 lg:w-40  lg:h-40 mt-10 flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 lg:w-16 lg:h-16" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-circle-dollar-sign-icon lucide-circle-dollar-sign">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                                <path d="M12 18V6" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-center mt-4">33 K</h2>
                        <p class="text-center">Monthly Products sale</p>
                    </div>

                    <div
                        class="flex w-full flex-col justify-center items-center shadow-lg rounded-lg pb-4 hover:bg-red-700  hover:text-white transition-colors">
                        <div
                            class="border-white bg-black rounded-full w-20 h-20 lg:w-40  lg:h-40 mt-10 flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 lg:w-16 lg:h-16" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-shopping-bag-icon lucide-shopping-bag">
                                <path d="M16 10a4 4 0 0 1-8 0" />
                                <path d="M3.103 6.034h17.794" />
                                <path
                                    d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-center mt-4">45.5 K</h2>
                        <p class="text-center">Costumer active in our site</p>
                    </div>

                    <div
                        class="flex w-full flex-col justify-center items-center shadow-lg rounded-lg pb-4 hover:bg-red-700  hover:text-white transition-colors">
                        <div
                            class="border-white bg-black rounded-full w-20 h-20 lg:w-40  lg:h-40 mt-10 flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 lg:w-16 lg:h-16" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-hand-coins-icon lucide-hand-coins">
                                <path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 17" />
                                <path
                                    d="m7 21 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9" />
                                <path d="m2 16 6 6" />
                                <circle cx="16" cy="9" r="2.9" />
                                <circle cx="6" cy="5" r="3" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-center mt-4">25 K</h2>
                        <p class="text-center">Anual grass sale in our site</p>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
