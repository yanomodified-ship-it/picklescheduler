<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HomeCourt PickleHouse | Book a Pickleball Court in Panabo City</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{--
        PALETTE (used as plain hex values in the classes below)
        Ink         #1B2B1C   text
        Court       #2F5D34   primary buttons, accents
        Court dark  #1F3F24   hover state, night card, footer
        Sage        #CFDDAE   hero and map panels
        Sage soft   #E5ECD2   rates band, chips
        Paper       #F6F8EE   page background
        Ball        #E4F03A   pickleball yellow (used sparingly)
    --}}
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; }
        .hc-display { font-family: 'Bricolage Grotesque', 'Inter', system-ui, sans-serif; letter-spacing: -0.025em; }
        html { scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }

        a:focus-visible, button:focus-visible { outline: 2px solid #2F5D34; outline-offset: 3px; }
        .hc-on-dark a:focus-visible, .hc-on-dark button:focus-visible { outline-color: #E4F03A; }

        /* Floating pickleballs in the hero and map panels (transform only, so it stays smooth). */
        @keyframes hc-hop {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-20px) rotate(16deg); }
        }
        @keyframes hc-drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50%      { transform: translate(12px, -16px) rotate(-14deg); }
        }
        /* Decoration layers: pure CSS, so sizing/colour never depend on Tailwind having compiled. */
        .hc-layer  { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
        .hc-above  { position: relative; z-index: 1; }
        .hc-float  { position: absolute; display: block; }
        .hc-sm-up, .hc-md-up { display: none; }
        @media (min-width: 640px) { .hc-sm-up { display: block; } }
        @media (min-width: 768px) { .hc-md-up { display: block; } }

        /* The one big hero ball */
        .hc-hero-ball { width: 18rem; height: 18rem; right: -6rem; top: -4rem; color: #E4F03A; opacity: .6; }
        @media (min-width: 768px)  { .hc-hero-ball { width: 22rem; height: 22rem; right: -2.5rem; top: 50%; margin-top: -11rem; } }
        @media (min-width: 1024px) { .hc-hero-ball { width: 30rem; height: 30rem; margin-top: -15rem; opacity: 1; } }

        .hc-hop   { animation: hc-hop 5s ease-in-out infinite; will-change: transform; }
        .hc-drift { animation: hc-drift 8s ease-in-out infinite; will-change: transform; }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition-duration: 0.01ms !important; }
            .hc-hop, .hc-drift { animation: none !important; }
        }

        /* Popups (House Rules + Privacy Policy): plain CSS so layering and sizing never depend on Tailwind compiling. */
        .hc-modal-overlay { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: flex-end; justify-content: center; background: rgba(27, 43, 28, .6); -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px); }
        @media (min-width: 640px) { .hc-modal-overlay { align-items: center; padding: 1.5rem; } }
        .hc-modal-panel { display: flex; flex-direction: column; width: 100%; max-width: 42rem; max-height: 90vh; max-height: 90dvh; overflow: hidden; background: #fff; color: #1B2B1C; border-radius: 2rem 2rem 0 0; }
        @media (min-width: 640px) { .hc-modal-panel { border-radius: 2rem; } }
        .hc-modal-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(27, 43, 28, .1); }
        .hc-modal-title { margin: 0; font-size: 1.5rem; font-weight: 800; line-height: 1.2; }
        .hc-modal-sub { margin: .25rem 0 0; font-size: .875rem; color: rgba(27, 43, 28, .7); }
        .hc-modal-x { flex: none; padding: .5rem; border: 0; border-radius: 9999px; background: transparent; color: rgba(27, 43, 28, .7); cursor: pointer; }
        .hc-modal-x:hover { background: #E5ECD2; color: #1B2B1C; }
        .hc-modal-body { overflow-y: auto; overscroll-behavior: contain; padding: 1.5rem; font-size: .875rem; line-height: 1.65; color: rgba(27, 43, 28, .85); }
        .hc-modal-body section + section { margin-top: 1.5rem; }
        .hc-modal-body h3 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #1B2B1C; }
        .hc-modal-body p { margin: .5rem 0 0; }
        .hc-modal-body ul { margin: .5rem 0 0; padding-left: 1.25rem; list-style: disc; }
        .hc-modal-body li + li { margin-top: .25rem; }
        .hc-modal-body a { color: #2F5D34; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; }
        .hc-modal-body .hc-rules-intro { margin: 0 0 1.5rem; }
        .hc-rules { counter-reset: rule; }
        .hc-rules h3::before { counter-increment: rule; content: counter(rule) ". "; }
        .hc-modal-foot { padding: 1rem 1.5rem; border-top: 1px solid rgba(27, 43, 28, .1); text-align: right; }
        .hc-modal-close { border: 0; border-radius: 9999px; background: #2F5D34; color: #fff; font-size: .875rem; font-weight: 700; padding: .625rem 1.75rem; cursor: pointer; }
        .hc-modal-close:hover { background: #1F3F24; }
        .hc-footer a:focus-visible, .hc-footer button:focus-visible { outline: 2px solid #E4F03A; outline-offset: 3px; }
    </style>
</head>

@php
    // Single source for the address: used in the hero, the map, and the directions link.
    $address = 'Purok 7, San Vicente Panabo City, Panabo, Philippines, 8105';

    // Exact pin from the owner's Google Maps link (HomeCourt Pickle House).
    $lat = 7.3144437;
    $lng = 125.6915676;

    // Icon markup is static, trusted SVG (24x24, stroke style).
    $rules = [
        ['icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>', 'title' => 'Arrive Early', 'text' => 'Please arrive before your scheduled booking time.'],
        ['icon' => '<circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>', 'title' => 'Have Fun', 'text' => 'Enjoy your time on the court and bring your best energy!'],
        ['icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', 'title' => 'Respect Others', 'text' => 'Keep the court area clean and respect other players.'],
        ['icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>', 'title' => 'Play Safely', 'text' => 'Follow facility instructions and play responsibly.'],
    ];
@endphp

<body class="flex min-h-screen flex-col bg-[#F6F8EE] text-[#1B2B1C] antialiased">

    {{-- Shared graphics: defined once, reused with <use>. --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="hc-ball" viewBox="0 0 200 200">
            <path fill="currentColor" fill-rule="evenodd" d="M4.00 100.00a96 96 0 1 0 192 0a96 96 0 1 0 -192 0zM117.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM105.80 120.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM81.80 120.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM69.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM81.80 79.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM105.80 79.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM144.03 113.46a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM130.57 136.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.26 150.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.34 150.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM57.03 136.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM43.57 113.46a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM43.57 86.54a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM57.03 63.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.34 49.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.26 49.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM130.57 63.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM144.03 86.54a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM172.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM168.04 127.02a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM154.32 150.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM133.30 168.42a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.52 177.80a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.08 177.80a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM54.30 168.42a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM33.28 150.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM19.56 127.02a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM14.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM19.56 72.98a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM33.28 49.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM54.30 31.58a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.08 22.20a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.52 22.20a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM133.30 31.58a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM154.32 49.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM168.04 72.98a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0z"/>
        </symbol>
        <symbol id="hc-court" viewBox="0 0 160 100">
            <g fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="8" y="17" width="144" height="66" rx="1"/>
                <line x1="80" y1="11" x2="80" y2="89" stroke-dasharray="2 2.5"/>
                <line x1="57" y1="17" x2="57" y2="83"/>
                <line x1="103" y1="17" x2="103" y2="83"/>
                <line x1="8" y1="50" x2="57" y2="50"/>
                <line x1="103" y1="50" x2="152" y2="50"/>
            </g>
        </symbol>
    </svg>

    <!-- Navigation Header (kept pure white: the logo file has a white background) -->
    <header class="sticky top-0 z-50 border-b border-[#1B2B1C]/10 bg-white/95 backdrop-blur-md" x-data="{ mobileMenu: false }">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3">
            <a href="{{ route('home') }}" class="flex items-center" aria-label="HomeCourt PickleHouse home">
                <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high" class="h-10 w-auto sm:h-12" style="height: 44px; width: auto; max-height: 44px;">
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden items-center gap-8 text-sm font-semibold text-[#1B2B1C]/80 md:flex" aria-label="Main">
                <a href="#courts" class="transition hover:text-[#2F5D34]">Courts</a>
                <a href="#rates" class="transition hover:text-[#2F5D34]">Rates</a>
                <a href="#rules" class="transition hover:text-[#2F5D34]">Rules</a>
                <a href="#find-us" class="transition hover:text-[#2F5D34]">Find us</a>
            </nav>

            <div class="hidden items-center md:flex">
                <a href="{{ route('booking.create') }}" class="rounded-full bg-[#2F5D34] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-[#1F3F24]">Book a court</a>
            </div>

            <!-- Mobile Menu Button -->
            <button type="button" @click="mobileMenu = !mobileMenu" :aria-expanded="mobileMenu" aria-label="Toggle menu" class="text-[#1B2B1C]/80 hover:text-[#1B2B1C] md:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="mobileMenu" x-cloak class="space-y-3 border-t border-[#1B2B1C]/10 bg-white px-6 py-4 md:hidden" aria-label="Mobile">
            <a href="#courts" @click="mobileMenu = false" class="block text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Courts</a>
            <a href="#rates" @click="mobileMenu = false" class="block text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Rates</a>
            <a href="#rules" @click="mobileMenu = false" class="block text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Rules</a>
            <a href="#find-us" @click="mobileMenu = false" class="block text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Find us</a>
            <a href="{{ route('booking.create') }}" class="block rounded-full bg-[#2F5D34] px-5 py-2.5 text-center font-bold text-white hover:bg-[#1F3F24]">Book a court</a>
        </nav>
    </header>

    <main class="flex-grow">

        <!-- Hero Section -->
        <section class="relative mx-3 mt-3 overflow-hidden rounded-[2.5rem] bg-[#CFDDAE] md:mx-6 md:rounded-[4rem]">
            <!-- Decoration layer: big ball + floating pickleballs (motion is switched off for reduced-motion users) -->
            <div class="hc-layer" aria-hidden="true">
                <svg class="hc-float hc-hero-ball" viewBox="0 0 200 200" width="480" height="480"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-hop hc-sm-up" viewBox="0 0 200 200" width="40" height="40" style="left:4%;top:12px;color:#F6F8EE;opacity:.8;animation-delay:-1s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-drift hc-md-up" viewBox="0 0 200 200" width="28" height="28" style="left:38%;top:16px;color:#E4F03A;animation-delay:-3s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-hop" viewBox="0 0 200 200" width="56" height="56" style="left:6%;bottom:12px;color:#F6F8EE;opacity:.8;animation-delay:-2.5s;animation-duration:6s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-drift hc-sm-up" viewBox="0 0 200 200" width="32" height="32" style="left:44%;bottom:24px;color:#E4F03A;animation-delay:-5s;animation-duration:9s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-hop hc-md-up" viewBox="0 0 200 200" width="40" height="40" style="right:10%;bottom:16px;color:#F6F8EE;opacity:.8;animation-delay:-4s;animation-duration:7s"><use href="#hc-ball"/></svg>
            </div>

            <div class="hc-above mx-auto max-w-7xl px-6 pb-20 pt-14 md:px-10 md:pb-28 md:pt-20">
                <div class="max-w-3xl">
                    <h1 class="hc-display text-5xl font-extrabold leading-[1.02] text-[#1B2B1C] sm:text-6xl md:text-7xl">
                        <span class="block">Reserve your court.</span>
                        <span class="block">Serve your best game.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-[#1B2B1C]/80">
                        Private pickleball courts in Panabo City. Pick your hours online, then send your payment receipt on Facebook and we'll confirm your booking.
                    </p>

                    <p class="mt-5 flex items-start gap-2 text-base font-semibold text-[#1B2B1C]">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#2F5D34]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>{{ $address }}</span>
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('booking.create') }}" class="inline-flex items-center justify-center rounded-full bg-[#2F5D34] px-8 py-4 text-base font-bold text-white transition hover:bg-[#1F3F24]">
                            Reserve a court
                        </a>
                        <a href="#courts" class="inline-flex items-center justify-center rounded-full border-2 border-[#1B2B1C]/25 px-8 py-4 text-base font-bold text-[#1B2B1C] transition hover:border-[#1B2B1C] hover:bg-[#F6F8EE]/60">
                            View live courts
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Courts Section -->
        <section id="courts" class="mx-auto max-w-7xl scroll-mt-24 px-6 py-16 md:py-24" x-data="{ courtFilter: 'outdoor' }">
            <div class="mb-10 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h2 class="hc-display text-4xl font-extrabold text-[#1B2B1C] sm:text-5xl">Choose your court</h2>
                    <p class="mt-3 max-w-md text-[#1B2B1C]/75">Select between our available outdoor and indoor courts below.</p>
                </div>

                <!-- Dynamic Filter Buttons -->
                <div class="inline-flex self-start rounded-full bg-[#E5ECD2] p-1.5 text-sm font-bold md:self-auto" role="group" aria-label="Court type">
                    <button
                        type="button"
                        @click="courtFilter = 'outdoor'"
                        :aria-pressed="courtFilter === 'outdoor'"
                        :class="courtFilter === 'outdoor' ? 'bg-[#2F5D34] text-white' : 'text-[#1B2B1C]/70 hover:text-[#1B2B1C]'"
                        class="flex items-center gap-2 rounded-full px-5 py-2 transition duration-200">
                        <span>Outdoor</span>
                        <span class="rounded-full px-2 py-0.5 text-xs" :class="courtFilter === 'outdoor' ? 'bg-white/25 text-white' : 'bg-white text-[#1B2B1C]/70'">
                            {{ $courts->filter(fn($c) => strtolower($c->classification) === 'outdoor')->count() }}
                        </span>
                    </button>

                    <button
                        type="button"
                        @click="courtFilter = 'indoor'"
                        :aria-pressed="courtFilter === 'indoor'"
                        :class="courtFilter === 'indoor' ? 'bg-[#2F5D34] text-white' : 'text-[#1B2B1C]/70 hover:text-[#1B2B1C]'"
                        class="flex items-center gap-2 rounded-full px-5 py-2 transition duration-200">
                        <span>Indoor</span>
                        <span class="rounded-full px-2 py-0.5 text-xs" :class="courtFilter === 'indoor' ? 'bg-white/25 text-white' : 'bg-white text-[#1B2B1C]/70'">
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
                        class="flex flex-col justify-between overflow-hidden rounded-[1.75rem] border border-[#1B2B1C]/10 bg-white transition hover:-translate-y-1 hover:border-[#2F5D34]/40">
                        <div>
                            <!-- Top-down court diagram with a ball in play -->
                            <div class="relative bg-[#2F5D34] px-6 py-8">
                                <svg viewBox="0 0 160 100" class="mx-auto block h-auto w-full max-w-[14rem] text-white/80" aria-hidden="true">
                                    <use href="#hc-court"/>
                                    <use href="#hc-ball" x="113" y="31" width="13" height="13" class="text-[#E4F03A]"/>
                                </svg>
                                <span class="absolute left-4 top-4 rounded-full bg-[#F6F8EE] px-3 py-1 text-xs font-bold capitalize text-[#2F5D34]">
                                    {{ $court->classification ?? 'Outdoor' }}
                                </span>
                            </div>
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="hc-display text-xl font-bold text-[#1B2B1C]">{{ $court->name }}</h3>

                                    <!-- Dynamic Status Badge -->
                                    @if($court->is_fully_booked)
                                        <span class="rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">Fully Booked</span>
                                    @else
                                        <span class="rounded-full bg-[#E5ECD2] px-2.5 py-1 text-xs font-bold text-[#2F5D34]">Active</span>
                                    @endif

                                </div>
                                <p class="mt-2 text-sm leading-relaxed text-[#1B2B1C]/75">
                                    {{ $court->description ?? 'Professional pickleball court available for scheduled games.' }}
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <!-- Dynamic Booking Button -->
                            @if($court->is_fully_booked)
                                <button disabled class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-full border border-gray-200 bg-gray-100 py-2.5 text-sm font-bold text-gray-500">
                                    Unavailable
                                </button>
                            @else
                                <a href="{{ route('booking.create', ['court_id' => $court->id]) }}"
                                   class="inline-flex w-full items-center justify-center rounded-full bg-[#E5ECD2] py-2.5 text-sm font-bold text-[#2F5D34] transition hover:bg-[#2F5D34] hover:text-white">
                                    Book this court
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-[1.75rem] border border-[#1B2B1C]/10 bg-white p-6 text-[#1B2B1C]/75">
                        No active courts are currently available.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Rates Section -->
        <section id="rates" class="scroll-mt-24 bg-[#E5ECD2]">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 py-16 md:py-24 lg:grid-cols-[1fr_1.6fr] lg:items-center">
                <div>
                    <h2 class="hc-display text-4xl font-extrabold text-[#1B2B1C] sm:text-5xl">Simple, transparent pricing</h2>
                    <p class="mt-4 max-w-sm leading-relaxed text-[#1B2B1C]/75">
                        Rates are per hour. Book as many hours as you need. They don't have to be back to back. Pay with GCash or Maya.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <!-- Daytime Rate -->
                    <div class="flex flex-col justify-between rounded-[2rem] border-2 border-[#1B2B1C]/10 bg-white p-8">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#E5ECD2] text-[#2F5D34]">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-bold text-[#1B2B1C]">Day slot</p>
                                    <p class="text-sm text-[#1B2B1C]/70">5:00 AM – 4:00 PM</p>
                                </div>
                            </div>
                            <p class="hc-display mt-8 text-6xl font-extrabold text-[#2F5D34]">₱150</p>
                            <p class="mt-1 text-[#1B2B1C]/70">per hour</p>
                        </div>
                        <a href="{{ route('booking.create') }}" class="mt-8 inline-flex items-center justify-center rounded-full bg-[#2F5D34] px-6 py-3 font-bold text-white transition hover:bg-[#1F3F24]">
                            Book a day slot
                        </a>
                    </div>

                    <!-- Night-time Rate -->
                    <div class="hc-on-dark flex flex-col justify-between rounded-[2rem] bg-[#1F3F24] p-8">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-[#E4F03A]">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-bold text-white">Night slot</p>
                                    <p class="text-sm text-[#CFDDAE]">5:00 PM – 12:00 AM</p>
                                </div>
                            </div>
                            <p class="hc-display mt-8 text-6xl font-extrabold text-[#E4F03A]">₱300</p>
                            <p class="mt-1 text-[#CFDDAE]">per hour</p>
                        </div>
                        <a href="{{ route('booking.create') }}" class="mt-8 inline-flex items-center justify-center rounded-full bg-[#E4F03A] px-6 py-3 font-bold text-[#1B2B1C] transition hover:bg-white">
                            Book a night slot
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rules Section -->
        <section id="rules" class="mx-auto max-w-7xl scroll-mt-24 px-6 py-16 md:py-24" x-data>
            <h2 class="hc-display text-4xl font-extrabold text-[#1B2B1C] sm:text-5xl">Play fair. Play safe.</h2>
            <div class="mt-12 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($rules as $rule)
                    <div class="border-t-2 border-[#1B2B1C] pt-5">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#CFDDAE] text-[#2F5D34]">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $rule['icon'] !!}</svg>
                        </span>
                        <h3 class="hc-display mt-4 text-xl font-bold text-[#1B2B1C]">{{ $rule['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-[#1B2B1C]/75">{{ $rule['text'] }}</p>
                    </div>
                @endforeach
            </div>
            <p class="mt-10">
                <button type="button" @click="$dispatch('open-house-rules')" class="rounded-full border-2 border-[#1B2B1C]/25 px-7 py-3 font-bold text-[#1B2B1C] transition hover:border-[#1B2B1C] hover:bg-[#E5ECD2]">Read the full House Rules</button>
            </p>
        </section>

        <!-- Find Us Section -->
        <section id="find-us" class="relative mx-3 mb-6 scroll-mt-24 overflow-hidden rounded-[2.5rem] bg-[#CFDDAE] px-6 py-16 md:mx-6 md:rounded-[4rem] md:py-20">
            <!-- Decoration layer: floating pickleballs in the empty top and bottom bands -->
            <div class="hc-layer" aria-hidden="true">
                <svg class="hc-float hc-hop" viewBox="0 0 200 200" width="36" height="36" style="left:6%;top:12px;color:#E4F03A;animation-delay:-1.5s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-drift hc-md-up" viewBox="0 0 200 200" width="48" height="48" style="right:8%;top:16px;color:#F6F8EE;opacity:.8;animation-delay:-4s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-drift hc-sm-up" viewBox="0 0 200 200" width="48" height="48" style="left:12%;bottom:12px;color:#F6F8EE;opacity:.8;animation-delay:-6s;animation-duration:9s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-hop" viewBox="0 0 200 200" width="36" height="36" style="right:7%;bottom:12px;color:#E4F03A;animation-delay:-3s;animation-duration:6s"><use href="#hc-ball"/></svg>
            </div>

            <div class="hc-above mx-auto max-w-5xl">
                <h2 class="hc-display text-4xl font-extrabold text-[#1B2B1C] sm:text-5xl">Find us</h2>
                <p class="mt-3 text-lg text-[#1B2B1C]/80">{{ $address }}</p>

                <div class="mt-10 aspect-[4/3] overflow-hidden rounded-[2rem] border-2 border-[#1B2B1C]/15 bg-white sm:aspect-[16/9]">
                    <iframe
                        title="Map showing HomeCourt PickleHouse"
                        src="https://www.google.com/maps?q={{ $lat }},{{ $lng }}&amp;z=18&amp;output=embed"
                        class="h-full w-full"
                        style="border:0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="flex items-center gap-2 font-semibold text-[#1B2B1C]">
                        <svg class="h-5 w-5 text-[#2F5D34]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Courts open 5:00 AM to 12:00 AM</span>
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $lat }},{{ $lng }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center rounded-full bg-[#2F5D34] px-7 py-3 font-bold text-white transition hover:bg-[#1F3F24]">
                            Get directions
                        </a>
                        <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center rounded-full border-2 border-[#1B2B1C]/25 px-7 py-3 font-bold text-[#1B2B1C] transition hover:border-[#1B2B1C] hover:bg-[#F6F8EE]/60">
                            Message us on Facebook
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer + House Rules and Privacy Policy popups. Edit the text below. --}}
    <div x-data="{ open: null, lastFocus: null }"
         x-init="$watch('open', value => {
             document.body.style.overflow = value ? 'hidden' : '';
             if (value) { $nextTick(() => { const btn = value === 'rules' ? $refs.closeRules : $refs.closePrivacy; if (btn) btn.focus(); }); }
             else if (lastFocus) { lastFocus.focus(); }
         })"
         @open-privacy.window="lastFocus = document.activeElement; open = 'privacy'"
         @open-house-rules.window="lastFocus = document.activeElement; open = 'rules'"
         @keydown.escape.window="open = null">

        <!-- Footer -->
        <footer class="hc-footer bg-[#1F3F24] text-[#CFDDAE]">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 py-8 text-sm sm:flex-row">
                <p>© {{ date('Y') }} HomeCourt PickleHouse. All rights reserved.</p>

                <div class="flex items-center gap-6">
                    <button type="button" @click="$dispatch('open-house-rules')" class="underline-offset-4 transition-colors hover:text-white hover:underline">House Rules</button>
                    <button type="button" @click="$dispatch('open-privacy')" class="underline-offset-4 transition-colors hover:text-white hover:underline">Privacy Policy</button>

                    <!-- Facebook Social Link -->
                    <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" aria-label="HomeCourt PickleHouse on Facebook" class="transition-colors hover:text-white">
                        <svg width="20" height="20" class="fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>
            </div>
        </footer>

        <!-- House Rules popup -->
        <div class="hc-modal-overlay" x-show="open === 'rules'" x-cloak x-transition.opacity @click.self="open = null">
            <div class="hc-modal-panel" role="dialog" aria-modal="true" aria-labelledby="rules-title">
                <div class="hc-modal-head">
                    <div>
                        <h2 id="rules-title" class="hc-modal-title hc-display">House Rules</h2>
                        <p class="hc-modal-sub">Last updated: October 2026</p>
                    </div>
                    <button type="button" x-ref="closeRules" @click="open = null" aria-label="Close house rules" class="hc-modal-x"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                </div>

                <div class="hc-modal-body hc-rules">
                    <p class="hc-rules-intro">To keep our courts safe and in great condition for everyone, all players and guests of HomeCourt PickleHouse agree to the following house rules:</p>

                    <section>
                        <h3 class="hc-display">Play within your booked time</h3>
                        <p>Please arrive on time and leave the court promptly when your session ends, so the next players can start on time.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Keep the courts clean</h3>
                        <p>Throw trash, cups, bottles and other waste in the bins. Leave the court clean and ready for the next players.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Food and drinks</h3>
                        <p>Please keep food away from the playing surface. Drinks in closed bottles are fine; clean up any spills right away.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Clean your shoes before playing</h3>
                        <p>Wipe your shoes before stepping onto the court. This keeps dirt and debris off the surface and keeps everyone's footing safe.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Protect the court surface</h3>
                        <p>Please do not bring in furniture, sharp objects or anything else that could damage the playing surface.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">No smoking or vaping</h3>
                        <p>Smoking and vaping are not allowed on the courts or in the playing areas.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Play safely and respectfully</h3>
                        <p>No aggressive behavior, abusive language, or conduct that could endanger or disturb other players.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Keep your belongings secure</h3>
                        <p>Keep bags, phones, paddles and other items with you or in a safe place. HomeCourt PickleHouse is not responsible for lost or unattended belongings.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Damages and misuse</h3>
                        <p>Intentional or careless damage to the courts, equipment or property may be charged for repair or replacement.</p>
                    </section>
                </div>

                <div class="hc-modal-foot"><button type="button" @click="open = null" class="hc-modal-close">Close</button></div>
            </div>
        </div>

        <!-- Privacy Policy popup -->
        <div class="hc-modal-overlay" x-show="open === 'privacy'" x-cloak x-transition.opacity @click.self="open = null">
            <div class="hc-modal-panel" role="dialog" aria-modal="true" aria-labelledby="privacy-title">
                <div class="hc-modal-head">
                    <div>
                        <h2 id="privacy-title" class="hc-modal-title hc-display">Privacy Policy</h2>
                        <p class="hc-modal-sub">Last updated: October 2026</p>
                    </div>
                    <button type="button" x-ref="closePrivacy" @click="open = null" aria-label="Close privacy policy" class="hc-modal-x"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                </div>

                <div class="hc-modal-body">
                    <section>
                        <h3 class="hc-display">Who we are</h3>
                        <p>HomeCourt PickleHouse ("we", "us") runs this website so you can book pickleball courts at Purok 7, San Vicente Panabo City, Panabo, Philippines, 8105.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">What we collect</h3>
                        <p>When you book a court we collect only what we need to run your booking:</p>
                        <ul>
                            <li>The Facebook name and contact number you type into the booking form</li>
                            <li>The number of players, and the court, date and time slots you choose</li>
                            <li>The payment method you pick (GCash or Maya)</li>
                            <li>The booking reference and status we assign to your booking</li>
                        </ul>
                        <p>We never ask for your e-wallet PIN, card details or social media password. Payment receipts you send through Facebook Messenger stay on Facebook; they are not uploaded to this website.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Why we use it</h3>
                        <p>We use your details to reserve your court, verify your payment, contact you about your booking, issue your invoice, prevent double bookings, and keep our business records.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Who can see it</h3>
                        <p>Only our staff who manage bookings, and the website hosting and database providers that store the data for us. We do not sell your information or use it for advertising. We may share it if the law requires us to.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Third-party content</h3>
                        <p>This website loads Google Fonts, and the map on our home page is provided by Google Maps. Google may receive your IP address and browser details when these load.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Cookies</h3>
                        <p>We use only essential cookies that keep the site working and protect our forms from misuse. We do not use advertising cookies.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">How long we keep it</h3>
                        <p>We keep booking records only as long as we need them for bookkeeping, customer support and resolving disputes, then we delete or anonymise them.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Your rights</h3>
                        <p>Under the Data Privacy Act of 2012 (Republic Act No. 10173) you may ask to see, correct or delete your personal data, object to how we use it, and file a complaint with the National Privacy Commission at <a href="https://privacy.gov.ph" target="_blank" rel="noopener noreferrer">privacy.gov.ph</a>.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Contact us</h3>
                        <p>To make a request or ask a question about your data, message our <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer">Facebook page</a>.</p>
                    </section>
                    <section>
                        <h3 class="hc-display">Changes to this policy</h3>
                        <p>We may update this policy from time to time. The date at the top shows when it was last changed.</p>
                    </section>
                </div>

                <div class="hc-modal-foot"><button type="button" @click="open = null" class="hc-modal-close">Close</button></div>
            </div>
        </div>
    </div>

</body>
</html>