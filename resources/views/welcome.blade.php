<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pickleball Courts | Home</title>

    <!-- Tailwind CSS CDN & Inter Font -->
    <script src="https://cdn.tailwindcss.com"></script>
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

<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between antialiased">

    <!-- Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-2xl sm:text-3xl">🏓</span>
                <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-lime-400">
                    HomeCourt<span class="text-white">PickleHouse</span>
                </span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8 text-sm font-semibold text-slate-300">
                <a href="#courts" class="hover:text-lime-400 transition">Courts</a>
                <a href="#rates" class="hover:text-lime-400 transition">Rates</a>
                <a href="#rules" class="hover:text-lime-400 transition">Rules</a>
            </nav>

            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('booking.create') }}" class="text-sm font-semibold bg-lime-400 text-slate-950 px-5 py-2.5 rounded-lg hover:bg-lime-300 transition shadow-lg shadow-lime-400/20">Book a Court</a>
            </div>

            <!-- Mobile Menu Button -->
            <button type="button" @click="mobileMenu = !mobileMenu" class="md:hidden text-slate-300 hover:text-white">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="mobileMenu" x-cloak class="md:hidden border-t border-slate-800 px-6 py-4 space-y-3 bg-slate-900">
            <a href="#courts" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-300 hover:text-lime-400">Courts</a>
            <a href="#rates" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-300 hover:text-lime-400">Rates</a>
            <a href="#rules" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-300 hover:text-lime-400">Rules</a>
            <a href="{{ route('booking.create') }}" class="block text-center font-semibold bg-lime-400 text-slate-950 px-5 py-2.5 rounded-lg hover:bg-lime-300">Book a Court</a>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative pt-16 pb-20 px-6 max-w-7xl mx-auto text-center">
            <div class="inline-block bg-slate-800 text-lime-400 text-xs font-bold px-4 py-1.5 rounded-full mb-6 border border-slate-700">
                🎾 Private Court Bookings
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6">
                Reserve Your Court.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-400 to-emerald-400">Serve Your Best Game.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10">
                Seamless pickleball court reservations and match coordination—all in one place.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('booking.create') }}" class="bg-lime-400 text-slate-950 font-bold px-8 py-4 rounded-xl text-lg hover:bg-lime-300 transition shadow-xl shadow-lime-400/20">
                    Reserve Court Now
                </a>
                <a href="#courts" class="bg-slate-800 text-white font-semibold px-8 py-4 rounded-xl text-lg hover:bg-slate-700 border border-slate-700 transition">
                    View Live Courts
                </a>
            </div>
        </section>

        <!-- Courts Section -->
        <section id="courts" class="scroll-mt-20 border-t border-slate-800 px-6 py-16 max-w-7xl mx-auto" x-data="{ courtFilter: 'outdoor' }">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Our Courts</div>
                    <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white mt-1">Choose your court</h2>
                    <p class="mt-2 text-slate-400">Select between our available outdoor and indoor courts below.</p>
                </div>

                <!-- Filter Buttons -->
                <div class="inline-flex p-1.5 rounded-xl bg-slate-800 border border-slate-700/60 text-sm font-semibold">
                    <button 
                        type="button" 
                        @click="courtFilter = 'outdoor'" 
                        :class="courtFilter === 'outdoor' ? 'bg-lime-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white'" 
                        class="px-5 py-2 rounded-lg transition duration-200 flex items-center gap-2">
                        <span>☀️ Outdoor</span>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="courtFilter === 'outdoor' ? 'bg-slate-950/20 text-slate-950' : 'bg-slate-700 text-slate-300'">4</span>
                    </button>

                    <button 
                        type="button" 
                        @click="courtFilter = 'indoor'" 
                        :class="courtFilter === 'indoor' ? 'bg-lime-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white'" 
                        class="px-5 py-2 rounded-lg transition duration-200 flex items-center gap-2">
                        <span>🏢 Indoor</span>
                        <span class="text-xs px-2 py-0.5 rounded-full" :class="courtFilter === 'indoor' ? 'bg-slate-950/20 text-slate-950' : 'bg-slate-700 text-slate-300'">3</span>
                    </button>
                </div>
            </div>

            <!-- Courts Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($courts as $court)
                    @php
                        $type = strtolower($court->type ?? '');
                        if (!$type) {
                            $type = ($loop->iteration <= 4) ? 'outdoor' : 'indoor';
                        }
                    @endphp

                    <article 
                        x-show="courtFilter === '{{ $type }}'" 
                        x-cloak 
                        class="group overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 transition hover:-translate-y-1 hover:border-slate-700 flex flex-col justify-between">
                        <div>
                            <div class="flex h-36 items-center justify-center bg-slate-800 relative">
                                <span class="text-6xl transition group-hover:scale-110">🏓</span>
                                <span class="absolute top-3 right-3 text-xs font-bold px-2.5 py-1 rounded-md bg-slate-900/80 text-slate-300 border border-slate-700/50 uppercase tracking-wider">
                                    {{ $type }}
                                </span>
                            </div>
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-lg font-bold text-white">{{ $court->name }}</h3>
                                    
                                    <!-- Dynamic Status Badge -->
                                    @if($court->is_fully_booked)
                                        <span class="rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-bold text-red-400 border border-red-500/20">Fully Booked</span>
                                    @else
                                        <span class="rounded-full bg-lime-400/10 px-2.5 py-1 text-xs font-bold text-lime-400 border border-lime-400/20">Active</span>
                                    @endif

                                </div>
                                <p class="mt-2 text-sm leading-relaxed text-slate-400">
                                    {{ $court->description ?? 'Professional pickleball court available for scheduled games.' }}
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <!-- Dynamic Booking Button -->
                            @if($court->is_fully_booked)
                                <button disabled class="inline-flex w-full items-center justify-center rounded-xl border border-slate-700 bg-slate-800/50 py-2.5 text-sm font-bold text-slate-500 cursor-not-allowed">
                                    Unavailable
                                </button>
                            @else
                                <a href="{{ route('booking.create', ['court_id' => $court->id]) }}" 
                                class="inline-flex w-full items-center justify-center rounded-xl border border-lime-400/30 bg-lime-400/10 py-2.5 text-sm font-bold text-lime-400 transition hover:bg-lime-400 hover:text-slate-950">
                                    Book this court
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-slate-800 bg-slate-900 p-6 text-slate-400">
                        No active courts are currently available.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Rates Section -->
        <section id="rates" class="scroll-mt-20 py-16 bg-slate-950/50 border-t border-b border-slate-800 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mx-auto text-center">
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Rates</div>
                    <h2 class="mt-1 text-3xl font-extrabold text-white sm:text-4xl">Simple and transparent pricing</h2>
                </div>
                <div class="max-w-lg mx-auto mt-10">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-8 text-center shadow-xl">
                        <div class="text-xs font-bold uppercase tracking-widest text-slate-400">Standard Court Rate</div>
                        <div class="mt-3 text-5xl font-black text-lime-400">
                            ₱{{ number_format((float) env('PICKLEBALL_HOURLY_RATE', 500), 2) }}
                        </div>
                        <div class="mt-2 text-slate-400">per hour</div>
                        <a href="{{ route('booking.create') }}" class="mt-8 inline-block bg-lime-400 text-slate-950 font-bold px-8 py-3 rounded-xl hover:bg-lime-300 transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rules Section -->
        <section id="rules" class="scroll-mt-20 px-6 py-16 max-w-7xl mx-auto">
            <div class="max-w-2xl mb-10">
                <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Court Rules</div>
                <h2 class="mt-1 text-3xl font-extrabold text-white sm:text-4xl">Play fair. Play safe.</h2>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['icon' => '⏱️', 'title' => 'Arrive Early', 'text' => 'Please arrive before your scheduled booking time.'],
                    ['icon' => '🎉', 'title' => 'Have Fun', 'text' => 'Enjoy your time on the court and bring your best energy!'],
                    ['icon' => '🤝', 'title' => 'Respect Others', 'text' => 'Keep the court area clean and respect other players.'],
                    ['icon' => '🏓', 'title' => 'Play Safely', 'text' => 'Follow facility instructions and play responsibly.'],
                ] as $rule)
                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                        <div class="text-3xl">{{ $rule['icon'] }}</div>
                        <h3 class="mt-4 font-bold text-white text-lg">{{ $rule['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-400">{{ $rule['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-8 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-slate-500">
            <p>© {{ date('Y') }} HomeCourt PickleHouse. All rights reserved.</p>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-slate-300 transition">Privacy Policy</a>
                <a href="#" class="hover:text-slate-300 transition">Terms of Service</a>
                <a href="#" class="hover:text-slate-300 transition">Contact Support</a>
            </div>
        </div>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>