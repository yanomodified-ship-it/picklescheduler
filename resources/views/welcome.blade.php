<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PickleScheduler') }} - Court Reservations</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-lime-400 selection:text-slate-950">

    <!-- Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <span class="bg-lime-400 text-slate-950 text-xl font-extrabold px-2.5 py-1 rounded-lg">P</span>
                <span class="text-xl font-extrabold tracking-tight">Pickle<span class="text-lime-400">Scheduler</span></span>
            </a>
            <div class="flex items-center gap-4">
                <a href="#schedule" class="text-sm text-slate-400 hover:text-white transition">Today's Schedule</a>
                <a href="#book" class="bg-lime-400 hover:bg-lime-300 text-slate-950 text-sm font-bold px-4 py-2 rounded-lg transition">Reserve Court</a>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-6xl w-full mx-auto px-6 py-12 space-y-16">

        <!-- Hero Section -->
        <section class="text-center space-y-4 max-w-3xl mx-auto">
            <span class="inline-block bg-lime-400/10 text-lime-400 border border-lime-400/20 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                Pay On-Site • No Online Payment Required
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight">
                Reserve Your Court. <br><span class="text-lime-400">Serve Your Best Game.</span>
            </h1>
            <p class="text-slate-400 text-base sm:text-lg">
                Book private sessions or join open play queues instantly. Pay directly at the front desk when you arrive.
            </p>
        </section>

        <!-- Status & Flash Messages -->
        @if(session('success'))
            <div class="p-4 bg-lime-500/10 border border-lime-500/30 rounded-xl text-lime-400 text-sm font-medium text-center">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            
            <!-- Reservation Form -->
            <section id="book" class="lg:col-span-7 bg-slate-900 border border-slate-800 p-8 rounded-2xl shadow-xl space-y-6">
                <div class="border-b border-slate-800 pb-4">
                    <h2 class="text-xl font-bold text-white">Book a Court Slot</h2>
                    <p class="text-xs text-slate-400 mt-1">Fill out your details below to hold your preferred court time.</p>
                </div>

                <form action="{{ route('bookings.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Full Name</label>
                            <input type="text" name="player_name" placeholder="John Doe" required
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-lime-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Contact Phone</label>
                            <input type="tel" name="player_phone" placeholder="0912 345 6789" required
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-lime-400 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Select Court</label>
                            <select name="court_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-lime-400 transition">
                                @isset($courts)
                                    @foreach($courts as $court)
                                        <option value="{{ $court->id }}">{{ $court->name }}</option>
                                    @endforeach
                                @else
                                    <option value="1">Court 1</option>
                                    <option value="2">Court 2</option>
                                @endisset
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Session Type</label>
                            <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-lime-400 transition">
                                <option value="private">Private Session</option>
                                <option value="open_play">Open Play / Queue</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Date</label>
                        <input type="date" name="booking_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-lime-400 transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Start Time</label>
                            <input type="time" name="start_time" required
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-lime-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">End Time</label>
                            <input type="time" name="end_time" required
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-lime-400 transition">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-lime-400 hover:bg-lime-300 text-slate-950 font-bold text-sm py-3 rounded-lg transition shadow-lg shadow-lime-400/10 mt-2">
                        Confirm Court Reservation
                    </button>
                </form>
            </section>

            <!-- Live Schedule Display -->
            <section id="schedule" class="lg:col-span-5 bg-slate-900 border border-slate-800 p-8 rounded-2xl shadow-xl space-y-6">
                <div class="border-b border-slate-800 pb-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-white">Today's Schedule</h2>
                        <p class="text-xs text-slate-400 mt-1">{{ date('M d, Y') }}</p>
                    </div>
                    <span class="h-2 w-2 rounded-full bg-lime-400 animate-pulse"></span>
                </div>

                <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                    @isset($todayBookings)
                        @forelse($todayBookings as $booking)
                            <div class="flex items-center justify-between p-3.5 bg-slate-950 border border-slate-800/80 rounded-xl">
                                <div class="space-y-1">
                                    <p class="text-sm font-semibold text-white">{{ $booking->player_name }}</p>
                                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-lime-400 px-2 py-0.5 rounded">
                                        {{ $booking->type === 'open_play' ? 'Open Play' : 'Private' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-mono text-slate-300">{{ date('g:i A', strtotime($booking->start_time)) }}</p>
                                    <p class="text-[10px] text-slate-500">to {{ date('g:i A', strtotime($booking->end_time)) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-500 text-sm">
                                No court reservations scheduled for today yet.
                            </div>
                        @endforelse
                    @else
                        <!-- Placeholder items when database data is omitted -->
                        <div class="flex items-center justify-between p-3.5 bg-slate-950 border border-slate-800/80 rounded-xl">
                            <div>
                                <p class="text-sm font-semibold text-white">Sample Player</p>
                                <span class="text-[10px] font-bold uppercase text-lime-400 bg-slate-800 px-2 py-0.5 rounded">Private</span>
                            </div>
                            <p class="text-xs font-mono text-slate-400">4:00 PM - 5:00 PM</p>
                        </div>
                    @endisset
                </div>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/40 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} PickleScheduler. All rights reserved. Pay at desk upon arrival.
    </footer>

</body>
</html>