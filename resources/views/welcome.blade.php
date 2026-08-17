{{-- =========================================================================
resources/views/welcome.blade.php
Pickleball Court Booking & Management System
Laravel 11 + Blade + Tailwind CSS (Dark Slate Theme)
============================================================================ --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pickleball Courts | Court Booking</title>

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
                <span class="text-3xl">🏓</span>
                <span class="text-2xl font-extrabold tracking-tight text-lime-400">Pickle<span class="text-white">Scheduler</span></span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8 text-sm font-semibold text-slate-300">
                <a href="#features" class="hover:text-lime-400 transition">Features</a>
                <a href="#courts" class="hover:text-lime-400 transition">Courts</a>
                <a href="#rates" class="hover:text-lime-400 transition">Rates</a>
                <a href="#rules" class="hover:text-lime-400 transition">Rules</a>
            </nav>

            <div class="hidden md:flex items-center space-x-3">
                <a href="#booking" class="text-sm font-semibold bg-lime-400 text-slate-950 px-5 py-2.5 rounded-lg hover:bg-lime-300 transition shadow-lg shadow-lime-400/20">Book a Court</a>
            </div>

            <!-- Mobile menu button -->
            <button type="button" @click="mobileMenu = !mobileMenu" class="md:hidden text-slate-300 hover:text-white">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="mobileMenu" x-cloak class="md:hidden border-t border-slate-800 px-6 py-4 space-y-3 bg-slate-900">
            <a href="#features" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-300 hover:text-lime-400">Features</a>
            <a href="#courts" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-300 hover:text-lime-400">Courts</a>
            <a href="#rates" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-300 hover:text-lime-400">Rates</a>
            <a href="#rules" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-300 hover:text-lime-400">Rules</a>
            <a href="#booking" @click="mobileMenu = false" class="block text-center font-semibold bg-lime-400 text-slate-950 px-5 py-2.5 rounded-lg hover:bg-lime-300">Book a Court</a>
        </nav>
    </header>

    <!-- Flash / Validation Messages -->
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-6 pt-6 w-full">
            <div class="rounded-2xl border border-red-500/30 bg-red-900/20 p-4 text-red-300">
                <div class="font-bold">Please correct the following:</div>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-6 pt-6 w-full">
            <div class="rounded-2xl border border-lime-500/30 bg-lime-900/20 p-4 text-lime-300">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main>
    @if (isset($confirmationBooking))

        <!-- Confirmation View -->
        <section class="min-h-[70vh] px-6 py-12">
            <div class="max-w-3xl mx-auto">
                <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-950 shadow-2xl">
                    <div class="bg-gradient-to-r from-lime-500 to-emerald-500 px-6 py-10 text-center text-slate-950">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-950 text-3xl text-lime-400 font-bold">
                            ✓
                        </div>
                        <h1 class="mt-5 text-3xl font-extrabold sm:text-4xl">Booking Submitted!</h1>
                        <p class="mx-auto mt-3 max-w-xl text-slate-900 font-medium">
                            Your booking request has been received and is awaiting payment verification.
                        </p>
                    </div>

                    <div class="p-6 sm:p-10 space-y-6">
                        <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 text-center">
                            <div class="text-xs font-bold uppercase tracking-widest text-slate-400">Booking Reference</div>
                            <div class="mt-2 text-3xl font-black tracking-wider text-lime-400">
                                {{ $confirmationBooking->booking_reference }}
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                <div class="text-xs font-semibold uppercase text-slate-400">Customer</div>
                                <div class="mt-1 font-bold text-white">{{ $confirmationBooking->customer?->name ?? '—' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                <div class="text-xs font-semibold uppercase text-slate-400">Court</div>
                                <div class="mt-1 font-bold text-white">{{ $confirmationBooking->court?->name ?? '—' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                <div class="text-xs font-semibold uppercase text-slate-400">Date</div>
                                <div class="mt-1 font-bold text-white">{{ \Carbon\Carbon::parse($confirmationBooking->booking_date)->format('F d, Y') }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                <div class="text-xs font-semibold uppercase text-slate-400">Time</div>
                                <div class="mt-1 font-bold text-white">
                                    {{ \Carbon\Carbon::parse($confirmationBooking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($confirmationBooking->end_time)->format('g:i A') }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                <div class="text-xs font-semibold uppercase text-slate-400">Players</div>
                                <div class="mt-1 font-bold text-white">{{ $confirmationBooking->number_of_players ?? '—' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                <div class="text-xs font-semibold uppercase text-slate-400">Total</div>
                                <div class="mt-1 font-bold text-lime-400">₱{{ number_format((float) $confirmationBooking->total_price, 2) }}</div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-amber-500/10 border border-amber-500/20 p-5">
                                <div class="font-bold text-amber-400">Booking Status</div>
                                <div class="mt-2 inline-flex rounded-full bg-amber-400/20 px-3 py-1 text-sm font-bold text-amber-300">
                                    {{ $confirmationBooking->booking_status }}
                                </div>
                            </div>
                            <div class="rounded-2xl bg-blue-500/10 border border-blue-500/20 p-5">
                                <div class="font-bold text-blue-400">Payment Status</div>
                                <div class="mt-2 inline-flex rounded-full bg-blue-400/20 px-3 py-1 text-sm font-bold text-blue-300">
                                    {{ $confirmationBooking->payment_status }}
                                </div>
                            </div>
                        </div>

                        <div class="text-center pt-4">
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-lime-400 px-8 py-3 font-bold text-slate-950 transition hover:bg-lime-300">
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @else

        <!-- Hero Section -->
        <section class="relative pt-16 pb-20 px-6 max-w-7xl mx-auto text-center">
            <div class="inline-block bg-slate-800 text-lime-400 text-xs font-bold px-4 py-1.5 rounded-full mb-6 border border-slate-700">
                🎾 Open Play & Private Court Bookings Now Live
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6">
                Reserve Your Court. <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-400 to-emerald-400">Serve Your Best Game.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10">
                Seamless pickleball court reservations, open-play queueing, and match coordination—all in one place.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#booking" class="bg-lime-400 text-slate-950 font-bold px-8 py-4 rounded-xl text-lg hover:bg-lime-300 transition shadow-xl shadow-lime-400/20">
                    Reserve Court Now
                </a>
                <a href="#courts" class="bg-slate-800 text-white font-semibold px-8 py-4 rounded-xl text-lg hover:bg-slate-700 border border-slate-700 transition">
                    View Live Courts
                </a>
            </div>
        </section>

        <!-- Feature Grid -->
        <section id="features" class="py-16 bg-slate-950/50 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-slate-900 p-8 rounded-2xl border border-slate-800">
                        <div class="w-12 h-12 bg-lime-400/10 text-lime-400 rounded-xl flex items-center justify-center text-2xl mb-6">⚡</div>
                        <h3 class="text-xl font-bold mb-3">Real-Time Booking</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Check court availability in real time and lock in your preferred time slot instantly with no hassle.
                        </p>
                    </div>
                    <div class="bg-slate-900 p-8 rounded-2xl border border-slate-800">
                        <div class="w-12 h-12 bg-lime-400/10 text-lime-400 rounded-xl flex items-center justify-center text-2xl mb-6">👥</div>
                        <h3 class="text-xl font-bold mb-3">Open Play Queues</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Join open play rotation, see who is on deck, and get notified when it's your turn to take the court.
                        </p>
                    </div>
                    <div class="bg-slate-900 p-8 rounded-2xl border border-slate-800">
                        <div class="w-12 h-12 bg-lime-400/10 text-lime-400 rounded-xl flex items-center justify-center text-2xl mb-6">🏆</div>
                        <h3 class="text-xl font-bold mb-3">Tournaments & Events</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Sign up for weekend round-robins, local leagues, and skill-level based matchmaking events.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Courts Section -->
        <section id="courts" class="scroll-mt-20 px-6 py-16 max-w-7xl mx-auto">
            <div class="max-w-2xl mb-10">
                <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Our Courts</div>
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white mt-1">Choose your court</h2>
                <p class="mt-2 text-slate-400">We currently have {{ $courts->count() }} active courts available for booking.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($courts as $court)
                    <article class="group overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 transition hover:-translate-y-1 hover:border-slate-700">
                        <div class="flex h-36 items-center justify-center bg-slate-800">
                            <span class="text-6xl transition group-hover:scale-110">🏓</span>
                        </div>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-lg font-bold text-white">{{ $court->name }}</h3>
                                <span class="rounded-full bg-lime-400/10 px-2.5 py-1 text-xs font-bold text-lime-400 border border-lime-400/20">Active</span>
                            </div>
                            <p class="mt-2 text-sm leading-relaxed text-slate-400">
                                {{ $court->description ?? 'Professional pickleball court available for scheduled games.' }}
                            </p>
                            <a href="#booking" class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-lime-400/30 bg-lime-400/10 py-2.5 text-sm font-bold text-lime-400 transition hover:bg-lime-400 hover:text-slate-950">
                                Book this court
                            </a>
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
                        <a href="#booking" class="mt-8 inline-block bg-lime-400 text-slate-950 font-bold px-8 py-3 rounded-xl hover:bg-lime-300 transition">
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
                    ['icon' => '👟', 'title' => 'Proper Shoes', 'text' => 'Use appropriate non-marking court footwear.'],
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

        <!-- Booking Section -->
        <section id="booking" class="scroll-mt-20 border-t border-slate-800 bg-slate-950 px-6 py-16">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mb-10">
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Book Now</div>
                    <h2 class="mt-1 text-3xl font-extrabold text-white sm:text-4xl">Reserve your court</h2>
                    <p class="mt-2 text-slate-400">Complete the form below. Your booking remains pending until payment verification.</p>
                </div>

                <form id="bookingForm" method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data" x-data="bookingForm()" @submit="prepareSubmit">
                    @csrf
                    <div class="grid gap-8 lg:grid-cols-3">
                        <div class="space-y-6 lg:col-span-2">
                            
                            <!-- Step 1: Schedule -->
                            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                                <div class="mb-6">
                                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Step 1</div>
                                    <h3 class="text-xl font-bold text-white mt-1">Select your schedule</h3>
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="booking_date" class="mb-2 block text-sm font-semibold text-slate-300">Booking Date</label>
                                        <input id="booking_date" name="booking_date" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('booking_date') }}" required x-model="bookingDate" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div>
                                        <label for="court_id" class="mb-2 block text-sm font-semibold text-slate-300">Court</label>
                                        <select id="court_id" name="court_id" required x-model="courtId" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                            <option value="">Select a court</option>
                                            @foreach ($courts as $court)
                                                <option value="{{ $court->id }}" data-rate="{{ $court->price_per_hour ?? $court->hourly_rate ?? $court->rate ?? env('PICKLEBALL_HOURLY_RATE', 500) }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                                    {{ $court->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="start_time" class="mb-2 block text-sm font-semibold text-slate-300">Start Time</label>
                                        <select id="start_time" name="start_time" required x-model="startTime" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                            <option value="">Select start time</option>
                                            @for ($hour = 6; $hour <= 22; $hour++)
                                                @foreach ([0, 30] as $minute)
                                                    @php $time = sprintf('%02d:%02d', $hour, $minute); @endphp
                                                    @if ($time < '23:00')
                                                        <option value="{{ $time }}" {{ old('start_time') === $time ? 'selected' : '' }}>
                                                            {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label for="end_time" class="mb-2 block text-sm font-semibold text-slate-300">End Time</label>
                                        <select id="end_time" name="end_time" required x-model="endTime" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                            <option value="">Select end time</option>
                                            @for ($hour = 6; $hour <= 23; $hour++)
                                                @foreach ([0, 30] as $minute)
                                                    @php $time = sprintf('%02d:%02d', $hour, $minute); @endphp
                                                    @if ($time <= '23:00')
                                                        <option value="{{ $time }}" {{ old('end_time') === $time ? 'selected' : '' }}>
                                                            {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Customer -->
                            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                                <div class="mb-6">
                                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Step 2</div>
                                    <h3 class="text-xl font-bold text-white mt-1">Your details</h3>
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-300">Full Name</label>
                                        <input id="name" name="name" type="text" maxlength="100" autocomplete="name" value="{{ old('name') }}" required x-model="customerName" placeholder="Juan Dela Cruz" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div>
                                        <label for="contact_number" class="mb-2 block text-sm font-semibold text-slate-300">Contact Number</label>
                                        <input id="contact_number" name="contact_number" type="tel" maxlength="30" autocomplete="tel" value="{{ old('contact_number') }}" required placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div>
                                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-300">Email Address <span class="text-slate-500 font-normal">(Optional)</span></label>
                                        <input id="email" name="email" type="email" maxlength="150" autocomplete="email" value="{{ old('email') }}" placeholder="you@example.com" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="number_of_players" class="mb-2 block text-sm font-semibold text-slate-300">Number of Players</label>
                                        <input id="number_of_players" name="number_of_players" type="number" min="1" max="30" value="{{ old('number_of_players', 2) }}" required x-model.number="players" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Payment -->
                            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                                <div class="mb-6">
                                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Step 3</div>
                                    <h3 class="text-xl font-bold text-white mt-1">Payment & receipt</h3>
                                </div>
                                <div class="rounded-2xl bg-slate-950 p-5 border border-slate-800 text-slate-300 mb-6">
                                    <div class="text-sm font-bold text-lime-400 mb-2">Payment Instructions</div>
                                    <p class="text-sm"><strong class="text-white">GCash:</strong> 09XX-XXX-XXXX</p>
                                    <p class="text-sm"><strong class="text-white">Account Name:</strong> Pickleball Court</p>
                                </div>
                                <div class="space-y-5">
                                    <div>
                                        <label for="payment_reference" class="mb-2 block text-sm font-semibold text-slate-300">Transaction Reference Number</label>
                                        <input id="payment_reference" name="payment_reference" type="text" maxlength="100" value="{{ old('payment_reference') }}" required placeholder="Enter reference number" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div>
                                        <label for="payment_receipt" class="mb-2 block text-sm font-semibold text-slate-300">Payment Receipt File</label>
                                        <input id="payment_receipt" name="payment_receipt" type="file" accept=".jpg,.jpeg,.png,.pdf" required class="block w-full text-sm text-slate-400 border border-slate-700 rounded-xl bg-slate-800 cursor-pointer file:mr-4 file:py-3 file:px-4 file:border-0 file:bg-slate-700 file:text-slate-200 hover:file:bg-slate-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Sidebar -->
                        <aside class="lg:col-span-1">
                            <div class="sticky top-24 rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-xl space-y-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Summary</div>
                                <h3 class="text-2xl font-bold text-white">Review booking</h3>

                                <div class="divide-y divide-slate-800 text-sm">
                                    <div class="flex justify-between py-3">
                                        <span class="text-slate-400">Customer</span>
                                        <span class="font-bold text-white text-right" x-text="customerName || 'Not provided'"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-slate-400">Date</span>
                                        <span class="font-bold text-white text-right" x-text="formattedDate"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-slate-400">Court</span>
                                        <span class="font-bold text-white text-right" x-text="courtName"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-slate-400">Time</span>
                                        <span class="font-bold text-white text-right" x-text="formattedTime"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-slate-400">Duration</span>
                                        <span class="font-bold text-white text-right" x-text="durationLabel"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-slate-400">Players</span>
                                        <span class="font-bold text-white text-right" x-text="players || '—'"></span>
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-slate-950 p-4 border border-slate-800">
                                    <div class="text-xs font-bold text-slate-400">Total Price</div>
                                    <div class="text-3xl font-black text-lime-400 mt-1" x-text="formattedTotal"></div>
                                </div>

                                <button type="submit" :disabled="submitting" class="w-full rounded-xl bg-lime-400 py-4 font-bold text-slate-950 hover:bg-lime-300 transition shadow-lg shadow-lime-400/20 disabled:opacity-50">
                                    <span x-show="!submitting">Submit Booking</span>
                                    <span x-show="submitting" x-cloak>Submitting...</span>
                                </button>
                            </div>
                        </aside>
                    </div>
                </form>
            </div>
        </section>

    @endif
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-8 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-slate-500">
            <p>© {{ date('Y') }} PickleScheduler. All rights reserved.</p>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-slate-300 transition">Privacy Policy</a>
                <a href="#" class="hover:text-slate-300 transition">Terms of Service</a>
                <a href="#" class="hover:text-slate-300 transition">Contact Support</a>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function bookingForm() {
            return {
                bookingDate: @js(old('booking_date', '')),
                courtId: @js(old('court_id', '')),
                startTime: @js(old('start_time', '')),
                endTime: @js(old('end_time', '')),
                customerName: @js(old('name', '')),
                players: @js((int) old('number_of_players', 2)),
                submitting: false,

                get courtName() {
                    if (!this.courtId) return 'Not selected';
                    const select = document.getElementById('court_id');
                    const option = select?.querySelector(`option[value="${this.courtId}"]`);
                    return option?.textContent.trim() || 'Not selected';
                },

                get hourlyRate() {
                    if (!this.courtId) return Number(@json((float) env('PICKLEBALL_HOURLY_RATE', 500)));
                    const select = document.getElementById('court_id');
                    const option = select?.querySelector(`option[value="${this.courtId}"]`);
                    return Number(option?.dataset.rate || @json((float) env('PICKLEBALL_HOURLY_RATE', 500)));
                },

                get durationMinutes() {
                    if (!this.startTime || !this.endTime) return 0;
                    const [startHour, startMinute] = this.startTime.split(':').map(Number);
                    const [endHour, endMinute] = this.endTime.split(':').map(Number);
                    return Math.max(0, (endHour * 60 + endMinute) - (startHour * 60 + startMinute));
                },

                get durationLabel() {
                    const minutes = this.durationMinutes;
                    if (!minutes) return 'Not selected';
                    const hours = Math.floor(minutes / 60);
                    const remaining = minutes % 60;
                    return remaining === 0 ? `${hours} ${hours === 1 ? 'hour' : 'hours'}` : `${hours}h ${remaining}m`;
                },

                get formattedDate() {
                    if (!this.bookingDate) return 'Not selected';
                    const date = new Date(`${this.bookingDate}T00:00:00`);
                    return Number.isNaN(date.getTime()) ? 'Not selected' : new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(date);
                },

                formatTime(time) {
                    if (!time) return '';
                    const [hour, minute] = time.split(':').map(Number);
                    const suffix = hour >= 12 ? 'PM' : 'AM';
                    const displayHour = hour % 12 || 12;
                    return `${displayHour}:${String(minute).padStart(2, '0')} ${suffix}`;
                },

                get formattedTime() {
                    if (!this.startTime || !this.endTime) return 'Not selected';
                    return `${this.formatTime(this.startTime)} - ${this.formatTime(this.endTime)}`;
                },

                get totalPrice() {
                    return this.durationMinutes ? (this.hourlyRate * (this.durationMinutes / 60)) : 0;
                },

                get formattedTotal() {
                    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 2 }).format(this.totalPrice);
                },

                prepareSubmit(event) {
                    if (!this.bookingDate || !this.courtId || !this.startTime || !this.endTime) return;
                    if (this.durationMinutes < 30) {
                        event.preventDefault();
                        alert('A booking must be at least 30 minutes long.');
                        return;
                    }
                    this.submitting = true;
                }
            };
        }
    </script>
</body>
</html>