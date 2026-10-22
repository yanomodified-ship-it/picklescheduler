<!-- Website by jondev -->
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
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=1">
</head>

@php
    
    $address = 'Purok 1, Salvacion Panabo City, Davao Del Norte, Philippines';

    
    $lat = 7.3144437;
    $lng = 125.6915676;

    
    $rules = [
        ['icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>', 'title' => 'Arrive Early', 'text' => 'Please arrive before your scheduled booking time.'],
        ['icon' => '<circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>', 'title' => 'Have Fun', 'text' => 'Enjoy your time on the court and bring your best energy!'],
        ['icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', 'title' => 'Respect Others', 'text' => 'Keep the court area clean and respect other players.'],
        ['icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>', 'title' => 'Play Safely', 'text' => 'Follow facility instructions and play responsibly.'],
    ];
@endphp

<body class="flex min-h-screen flex-col bg-[#F6F8EE] text-[#1B2B1C] antialiased">

{{-- Loading screen --}}
<style>
    #hc-loader{position:fixed;inset:0;z-index:99999;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:18px;background:#ffffff;transition:opacity .5s ease,visibility .5s ease}
    #hc-loader.hc-hide{opacity:0;visibility:hidden}
    #hc-loader img{height:64px;width:auto}
    .hc-ball-wrap{position:relative;width:40px;height:64px}
    .hc-ball{position:absolute;left:6px;top:0;width:28px;height:28px;border-radius:50%;background:#E4F03A;box-shadow:inset 0 0 0 3px rgba(47,93,52,.25);animation:hc-bounce .6s cubic-bezier(.3,0,.7,1) infinite alternate}
    .hc-shadow{position:absolute;left:8px;bottom:6px;width:24px;height:6px;border-radius:50%;background:rgba(27,43,28,.18);animation:hc-shadow .6s cubic-bezier(.3,0,.7,1) infinite alternate}
    @keyframes hc-bounce{from{transform:translateY(0)}to{transform:translateY(26px)}}
    @keyframes hc-shadow{from{transform:scale(.5);opacity:.3}to{transform:scale(1);opacity:1}}
    #hc-loader p{margin:0;font:600 13px/1 Inter,system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase;color:#2F5D34}
    @media (prefers-reduced-motion:reduce){.hc-ball,.hc-shadow{animation:none}}
</style>
<noscript><style>#hc-loader{display:none}</style></noscript>

<div id="hc-loader" role="status" aria-live="polite" aria-label="Loading HomeCourt PickleHouse">
    <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse">
    <div class="hc-ball-wrap"><div class="hc-ball"></div><div class="hc-shadow"></div></div>
</div>

<script>
(function () {
    var loader = document.getElementById('hc-loader');
    if (!loader) return;

    
    try {
        if (sessionStorage.getItem('hc_loaded') === '1') { loader.remove(); return; }
    } catch (e) {}

    var start = Date.now(), MIN = 600, MAX = 2500, done = false;

    function hide() {
        if (done) return;
        done = true;
        var wait = Math.max(0, MIN - (Date.now() - start));
        setTimeout(function () {
            loader.classList.add('hc-hide');
            try { sessionStorage.setItem('hc_loaded', '1'); } catch (e) {}
            setTimeout(function () { loader.remove(); }, 600);
        }, wait);
    }

    if (document.readyState === 'complete') hide();
    else window.addEventListener('load', hide);

    setTimeout(hide, MAX); 
})();
</script>

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

    {{-- Navigation Header (kept pure white: the logo file has a white background) --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md"
            :style="{ boxShadow: scrolled ? '0 8px 24px -12px rgba(27,43,28,.35)' : '0 1px 0 rgba(27,43,28,.08)' }"
            style="transition:box-shadow .3s ease;"
            x-data="{
                mobileMenu: false,
                scrolled: false,
                progress: 0,
                active: '',
                update() {
                    this.scrolled = window.scrollY > 8;
                    const h = document.documentElement.scrollHeight - window.innerHeight;
                    this.progress = h > 0 ? Math.min(100, (window.scrollY / h) * 100) : 0;
                    let cur = '';
                    ['courts', 'rates', 'rules', 'find-us'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el && el.getBoundingClientRect().top <= 140) cur = id;
                    });
                    this.active = cur;
                }
            }"
            x-init="update()"
            @scroll.window.passive="update()"
            @resize.window="update()">
        <style>
            @media (prefers-reduced-motion: no-preference){ html{scroll-behavior:smooth} }
            .hc-nav a, .hc-mnav a{position:relative}
            .hc-nav .hc-u{position:absolute;left:0;bottom:-2px;height:2px;width:0;border-radius:9999px;background:#2F5D34;transition:width .3s ease}
            .hc-nav a:hover .hc-u, .hc-nav a.hc-active .hc-u{width:100%}
            .hc-nav a.hc-active, .hc-mnav a.hc-active{color:#2F5D34}
        </style>

        <div class="mx-auto flex max-w-7xl items-center justify-between px-6"
             :style="{ paddingTop: scrolled ? '8px' : '12px', paddingBottom: scrolled ? '8px' : '12px' }"
             style="transition:padding .25s ease;">
            <a href="{{ route('home') }}" class="flex items-center" aria-label="HomeCourt PickleHouse home">
                <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high"
                     class="w-auto"
                     :style="{ height: scrolled ? '36px' : '44px' }"
                     style="height:44px;width:auto;max-height:44px;transition:height .25s ease;">
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hc-nav hidden items-center gap-8 text-sm font-semibold text-[#1B2B1C]/80 md:flex" aria-label="Main">
                <a href="#courts" :class="{ 'hc-active': active === 'courts' }" class="py-1 transition hover:text-[#2F5D34]">Courts<span class="hc-u"></span></a>
                <a href="#rates" :class="{ 'hc-active': active === 'rates' }" class="py-1 transition hover:text-[#2F5D34]">Rates<span class="hc-u"></span></a>
                <a href="#rules" :class="{ 'hc-active': active === 'rules' }" class="py-1 transition hover:text-[#2F5D34]">Rules<span class="hc-u"></span></a>
                <a href="#find-us" :class="{ 'hc-active': active === 'find-us' }" class="py-1 transition hover:text-[#2F5D34]">Find us<span class="hc-u"></span></a>
            </nav>

            <div class="hidden items-center md:flex">
                <a href="{{ route('booking.create') }}" class="group inline-flex items-center gap-2 rounded-full bg-[#2F5D34] px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1F3F24] hover:shadow-md">
                    Book a court
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <button type="button" @click="mobileMenu = !mobileMenu" :aria-expanded="mobileMenu" aria-label="Toggle menu" class="flex h-10 w-10 items-center justify-center rounded-full border border-[#1B2B1C]/15 text-[#1B2B1C]/80 transition hover:bg-[#E5ECD2] hover:text-[#1B2B1C] md:hidden">
                <svg x-show="!mobileMenu" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenu" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation --}}
        <nav x-show="mobileMenu" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="hc-mnav border-t border-[#1B2B1C]/10 bg-white px-6 pb-5 pt-2 md:hidden" aria-label="Mobile">
            <a href="#courts" @click="mobileMenu = false" :class="{ 'hc-active': active === 'courts' }" class="block border-b border-[#1B2B1C]/10 py-3 text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Courts</a>
            <a href="#rates" @click="mobileMenu = false" :class="{ 'hc-active': active === 'rates' }" class="block border-b border-[#1B2B1C]/10 py-3 text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Rates</a>
            <a href="#rules" @click="mobileMenu = false" :class="{ 'hc-active': active === 'rules' }" class="block border-b border-[#1B2B1C]/10 py-3 text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Rules</a>
            <a href="#find-us" @click="mobileMenu = false" :class="{ 'hc-active': active === 'find-us' }" class="block border-b border-[#1B2B1C]/10 py-3 text-sm font-semibold text-[#1B2B1C]/80 hover:text-[#2F5D34]">Find us</a>
            <a href="{{ route('booking.create') }}" class="mt-4 block rounded-full bg-[#2F5D34] px-5 py-3 text-center font-bold text-white hover:bg-[#1F3F24]">Book a court</a>
        </nav>

        {{-- Scroll progress line --}}
        <div style="height:3px;background:rgba(31,63,36,.12);">
            <div :style="{ width: progress + '%' }" style="height:100%;width:0;background:linear-gradient(90deg,#2F5D34,#E4F03A);transition:width .12s linear;"></div>
        </div>
    </header>

    <main class="flex-grow">

        {{-- Hero Section --}}
        <section class="hcx-hero relative mx-3 mt-3 overflow-hidden rounded-[2.5rem] bg-[#CFDDAE] md:mx-6 md:rounded-[4rem]">
            <style>
                .hcx-hero{
                    background-color:#CFDDAE;
                    background-image:
                        radial-gradient(900px 520px at 6% 0%, rgba(246,248,238,.9), transparent 60%),
                        radial-gradient(760px 520px at 100% 100%, rgba(47,93,52,.30), transparent 62%),
                        radial-gradient(520px 320px at 58% 8%, rgba(228,240,58,.30), transparent 65%);
                }
                .hcx-bg{position:absolute;inset:0;overflow:hidden;pointer-events:none}
                .hcx-content{position:relative;z-index:2}

                /* court lines */
                .hcx-court{position:absolute;inset:0;width:100%;height:100%;opacity:.55}

                /* ball trail */
                .hcx-trail{fill:none;stroke:#E4F03A;stroke-width:5;stroke-linecap:round;stroke-dasharray:1 15;animation:hcx-flow 1.6s linear infinite;opacity:.85}
                @keyframes hcx-flow{to{stroke-dashoffset:-32}}

                /* big ball + rings */
                .hcx-ballwrap{position:absolute;right:-70px;bottom:-40px;width:230px;height:230px}
                @media (min-width:768px){.hcx-ballwrap{right:-5%;bottom:auto;top:50%;width:clamp(300px,36vw,500px);height:clamp(300px,36vw,500px);transform:translateY(-50%)}}
                .hcx-ball{position:absolute;inset:0;color:#E4F03A;filter:drop-shadow(0 24px 30px rgba(31,63,36,.3));animation:hcx-bob 7s ease-in-out infinite alternate}
                .hcx-ball svg{display:block;width:100%;height:100%}
                @keyframes hcx-bob{from{transform:translateY(-10px) rotate(-4deg)}to{transform:translateY(14px) rotate(6deg)}}
                .hcx-ring{position:absolute;inset:0;border-radius:9999px;border:2px solid rgba(246,248,238,.7);opacity:0;animation:hcx-ring 5s ease-out infinite}
                .hcx-ring:nth-child(2){animation-delay:1.65s}
                .hcx-ring:nth-child(3){animation-delay:3.3s}
                @keyframes hcx-ring{0%{transform:scale(.85);opacity:.7}100%{transform:scale(1.55);opacity:0}}

                /* small bouncing balls */
                .hcx-b{position:absolute;animation:hcx-arc 6s ease-in-out infinite alternate}
                .hcx-b svg{display:block;width:100%;height:100%}
                @keyframes hcx-arc{
                    0%{transform:translate(0,0) rotate(0deg)}
                    25%{transform:translate(16px,-34px) rotate(70deg)}
                    50%{transform:translate(32px,0) rotate(140deg)}
                    75%{transform:translate(48px,-26px) rotate(210deg)}
                    100%{transform:translate(64px,0) rotate(280deg)}
                }
                @media (max-width:767px){.hcx-md{display:none}}

                /* entrance */
                .hcx-rise{opacity:0;transform:translateY(24px);animation:hcx-rise .8s cubic-bezier(.2,.7,.2,1) forwards}
                @keyframes hcx-rise{to{opacity:1;transform:none}}

                @media (prefers-reduced-motion:reduce){
                    .hcx-trail,.hcx-ball,.hcx-b{animation:none}
                    .hcx-ring{display:none}
                    .hcx-rise{animation:none;opacity:1;transform:none}
                }
            </style>

            {{-- Background layer --}}
            <div class="hcx-bg" aria-hidden="true">

                {{-- Pickleball court lines --}}
                <svg class="hcx-court" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round">
                    <rect x="60" y="60" width="1080" height="480" rx="6"/>
                    <line x1="600" y1="60" x2="600" y2="540" stroke-dasharray="3 10"/>
                    <line x1="428" y1="60" x2="428" y2="540"/>
                    <line x1="772" y1="60" x2="772" y2="540"/>
                    <line x1="60" y1="300" x2="428" y2="300"/>
                    <line x1="772" y1="300" x2="1140" y2="300"/>
                </svg>

                {{-- Ball trail --}}
                <svg class="hcx-court" style="opacity:1" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice">
                    <path class="hcx-trail" d="M -40 540 C 260 80, 760 20, 1000 260"/>
                </svg>

                {{-- Big ball with pulsing rings --}}
                <div class="hcx-ballwrap">
                    <span class="hcx-ring"></span><span class="hcx-ring"></span><span class="hcx-ring"></span>
                    <div class="hcx-ball"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                </div>

                {{-- Small bouncing balls --}}
                <div class="hcx-b" style="left:4%;top:12%;width:40px;height:40px;color:#F6F8EE;opacity:.85;animation-duration:6s"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcx-b hcx-md" style="left:42%;top:9%;width:26px;height:26px;color:#E4F03A;animation-duration:7.5s;animation-delay:-2s"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcx-b" style="left:6%;bottom:9%;width:54px;height:54px;color:#F6F8EE;opacity:.85;animation-duration:8s;animation-delay:-3s"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcx-b hcx-md" style="left:46%;bottom:11%;width:30px;height:30px;color:#E4F03A;animation-duration:6.5s;animation-delay:-1s"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcx-b hcx-md" style="right:22%;bottom:8%;width:38px;height:38px;color:#F6F8EE;opacity:.85;animation-duration:9s;animation-delay:-4s"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcx-b hcx-md" style="right:36%;top:13%;width:22px;height:22px;color:#E4F03A;animation-duration:7s;animation-delay:-5s"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
            </div>

            <div class="hcx-content mx-auto max-w-7xl px-6 pb-20 pt-14 md:px-10 md:pb-28 md:pt-20">
                <div class="max-w-3xl">
                    <h1 class="hc-display text-5xl font-extrabold leading-[1.02] text-[#1B2B1C] sm:text-6xl md:text-7xl">
                        <span class="hcx-rise block" style="animation-delay:.3s">Reserve your court.</span>
                        <span class="hcx-rise block" style="animation-delay:.45s">Serve your best game.</span>
                    </h1>

                    <p class="hcx-rise mt-6 max-w-xl text-lg leading-relaxed text-[#1B2B1C]/80" style="animation-delay:.6s">
                        Indoor pickleball courts in Panabo City. Pick your hours online and pay with GCash or Maya.
                    </p>

                    <div class="hcx-rise" style="animation-delay:.75s">
                        <p class="mt-5 flex items-start gap-2 text-base font-semibold text-[#1B2B1C]">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#2F5D34]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>{{ $address }}</span>
                        </p>

                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lat }},{{ $lng }}" target="_blank" rel="noopener"
                           style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;font-size:14px;font-weight:700;color:#2F5D34;text-decoration:underline;text-underline-offset:4px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                            Get directions
                        </a>
                    </div>

                    <div class="hcx-rise mt-9 flex flex-col gap-3 sm:flex-row" style="animation-delay:.9s">
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

        {{-- Ticker strip --}}
        <div class="hc-ticker" aria-label="Highlights">
            <style>
                .hc-ticker{margin:18px 12px 0;overflow:hidden;border-radius:9999px;background:#1F3F24;padding:12px 0}
                @media (min-width:768px){.hc-ticker{margin:22px 24px 0}}
                .hc-ticker-track{display:flex;width:max-content;animation:hc-scroll var(--hc-dur,20s) linear infinite;will-change:transform}
                .hc-ticker:hover .hc-ticker-track{animation-play-state:paused}
                .hc-ticker-group{display:flex;flex-shrink:0;align-items:center}
                .hc-ticker-item{display:inline-flex;align-items:center;gap:10px;padding:0 28px;font-size:14px;font-weight:700;color:#F6F8EE;white-space:nowrap}
                .hc-ticker-dot{width:8px;height:8px;border-radius:9999px;background:#E4F03A;flex-shrink:0}
                @keyframes hc-scroll{from{transform:translateX(0)}to{transform:translateX(calc(-1 * var(--hc-shift, 25%)))}}
                @media (prefers-reduced-motion:reduce){.hc-ticker-track{animation:none}}
            </style>

            <div class="hc-ticker-track">
                @for ($i = 0; $i < 4; $i++)
                    <div class="hc-ticker-group" @if($i > 0) aria-hidden="true" @endif>
                        <span class="hc-ticker-item"><span class="hc-ticker-dot"></span>Open 4:00 AM – 2:00 AM</span>
                        <span class="hc-ticker-item"><span class="hc-ticker-dot"></span>₱200/hr day · ₱300/hr night</span>
                        <span class="hc-ticker-item"><span class="hc-ticker-dot"></span>Pay with GCash or Maya</span>
                    </div>
                @endfor
            </div>

            <script>
            (function () {
                var ticker = document.querySelector('.hc-ticker');
                if (!ticker) return;
                var track = ticker.querySelector('.hc-ticker-track');
                var first = track.querySelector('.hc-ticker-group');

                function fit() {
                    var gw = first.getBoundingClientRect().width;
                    if (!gw) return;

                    // enough copies that the strip always reaches past the right edge,
                    // even after it has slid one copy to the left
                    var need = Math.max(4, Math.ceil(ticker.clientWidth / gw) + 2);
                    var groups = track.querySelectorAll('.hc-ticker-group');

                    while (groups.length < need) {
                        var c = first.cloneNode(true);
                        c.setAttribute('aria-hidden', 'true');
                        track.appendChild(c);
                        groups = track.querySelectorAll('.hc-ticker-group');
                    }
                    while (groups.length > need) {
                        track.removeChild(groups[groups.length - 1]);
                        groups = track.querySelectorAll('.hc-ticker-group');
                    }

                    track.style.setProperty('--hc-shift', (100 / need) + '%');   // slide exactly one copy
                    track.style.setProperty('--hc-dur', (gw / 70) + 's');        // steady speed (~70px per second)
                }

                fit();
                window.addEventListener('load', fit);
                if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);

                var t;
                window.addEventListener('resize', function () { clearTimeout(t); t = setTimeout(fit, 150); });
            })();
            </script>
        </div>

{{-- Courts Section --}}
        <section id="courts" class="hcc scroll-mt-24" x-data="courtSlider()" x-init="$nextTick(() => { $refs.track.scrollLeft = 0; update(); })" @resize.window.debounce.150ms="update()">
            <style>
                .hcc{position:relative;margin:40px 12px;overflow:hidden;border-radius:40px;padding:56px 20px 44px;
                    background-color:#1F3F24;
                    background-image:
                        radial-gradient(620px 360px at 92% 0%, rgba(228,240,58,.20), transparent 65%),
                        radial-gradient(700px 420px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
                @media (min-width:768px){.hcc{margin:64px 24px;border-radius:64px;padding:80px 48px 60px}}
                .hcc-lines{position:absolute;inset:0;width:100%;height:100%;opacity:.07;pointer-events:none}
                .hcc-inner{position:relative;z-index:2;margin:0 auto;max-width:1200px}

                .hcc-eyebrow{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:rgba(228,240,58,.14);padding:6px 14px;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#E4F03A}
                .hcc-eyebrow i{width:7px;height:7px;border-radius:9999px;background:#E4F03A;animation:hcc-pulse 2s ease-in-out infinite}

                .hcc-arrow{width:48px;height:48px;border-radius:9999px;border:2px solid rgba(246,248,238,.35);background:transparent;color:#F6F8EE;display:flex;align-items:center;justify-content:center;transition:background .2s,color .2s,border-color .2s,opacity .2s}
                .hcc-arrow:not(:disabled):hover{background:#E4F03A;border-color:#E4F03A;color:#1B2B1C}

                .hcc-track{display:flex;gap:18px;overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;-webkit-overflow-scrolling:touch;padding:14px 4px 22px}
                .hcc-track::-webkit-scrollbar{display:none}
                .hcc-slide{flex:0 0 80%;scroll-snap-align:start}
                @media (min-width:640px){.hcc-slide{flex-basis:calc((100% - 18px) / 2)}}
                @media (min-width:1024px){.hcc-slide{flex-basis:calc((100% - 54px) / 4)}}

                .hcc-card{position:relative;display:flex;flex-direction:column;overflow:hidden;height:100%;border-radius:28px;background:#fff;box-shadow:0 22px 40px -26px rgba(0,0,0,.65);transition:transform .35s cubic-bezier(.2,.7,.2,1),box-shadow .35s}
                .hcc-card:hover{transform:translateY(-8px);box-shadow:0 34px 52px -26px rgba(0,0,0,.75)}

                .hcc-art{position:relative;overflow:hidden;padding:44px 18px 24px;
                    background:radial-gradient(260px 160px at 50% 38%, rgba(95,150,80,.55), transparent 70%), #2F5D34}
                .hcc-art svg{position:relative;z-index:2;display:block;width:100%;max-width:178px;height:auto;margin:0 auto;color:rgba(246,248,238,.85)}
                .hcc-ball{transition:transform .6s cubic-bezier(.2,.7,.2,1)}
                .hcc-card:hover .hcc-ball{transform:translate(-34px,26px)}
                .hcc-num{position:absolute;right:12px;bottom:-26px;z-index:1;font-size:130px;font-weight:800;line-height:1;color:rgba(246,248,238,.10);pointer-events:none;font-family:inherit}
                .hcc-tag{position:absolute;left:14px;top:14px;z-index:3;border-radius:9999px;background:#F6F8EE;padding:4px 11px;font-size:11px;font-weight:800;color:#2F5D34;text-transform:capitalize}
                .hcc-status{position:absolute;right:14px;top:14px;z-index:3;display:inline-flex;align-items:center;gap:6px;border-radius:9999px;padding:4px 11px;font-size:11px;font-weight:800}
                .hcc-status.ok{background:rgba(31,63,36,.85);color:#E4F03A}
                .hcc-status.ok i{width:7px;height:7px;border-radius:9999px;background:#E4F03A;animation:hcc-pulse 2s ease-in-out infinite}
                .hcc-status.full{background:#FEE2E2;color:#B91C1C}

                .hcc-body{display:flex;flex:1;flex-direction:column;gap:14px;padding:18px}
                .hcc-name{margin:0;font-size:22px;font-weight:800;color:#1B2B1C;line-height:1.1}
                .hcc-chips{display:flex;flex-wrap:wrap;gap:8px}
                .hcc-chip{display:inline-flex;align-items:center;gap:6px;border-radius:9999px;background:#E5ECD2;padding:6px 11px;font-size:12px;font-weight:700;color:#2F5D34}
                .hcc-chip svg{flex-shrink:0}
                .hcc-btn{display:flex;width:100%;align-items:center;justify-content:center;gap:8px;border-radius:9999px;background:#E4F03A;padding:13px 16px;font-size:14px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s,transform .2s}
                .hcc-btn:hover{background:#2F5D34;color:#fff}
                .hcc-btn svg{transition:transform .25s}
                .hcc-btn:hover svg{transform:translateX(4px)}
                .hcc-btn.off{background:#F1F1F1;color:#9CA3AF;cursor:not-allowed;border:0}
                .hcc-btn.off:hover{background:#F1F1F1;color:#9CA3AF}

                @keyframes hcc-pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.45;transform:scale(.7)}}
                @media (prefers-reduced-motion:reduce){
                    .hcc-eyebrow i,.hcc-status.ok i{animation:none}
                    .hcc-card,.hcc-ball{transition:none}
                }
            </style>

            {{-- faint court lines --}}
            <svg class="hcc-lines" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round" aria-hidden="true">
                <rect x="60" y="60" width="1080" height="480" rx="6"/>
                <line x1="600" y1="60" x2="600" y2="540" stroke-dasharray="3 10"/>
                <line x1="428" y1="60" x2="428" y2="540"/>
                <line x1="772" y1="60" x2="772" y2="540"/>
                <line x1="60" y1="300" x2="428" y2="300"/>
                <line x1="772" y1="300" x2="1140" y2="300"/>
            </svg>

            <div class="hcc-inner">
                <div class="flex items-end justify-between gap-6" style="margin-bottom:18px;">
                    <div>
                        <span class="hcc-eyebrow"><i></i>{{ $courts->count() }} indoor courts</span>
                        <h2 class="hc-display text-4xl font-extrabold sm:text-5xl" style="margin:14px 0 0;color:#F6F8EE;">Choose your court</h2>
                    </div>

                    <div class="flex items-center" style="gap:10px;flex-shrink:0;">
                        <button type="button" class="hcc-arrow" @click="go(-1)" :disabled="atStart" :style="{ opacity: atStart ? '.35' : '1', cursor: atStart ? 'default' : 'pointer' }" aria-label="Previous courts">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button type="button" class="hcc-arrow" @click="go(1)" :disabled="atEnd" :style="{ opacity: atEnd ? '.35' : '1', cursor: atEnd ? 'default' : 'pointer' }" aria-label="Next courts">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </div>

                <div class="hcc-track" x-ref="track" @scroll.passive="update()">
                    @forelse ($courts as $court)
                        @php
                            $hoursText = null;
                            if ($court->operating_hours_start && $court->operating_hours_end) {
                                $hoursText = \Carbon\Carbon::parse($court->operating_hours_start)->format('g A')
                                    . ' – '
                                    . \Carbon\Carbon::parse($court->operating_hours_end)->addHour()->format('g A');
                            }
                        @endphp

                        <div class="hcc-slide">
                            <article class="hcc-card">
                                <div class="hcc-art">
                                    <span class="hcc-num" aria-hidden="true">{{ $loop->iteration }}</span>

                                    <svg viewBox="0 0 160 100" aria-hidden="true">
                                        <use href="#hc-court"/>
                                        <g class="hcc-ball"><use href="#hc-ball" x="113" y="31" width="13" height="13" style="color:#E4F03A"/></g>
                                    </svg>

                                    <span class="hcc-tag">{{ $court->classification ?? 'Indoor' }}</span>

                                    @if($court->is_fully_booked)
                                        <span class="hcc-status full">Full today</span>
                                    @else
                                        <span class="hcc-status ok"><i></i>Available</span>
                                    @endif
                                </div>

                                <div class="hcc-body">
                                    <h3 class="hc-display hcc-name">{{ $court->name }}</h3>

                                    <div class="hcc-chips">
                                        @if($hoursText)
                                            <span class="hcc-chip">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                {{ $hoursText }}
                                            </span>
                                        @endif
                                        <span class="hcc-chip">₱200–₱300 / hr</span>
                                    </div>

                                    <div style="margin-top:auto;">
                                        @if($court->is_fully_booked)
                                            <button type="button" disabled class="hcc-btn off">Fully booked today</button>
                                        @else
                                            <a href="{{ route('booking.create', ['court_id' => $court->id]) }}" class="hcc-btn">
                                                Book this court
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="hcc-slide" style="border-radius:24px;background:#fff;padding:24px;color:#1B2B1C;">
                            No active courts are currently available.
                        </div>
                    @endforelse
                </div>

                {{-- Dots --}}
                <div x-show="pages > 1" class="flex items-center justify-center" style="gap:8px;margin-top:6px;">
                    <template x-for="i in pages" :key="i">
                        <button type="button" @click="goTo(i - 1)" :aria-label="'Go to position ' + i"
                                :style="{ width: index === i - 1 ? '24px' : '8px', background: index === i - 1 ? '#E4F03A' : 'rgba(246,248,238,.3)' }"
                                style="height:8px;border-radius:9999px;border:0;padding:0;cursor:pointer;transition:all .25s;"></button>
                    </template>
                </div>
            </div>

            <script>
                function courtSlider() {
                    return {
                        index: 0,
                        pages: 1,
                        atStart: true,
                        atEnd: false,
                        step() {
                            const first = this.$refs.track.firstElementChild;
                            return first ? first.offsetWidth + 18 : 1;
                        },
                        update() {
                            const el = this.$refs.track;
                            const step = this.step();
                            const perView = Math.max(1, Math.round((el.clientWidth + 18) / step));
                            this.pages = Math.max(1, el.children.length - perView + 1);
                            this.index = Math.min(this.pages - 1, Math.round(el.scrollLeft / step));
                            this.atStart = el.scrollLeft <= 4;
                            this.atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 4;
                        },
                        go(dir) {
                            this.$refs.track.scrollBy({ left: dir * this.step(), behavior: 'smooth' });
                        },
                        goTo(i) {
                            this.$refs.track.scrollTo({ left: i * this.step(), behavior: 'smooth' });
                        }
                    };
                }
            </script>
        </section>

        {{-- Rates Section --}}
        @php
            $dayRate   = 200;
            $nightRate = 300;
            $stars = [[8,12,3,0],[22,28,2,.8],[38,9,3,1.6],[54,22,2,.4],[68,11,3,1.2],[80,30,2,2],[12,48,2,1],[30,62,3,.2],[46,44,2,1.8]];
        @endphp

        <section id="rates" class="hcr scroll-mt-24">
            <style>
                .hcr{position:relative;overflow:hidden;padding:64px 0;background-color:#E5ECD2;
                    background-image:radial-gradient(700px 420px at 100% 0%, rgba(228,240,58,.30), transparent 65%),radial-gradient(640px 420px at 0% 100%, rgba(246,248,238,.95), transparent 65%)}
                @media (min-width:768px){.hcr{padding:96px 0}}
                .hcr-inner{position:relative;z-index:2;margin:0 auto;max-width:1200px;padding:0 24px}

                .hcr-eyebrow{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:#1F3F24;padding:6px 14px;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#E4F03A}
                .hcr-eyebrow i{width:7px;height:7px;border-radius:9999px;background:#E4F03A;animation:hcr-pulse 2s ease-in-out infinite}

                .hcr-grid{display:grid;gap:22px;margin-top:40px}
                @media (min-width:768px){.hcr-grid{grid-template-columns:1fr 1fr;gap:26px}}

                .hcr-card{position:relative;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden;border-radius:32px;padding:30px;transition:transform .35s cubic-bezier(.2,.7,.2,1),box-shadow .35s}
                @media (min-width:768px){.hcr-card{padding:38px}}
                .hcr-card:hover{transform:translateY(-8px)}
                .hcr-card > *{position:relative;z-index:2}

                .hcr-day{background:linear-gradient(160deg,#FFFFFF 0%,#F6F8EE 52%,#EEF6B0 100%);border:2px solid rgba(27,43,28,.08);box-shadow:0 26px 44px -30px rgba(27,43,28,.45)}
                .hcr-day:hover{box-shadow:0 36px 54px -30px rgba(27,43,28,.55)}
                .hcr-sun{position:absolute;right:-46px;top:-46px;z-index:1;width:210px;height:210px;color:#E4F03A;opacity:.9;animation:hcr-spin 45s linear infinite;pointer-events:none}

                .hcr-night{background:radial-gradient(420px 260px at 85% 0%, rgba(95,150,80,.45), transparent 70%),#1F3F24;box-shadow:0 26px 44px -30px rgba(0,0,0,.7)}
                .hcr-night:hover{box-shadow:0 36px 54px -30px rgba(0,0,0,.8)}
                .hcr-moon{position:absolute;right:26px;top:22px;z-index:1;width:104px;height:104px;color:#E4F03A;filter:drop-shadow(0 0 22px rgba(228,240,58,.55));animation:hcr-float 6s ease-in-out infinite alternate;pointer-events:none}
                .hcr-star{position:absolute;z-index:1;border-radius:9999px;background:#F6F8EE;animation:hcr-twinkle 3s ease-in-out infinite;pointer-events:none}

                .hcr-label{display:inline-flex;align-items:center;gap:10px}
                .hcr-ico{display:flex;width:44px;height:44px;align-items:center;justify-content:center;border-radius:9999px}
                .hcr-day .hcr-ico{background:#E5ECD2;color:#2F5D34}
                .hcr-night .hcr-ico{background:rgba(255,255,255,.12);color:#E4F03A}
                .hcr-kind{margin:0;font-size:16px;font-weight:800}
                .hcr-time{margin:2px 0 0;font-size:13px;font-weight:600}
                .hcr-day .hcr-kind{color:#1B2B1C}.hcr-day .hcr-time{color:rgba(27,43,28,.65)}
                .hcr-night .hcr-kind{color:#fff}.hcr-night .hcr-time{color:#CFDDAE}

                /* price + "per hour" on one baseline, "per hour" in the normal body font */
                .hcr-price-row{display:flex;align-items:baseline;flex-wrap:wrap;column-gap:14px;margin-top:34px}
                .hcr-price{margin:0;font-weight:800;line-height:1;font-size:76px}
                @media (min-width:768px){.hcr-price{font-size:88px}}
                .hcr-price small{display:inline-block;margin-right:8px;font-size:.4em;font-weight:800;vertical-align:top;position:relative;top:.12em}
                .hcr-per{font-size:17px;font-weight:700;letter-spacing:0;word-spacing:normal;white-space:nowrap}
                .hcr-day .hcr-price{color:#2F5D34}.hcr-day .hcr-per{color:rgba(27,43,28,.65)}
                .hcr-night .hcr-price{color:#E4F03A}.hcr-night .hcr-per{color:#CFDDAE}

                .hcr-list{margin:22px 0 0;padding:0;list-style:none;display:grid;gap:10px}
                .hcr-list li{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600}
                .hcr-list svg{flex-shrink:0}
                .hcr-day .hcr-list li{color:#1B2B1C}.hcr-day .hcr-list svg{color:#2F5D34}
                .hcr-night .hcr-list li{color:#F6F8EE}.hcr-night .hcr-list svg{color:#E4F03A}

                .hcr-btn{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:28px;border-radius:9999px;padding:15px 22px;font-size:15px;font-weight:800;text-decoration:none;transition:background .2s,color .2s}
                .hcr-btn svg{transition:transform .25s}
                .hcr-btn:hover svg{transform:translateX(4px)}
                .hcr-day .hcr-btn{background:#2F5D34;color:#fff}.hcr-day .hcr-btn:hover{background:#1F3F24}
                .hcr-night .hcr-btn{background:#E4F03A;color:#1B2B1C}.hcr-night .hcr-btn:hover{background:#fff}

                @keyframes hcr-spin{to{transform:rotate(360deg)}}
                @keyframes hcr-float{from{transform:translateY(-6px) rotate(-6deg)}to{transform:translateY(8px) rotate(6deg)}}
                @keyframes hcr-twinkle{0%,100%{opacity:.25;transform:scale(.8)}50%{opacity:1;transform:scale(1.4)}}
                @keyframes hcr-pulse{0%,100%{opacity:1}50%{opacity:.45}}
                @media (prefers-reduced-motion:reduce){
                    .hcr-sun,.hcr-moon,.hcr-star,.hcr-eyebrow i{animation:none}
                    .hcr-card{transition:none}
                }
            </style>

            <div class="hcr-inner">

                {{-- Heading --}}
                <div style="text-align:center;">
                    <span class="hcr-eyebrow"><i></i>Rates</span>
                    <h2 class="hc-display text-4xl font-extrabold text-[#1B2B1C] sm:text-5xl" style="margin:16px 0 0;">Simple, transparent pricing</h2>
                </div>

                {{-- Rate cards --}}
                <div class="hcr-grid">

                    {{-- Day --}}
                    <div class="hcr-card hcr-day">
                        <svg class="hcr-sun" viewBox="0 0 200 200" aria-hidden="true">
                            <circle cx="100" cy="100" r="38" fill="currentColor"/>
                            <g stroke="currentColor" stroke-width="10" stroke-linecap="round">
                                @for ($a = 0; $a < 360; $a += 30)
                                    <line x1="100" y1="22" x2="100" y2="44" transform="rotate({{ $a }} 100 100)"/>
                                @endfor
                            </g>
                        </svg>

                        <div>
                            <div class="hcr-label">
                                <span class="hcr-ico">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                                </span>
                                <div>
                                    <p class="hcr-kind">Day slot</p>
                                    <p class="hcr-time">4:00 AM – 5:00 PM</p>
                                </div>
                            </div>

                            <div class="hcr-price-row">
                                <p class="hc-display hcr-price"><small>₱</small>{{ $dayRate }}</p>
                                <span class="hcr-per">per hour</span>
                            </div>

                            <ul class="hcr-list">
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>Book as many hours as you need</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>Pick your hours online</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>Pay with GCash or Maya</li>
                            </ul>
                        </div>

                        <a href="{{ route('booking.create') }}" class="hcr-btn">
                            Book a day slot
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- Night --}}
                    <div class="hcr-card hcr-night">
                        @foreach ($stars as $s)
                            <span class="hcr-star" style="left:{{ $s[0] }}%;top:{{ $s[1] }}%;width:{{ $s[2] }}px;height:{{ $s[2] }}px;animation-delay:{{ $s[3] }}s"></span>
                        @endforeach
                        <svg class="hcr-moon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>

                        <div>
                            <div class="hcr-label">
                                <span class="hcr-ico">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                                </span>
                                <div>
                                    <p class="hcr-kind">Night slot</p>
                                    <p class="hcr-time">5:00 PM – 2:00 AM</p>
                                </div>
                            </div>

                            <div class="hcr-price-row">
                                <p class="hc-display hcr-price"><small>₱</small>{{ $nightRate }}</p>
                                <span class="hcr-per">per hour</span>
                            </div>

                            <ul class="hcr-list">
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>Book as many hours as you need</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>Pick your hours online</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>Pay with GCash or Maya</li>
                            </ul>
                        </div>

                        <a href="{{ route('booking.create') }}" class="hcr-btn">
                            Book a night slot
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Rules Section --}}
        <section id="rules" class="hcl scroll-mt-24" x-data>
            <style>
                .hcl{position:relative;overflow:hidden;padding:64px 0;
                    background-color:#F6F8EE;
                    background-image:radial-gradient(620px 360px at 100% 0%, rgba(228,240,58,.22), transparent 65%),radial-gradient(560px 360px at 0% 100%, rgba(207,221,174,.45), transparent 65%)}
                @media (min-width:768px){.hcl{padding:96px 0}}
                .hcl-inner{position:relative;z-index:2;margin:0 auto;max-width:1200px;padding:0 24px}

                .hcl-eyebrow{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:#1F3F24;padding:6px 14px;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#E4F03A}
                .hcl-eyebrow i{width:7px;height:7px;border-radius:9999px;background:#E4F03A;animation:hcl-pulse 2s ease-in-out infinite}
                .hcl-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:16px}
                .hcl-sub{margin:0;max-width:340px;line-height:1.6;color:rgba(27,43,28,.72)}

                .hcl-grid{display:grid;gap:18px;margin-top:40px;grid-template-columns:minmax(0,1fr)}
                @media (min-width:640px){.hcl-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
                @media (min-width:1024px){.hcl-grid{grid-template-columns:repeat(4,minmax(0,1fr));gap:22px}}

                .hcl-card{position:relative;overflow:hidden;border-radius:28px;background:#fff;border:1px solid rgba(27,43,28,.1);padding:28px 24px 32px;box-shadow:0 18px 34px -28px rgba(27,43,28,.45);transition:transform .35s cubic-bezier(.2,.7,.2,1),box-shadow .35s,border-color .35s}
                .hcl-card:hover{transform:translateY(-8px);border-color:rgba(47,93,52,.4);box-shadow:0 30px 46px -28px rgba(27,43,28,.55)}
                .hcl-card > *{position:relative;z-index:1}
                .hcl-card > .hcl-num{position:absolute;z-index:0}
                .hcl-card > .hcl-bar{position:absolute;z-index:2}

                .hcl-num{right:14px;top:4px;font-size:88px;font-weight:800;line-height:1;color:rgba(47,93,52,.09);pointer-events:none;transition:color .35s}
                .hcl-card:hover .hcl-num{color:rgba(160,180,20,.35)}

                .hcl-ico{display:flex;width:58px;height:58px;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24;transition:background .35s,color .35s,transform .35s}
                .hcl-ico svg{width:26px;height:26px}
                .hcl-card:hover .hcl-ico{background:#1F3F24;color:#E4F03A;transform:rotate(-8deg) scale(1.07)}

                .hcl-title{margin:22px 0 0;font-size:21px;font-weight:800;color:#1B2B1C}
                .hcl-text{margin:8px 0 0;font-size:14px;line-height:1.65;color:rgba(27,43,28,.75)}

                .hcl-bar{left:0;right:0;bottom:0;height:5px;background:linear-gradient(90deg,#2F5D34,#E4F03A);transform:scaleX(0);transform-origin:left;transition:transform .45s cubic-bezier(.2,.7,.2,1)}
                .hcl-card:hover .hcl-bar{transform:scaleX(1)}

                .hcl-cta{position:relative;overflow:hidden;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;margin-top:26px;border-radius:32px;padding:30px 28px;
                    background-color:#1F3F24;
                    background-image:radial-gradient(520px 260px at 100% 0%, rgba(228,240,58,.22), transparent 65%),radial-gradient(460px 260px at 0% 100%, rgba(95,150,80,.4), transparent 65%)}
                @media (min-width:768px){.hcl-cta{padding:34px 44px}}
                .hcl-cta-lines{position:absolute;inset:0;width:100%;height:100%;opacity:.07;pointer-events:none}
                .hcl-cta > div, .hcl-cta > button{position:relative;z-index:2}
                .hcl-cta-title{margin:0;font-size:24px;font-weight:800;color:#F6F8EE}
                .hcl-cta-text{margin:6px 0 0;max-width:430px;font-size:14px;line-height:1.6;color:#CFDDAE}
                .hcl-cta-btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:9999px;background:#E4F03A;padding:15px 26px;font-size:15px;font-weight:800;color:#1B2B1C;cursor:pointer;transition:background .2s}
                .hcl-cta-btn:hover{background:#fff}
                .hcl-cta-btn svg{transition:transform .25s}
                .hcl-cta-btn:hover svg{transform:translateX(4px)}
                .hcl-ball{position:absolute;right:34%;top:-16px;z-index:1;width:44px;height:44px;color:#E4F03A;animation:hcl-hop 2.4s ease-in-out infinite;pointer-events:none}
                @media (max-width:900px){.hcl-ball{display:none}}

                @keyframes hcl-hop{0%,100%{transform:translateY(0) rotate(0)}50%{transform:translateY(26px) rotate(180deg)}}
                @keyframes hcl-pulse{0%,100%{opacity:1}50%{opacity:.45}}
                @media (prefers-reduced-motion:reduce){
                    .hcl-ball,.hcl-eyebrow i{animation:none}
                    .hcl-card,.hcl-ico,.hcl-bar{transition:none}
                }
            </style>

            <div class="hcl-inner">

                {{-- Heading --}}
                <div class="hcl-head">
                    <div>
                        <span class="hcl-eyebrow"><i></i>House rules</span>
                        <h2 class="hc-display text-4xl font-extrabold text-[#1B2B1C] sm:text-5xl" style="margin:16px 0 0;">Play fair. Play safe.</h2>
                    </div>
                    <p class="hcl-sub">A few simple rules keep every game fun for everyone.</p>
                </div>

                {{-- Rule cards --}}
                <div class="hcl-grid">
                    @foreach ($rules as $rule)
                        <div class="hcl-card">
                            <span class="hcl-num hc-display" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                            <span class="hcl-ico">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $rule['icon'] !!}</svg>
                            </span>

                            <h3 class="hc-display hcl-title">{{ $rule['title'] }}</h3>
                            <p class="hcl-text">{{ $rule['text'] }}</p>

                            <span class="hcl-bar" aria-hidden="true"></span>
                        </div>
                    @endforeach
                </div>

                {{-- Full rules banner --}}
                <div class="hcl-cta">
                    <svg class="hcl-cta-lines" viewBox="0 0 1200 300" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round" aria-hidden="true">
                        <rect x="40" y="30" width="1120" height="240" rx="6"/>
                        <line x1="600" y1="30" x2="600" y2="270" stroke-dasharray="3 10"/>
                        <line x1="40" y1="150" x2="400" y2="150"/>
                        <line x1="800" y1="150" x2="1160" y2="150"/>
                    </svg>
                    <svg class="hcl-ball" viewBox="0 0 200 200" aria-hidden="true"><use href="#hc-ball"/></svg>

                    <div>
                        <p class="hc-display hcl-cta-title">Want the full list?</p>
                        <p class="hcl-cta-text">Everything you need to know before you play, in one place.</p>
                    </div>

                    <button type="button" @click="$dispatch('open-house-rules')" class="hcl-cta-btn">
                        Read the full House Rules
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </section>

        {{-- Find Us Section --}}
        <section id="find-us" class="relative mx-3 mb-6 scroll-mt-24 overflow-hidden rounded-[2.5rem] bg-[#CFDDAE] px-6 py-16 md:mx-6 md:rounded-[4rem] md:py-20">
            {{-- Decoration layer: floating pickleballs in the empty top and bottom bands --}}
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
                        <span>Courts open 4:00 AM to 2:00 AM</span>
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

        {{-- Footer --}}
        <footer class="hc-footer bg-[#1F3F24] text-[#CFDDAE]">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 py-8 text-sm sm:flex-row">
                <p>© {{ date('Y') }} HomeCourt PickleHouse. All rights reserved.</p>

                <div class="flex items-center gap-6">
                    <button type="button" @click="$dispatch('open-house-rules')" class="underline-offset-4 transition-colors hover:text-white hover:underline">House Rules</button>
                    <button type="button" @click="$dispatch('open-privacy')" class="underline-offset-4 transition-colors hover:text-white hover:underline">Privacy Policy</button>

                    {{-- Facebook Social Link --}}
                    <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" aria-label="HomeCourt PickleHouse on Facebook" class="transition-colors hover:text-white">
                        <svg width="20" height="20" class="fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>
            </div>
        </footer>

        {{-- House Rules popup --}}
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

        {{-- Privacy Policy popup --}}
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
    <style>
    .hc-reveal{opacity:0;transform:translateY(28px);transition:opacity .7s ease,transform .7s ease}
    .hc-reveal.hc-in{opacity:1;transform:none}
</style>
<script>
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;

    var sections = Array.prototype.slice.call(document.querySelectorAll('main section')).slice(1); // skip the hero
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('hc-in'); io.unobserve(e.target); }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -6% 0px' });

    sections.forEach(function (s) { s.classList.add('hc-reveal'); io.observe(s); });
})();
</script>
    <script>
    console.log('%cHomeCourt PickleHouse%c  built by jondev',
        'background:#2F5D34;color:#E4F03A;padding:4px 8px;border-radius:4px 0 0 4px;font-weight:bold',
        'background:#E4F03A;color:#1B2B1C;padding:4px 8px;border-radius:0 4px 4px 0;font-weight:bold');
</script>
</body>
</html>