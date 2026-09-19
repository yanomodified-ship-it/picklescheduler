<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pickleball Courts | Home</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        body { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col justify-between antialiased">

    <!-- Navigation Header -->
    <header class="border-b border-gray-200 bg-white/90 backdrop-blur-md sticky top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
                        <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high" class="h-10 sm:h-12 w-auto" style="height: 44px; width: auto; max-height: 44px;">
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8 text-sm font-semibold text-gray-600">
                <a href="#courts" class="hover:text-lime-600 transition">Courts</a>
                <a href="#rates" class="hover:text-lime-600 transition">Rates</a>
                <a href="#rules" class="hover:text-lime-600 transition">Rules</a>
            </nav>

            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('booking.create') }}" class="text-sm font-semibold bg-lime-600 text-white px-5 py-2.5 rounded-lg hover:bg-lime-500 transition shadow-lg shadow-lime-600/20">Book a Court</a>
            </div>

            <!-- Mobile Menu Button -->
            <button type="button" @click="mobileMenu = !mobileMenu" class="md:hidden text-gray-600 hover:text-gray-900">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="mobileMenu" x-cloak class="md:hidden border-t border-gray-200 px-6 py-4 space-y-3 bg-white">
            <a href="#courts" @click="mobileMenu = false" class="block text-sm font-semibold text-gray-600 hover:text-lime-600">Courts</a>
            <a href="#rates" @click="mobileMenu = false" class="block text-sm font-semibold text-gray-600 hover:text-lime-600">Rates</a>
            <a href="#rules" @click="mobileMenu = false" class="block text-sm font-semibold text-gray-600 hover:text-lime-600">Rules</a>
            <a href="{{ route('booking.create') }}" class="block text-center font-semibold bg-lime-600 text-white px-5 py-2.5 rounded-lg hover:bg-lime-500">Book a Court</a>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative pt-16 pb-20 px-6 max-w-7xl mx-auto text-center">
            <div class="inline-block bg-lime-50 text-lime-700 text-xs font-bold px-4 py-1.5 rounded-full mb-6 border border-lime-200">
                🎾 Private Court Bookings
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 text-gray-900">
                Reserve Your Court.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-600 to-emerald-600">Serve Your Best Game.</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10">
                📍Purok 7, San Vicente Panabo City, Panabo, Philippines, 8105
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('booking.create') }}" class="bg-lime-600 text-white font-bold px-8 py-4 rounded-xl text-lg hover:bg-lime-500 transition shadow-xl shadow-lime-600/20">
                    Reserve Court Now
                </a>
                <a href="#courts" class="bg-gray-100 text-gray-900 font-semibold px-8 py-4 rounded-xl text-lg hover:bg-gray-200 border border-gray-200 transition">
                    View Live Courts
                </a>
            </div>
        </section>

        <!-- Courts Section -->
        <section id="courts" class="scroll-mt-20 border-t border-gray-200 px-6 py-16 max-w-7xl mx-auto" x-data="{ courtFilter: 'outdoor' }">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Our Courts</div>
                    <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-gray-900 mt-1">Choose your court</h2>
                    <p class="mt-2 text-gray-500">Select between our available outdoor and indoor courts below.</p>
                </div>

                <!-- Dynamic Filter Buttons -->
                <div class="inline-flex p-1.5 rounded-xl bg-gray-100 border border-gray-200 text-sm font-semibold">
                    <button 
                        type="button" 
                        @click="courtFilter = 'outdoor'" 
                        :class="courtFilter === 'outdoor' ? 'bg-lime-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900'" 
                        class="px-5 py-2 rounded-lg transition duration-200 flex items-center gap-2">
                        <span>☀️ Outdoor</span>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="courtFilter === 'outdoor' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-600'">
                            {{ $courts->filter(fn($c) => strtolower($c->classification) === 'outdoor')->count() }}
                        </span>
                    </button>

                    <button 
                        type="button" 
                        @click="courtFilter = 'indoor'" 
                        :class="courtFilter === 'indoor' ? 'bg-lime-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900'" 
                        class="px-5 py-2 rounded-lg transition duration-200 flex items-center gap-2">
                        <span>🏢 Indoor</span>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="courtFilter === 'indoor' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-600'">
                            {{ $courts->filter(fn($c) => strtolower($c->classification) === 'indoor')->count() }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Courts Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($courts as $court)
                    @php
                        // Read directly from classification column and normalize to lowercase for Alpine filtering
                        $type = strtolower($court->classification ?? 'outdoor');
                    @endphp

                    <article 
                        x-show="courtFilter === '{{ $type }}'" 
                        x-cloak 
                        class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-gray-300 hover:shadow-md flex flex-col justify-between">
                        <div>
                            <div class="flex h-36 items-center justify-center bg-gray-100 relative">
                                <span class="text-6xl transition group-hover:scale-110">🏓</span>
                                <span class="absolute top-3 right-3 text-xs font-bold px-2.5 py-1 rounded-md bg-white/90 text-gray-600 border border-gray-200 uppercase tracking-wider">
                                    {{ $court->classification ?? 'Outdoor' }}
                                </span>
                            </div>
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $court->name }}</h3>
                                    
                                    <!-- Dynamic Status Badge -->
                                    @if($court->is_fully_booked)
                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 border border-red-200">Fully Booked</span>
                                    @else
                                        <span class="rounded-full bg-lime-50 px-2.5 py-1 text-xs font-bold text-lime-700 border border-lime-200">Active</span>
                                    @endif

                                </div>
                                <p class="mt-2 text-sm leading-relaxed text-gray-500">
                                    {{ $court->description ?? 'Professional pickleball court available for scheduled games.' }}
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <!-- Dynamic Booking Button -->
                            @if($court->is_fully_booked)
                                <button disabled class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-gray-100 py-2.5 text-sm font-bold text-gray-400 cursor-not-allowed">
                                    Unavailable
                                </button>
                            @else
                                <a href="{{ route('booking.create', ['court_id' => $court->id]) }}" 
                                class="inline-flex w-full items-center justify-center rounded-xl border border-lime-200 bg-lime-50 py-2.5 text-sm font-bold text-lime-700 transition hover:bg-lime-600 hover:text-white">
                                    Book this court
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-6 text-gray-500">
                        No active courts are currently available.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Rates Section -->
        <section id="rates" class="scroll-mt-20 py-16 bg-gray-100/60 border-t border-b border-gray-200 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mx-auto text-center">
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Rates</div>
                    <h2 class="mt-1 text-3xl font-extrabold text-gray-900 sm:text-4xl">Simple and transparent pricing</h2>
                </div>

                <div class="max-w-3xl mx-auto mt-10 grid gap-6 sm:grid-cols-2">
                    <!-- Daytime Rate -->
                    <div class="rounded-3xl border border-gray-200 bg-white p-8 text-center shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="inline-block bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-amber-200">
                                ☀️ Day Slot
                            </div>
                            <div class="text-xs font-bold uppercase tracking-widest text-gray-500">5:00 AM – 4:00 PM</div>
                            <div class="mt-3 text-5xl font-black text-lime-600">
                                ₱150
                            </div>
                            <div class="mt-2 text-gray-500">per hour</div>
                        </div>
                        <a href="{{ route('booking.create') }}" class="mt-8 inline-block bg-lime-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-lime-500 transition">
                            Book Day Slot
                        </a>
                    </div>

                    <!-- Night-time Rate -->
                    <div class="rounded-3xl border border-gray-200 bg-white p-8 text-center shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="inline-block bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-indigo-200">
                                🌙 Night Slot
                            </div>
                            <div class="text-xs font-bold uppercase tracking-widest text-gray-500">5:00 PM – 12:00 AM</div>
                            <div class="mt-3 text-5xl font-black text-lime-600">
                                ₱300
                            </div>
                            <div class="mt-2 text-gray-500">per hour</div>
                        </div>
                        <a href="{{ route('booking.create') }}" class="mt-8 inline-block bg-lime-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-lime-500 transition">
                            Book Night Slot
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rules Section -->
        <section id="rules" class="scroll-mt-20 px-6 py-16 max-w-7xl mx-auto">
            <div class="max-w-2xl mb-10">
                <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Court Rules</div>
                <h2 class="mt-1 text-3xl font-extrabold text-gray-900 sm:text-4xl">Play fair. Play safe.</h2>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['icon' => '⏱️', 'title' => 'Arrive Early', 'text' => 'Please arrive before your scheduled booking time.'],
                    ['icon' => '🎉', 'title' => 'Have Fun', 'text' => 'Enjoy your time on the court and bring your best energy!'],
                    ['icon' => '🤝', 'title' => 'Respect Others', 'text' => 'Keep the court area clean and respect other players.'],
                    ['icon' => '🏓', 'title' => 'Play Safely', 'text' => 'Follow facility instructions and play responsibly.'],
                ] as $rule)
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="text-3xl">{{ $rule['icon'] }}</div>
                        <h3 class="mt-4 font-bold text-gray-900 text-lg">{{ $rule['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500">{{ $rule['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </main>

    <!-- Footer -->
<footer class="border-t border-gray-200 py-8 bg-gray-100">
    <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-500">
        <p>© {{ date('Y') }} HomeCourt PickleHouse. All rights reserved.</p>

        <!-- Facebook Social Link -->
        <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </a>
    </div>
</footer>

</body>
</html>