<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Submitted | HomeCourt PickleHouse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; }
        .hc-display { font-family: 'Bricolage Grotesque', 'Inter', system-ui, sans-serif; letter-spacing: -0.025em; }
        [x-cloak] { display: none !important; }

        a:focus-visible, button:focus-visible { outline: 2px solid #2F5D34; outline-offset: 3px; }
        .hc-on-dark a:focus-visible { outline-color: #E4F03A; }

        /* Floating pickleballs in the success panel (plain CSS, transform only). */
        .hc-layer  { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
        .hc-above  { position: relative; z-index: 1; }
        .hc-float  { position: absolute; display: block; }
        .hc-sm-up, .hc-md-up { display: none; }
        @media (min-width: 640px) { .hc-sm-up { display: block; } }
        @media (min-width: 768px) { .hc-md-up { display: block; } }
        @keyframes hc-hop {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-20px) rotate(16deg); }
        }
        @keyframes hc-drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50%      { transform: translate(12px, -16px) rotate(-14deg); }
        }
        .hc-hop   { animation: hc-hop 5s ease-in-out infinite; will-change: transform; }
        .hc-drift { animation: hc-drift 8s ease-in-out infinite; will-change: transform; }

        @media (prefers-reduced-motion: reduce) {
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
<body class="flex min-h-screen flex-col bg-[#F6F8EE] text-[#1B2B1C] antialiased">

    {{-- Shared pickleball graphic, reused with <use>. --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="hc-ball" viewBox="0 0 200 200">
            <path fill="currentColor" fill-rule="evenodd" d="M4.00 100.00a96 96 0 1 0 192 0a96 96 0 1 0 -192 0zM117.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM105.80 120.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM81.80 120.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM69.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM81.80 79.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM105.80 79.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM144.03 113.46a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM130.57 136.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.26 150.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.34 150.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM57.03 136.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM43.57 113.46a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM43.57 86.54a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM57.03 63.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.34 49.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.26 49.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM130.57 63.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM144.03 86.54a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM172.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM168.04 127.02a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM154.32 150.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM133.30 168.42a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.52 177.80a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.08 177.80a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM54.30 168.42a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM33.28 150.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM19.56 127.02a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM14.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM19.56 72.98a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM33.28 49.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM54.30 31.58a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.08 22.20a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.52 22.20a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM133.30 31.58a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM154.32 49.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM168.04 72.98a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0z"/>
        </symbol>
    </svg>

    <!-- Navigation Header (kept pure white: the logo file has a white background) -->
    <header class="sticky top-0 z-50 border-b border-[#1B2B1C]/10 bg-white/95 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3">
            <a href="/" class="flex items-center" aria-label="HomeCourt PickleHouse home">
                <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high" class="h-10 w-auto sm:h-12" style="height: 44px; width: auto; max-height: 44px;">
            </a>
        </div>
    </header>

    <main class="flex-grow">

        <!-- Success panel (echoes the home page hero) -->
        <section class="relative mx-3 mt-3 overflow-hidden rounded-[2.5rem] bg-[#CFDDAE] md:mx-6 md:rounded-[4rem]">
            <div class="hc-layer" aria-hidden="true">
                <svg class="hc-float hc-hop" viewBox="0 0 200 200" width="36" height="36" style="left:6%;top:14px;color:#E4F03A;animation-delay:-1.5s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-drift hc-sm-up" viewBox="0 0 200 200" width="48" height="48" style="right:8%;top:18px;color:#F6F8EE;opacity:.8;animation-delay:-4s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-drift hc-sm-up" viewBox="0 0 200 200" width="48" height="48" style="left:10%;bottom:14px;color:#F6F8EE;opacity:.8;animation-delay:-6s;animation-duration:9s"><use href="#hc-ball"/></svg>
                <svg class="hc-float hc-hop" viewBox="0 0 200 200" width="36" height="36" style="right:7%;bottom:14px;color:#E4F03A;animation-delay:-3s;animation-duration:6s"><use href="#hc-ball"/></svg>
            </div>

            <div class="hc-above mx-auto max-w-2xl px-6 py-12 text-center md:py-16">
                <div class="mx-auto flex items-center justify-center rounded-full bg-[#2F5D34] text-white" style="width:4rem;height:4rem">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h1 class="hc-display mt-6 text-3xl font-extrabold text-[#1B2B1C] sm:text-4xl md:text-5xl">Booking Request Submitted!</h1>
                <p class="mt-3 text-[#1B2B1C]/80">Thank you for choosing PickleHouse.</p>

                <!-- Booking Reference (ticket-stub style) -->
                <div class="mt-8 rounded-[1.5rem] border-2 border-dashed border-[#2F5D34]/50 bg-white/85 p-6">
                    <p class="text-sm font-semibold text-[#1B2B1C]/70">Your booking reference</p>
                    <p class="hc-display mt-2 break-all text-4xl font-extrabold tracking-wider text-[#2F5D34] sm:text-5xl">{{ $booking->booking_reference }}</p>
                    <p class="mt-3 text-sm text-[#1B2B1C]/70">Screenshot this reference and send it to our Facebook page.</p>
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-2xl space-y-6 px-4 py-8 sm:px-6 sm:py-12">

            <!-- Booking Details -->
            <div class="rounded-[2rem] border border-[#1B2B1C]/10 bg-white p-6 sm:p-8">
                <h2 class="hc-display mb-5 text-xl font-bold text-[#1B2B1C]">Booking details</h2>
                <dl class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-[#1B2B1C]/60">Customer</dt>
                        <dd class="mt-1 break-words font-bold">{{ $booking->customer_name ?? $booking->customer->full_name ?? 'Guest Customer' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[#1B2B1C]/60">Contact number</dt>
                        <dd class="mt-1 break-words font-bold">{{ $booking->customer->contact_number ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[#1B2B1C]/60">Court</dt>
                        <dd class="mt-1 font-bold">{{ $booking->court->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[#1B2B1C]/60">Date</dt>
                        <dd class="mt-1 font-bold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[#1B2B1C]/60">Total</dt>
                        <dd class="hc-display mt-1 text-2xl font-extrabold text-[#2F5D34]">
                            ₱{{ number_format((float) ($totalAmount ?? $booking->total_price ?? $booking->total_amount ?? 0), 2) }}
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-[#1B2B1C]/60">Time slot{{ (isset($bookings) && $bookings->count() > 1) ? 's' : '' }}</dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            @forelse (($bookings ?? collect([$booking])) as $slotBooking)
                                <span class="inline-flex items-center rounded-full bg-[#E5ECD2] px-3.5 py-1.5 text-xs font-semibold text-[#1B2B1C]">
                                    {{ \Carbon\Carbon::parse($slotBooking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slotBooking->end_time)->format('g:i A') }}
                                </span>
                            @empty
                                <span class="text-xs text-[#1B2B1C]/60">N/A</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Payment Details -->
            <div class="hc-on-dark rounded-[2rem] bg-[#1F3F24] p-6 text-left sm:p-8">
                <h2 class="hc-display text-xl font-bold text-white">Payment details</h2>

                @if(($booking->payment_method ?? '') === 'Maya')
                    <div class="mt-5">
                        <p class="text-sm text-[#CFDDAE]">Send payment via Maya</p>
                        <p class="hc-display mt-1 text-4xl font-extrabold tracking-wide text-[#E4F03A]">0912 345 6789</p>
                        <p class="mt-3 text-sm text-[#CFDDAE]">Account name</p>
                        <p class="font-semibold text-white">HomeCourt PickleHouse</p>
                    </div>
                @else
                    <div class="mt-5">
                        <p class="text-sm text-[#CFDDAE]">Send payment via GCash</p>
                        <p class="hc-display mt-1 text-4xl font-extrabold tracking-wide text-[#E4F03A]">0912 345 6789</p>
                        <p class="mt-3 text-sm text-[#CFDDAE]">Account name</p>
                        <p class="font-semibold text-white">HomeCourt PickleHouse</p>
                    </div>
                @endif

                <div class="mt-6 border-t border-white/15 pt-6">
                    <p class="text-sm leading-relaxed text-[#CFDDAE]">
                        To verify your booking, kindly message our Facebook page with your booking reference and payment receipt.
                    </p>

                    <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="mt-4 flex w-full items-center justify-center gap-2.5 rounded-2xl bg-[#E4F03A] px-5 py-3.5 text-center text-sm font-bold leading-snug text-[#1B2B1C] transition hover:bg-white">
                        <svg width="18" height="18" style="flex:none" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        <span>Open our Facebook page</span>
                    </a>
                </div>
            </div>

            <!-- Return Home Button -->
            <div class="text-center">
                <a href="/" class="inline-flex w-full items-center justify-center rounded-full bg-[#2F5D34] px-8 py-3.5 font-bold text-white transition hover:bg-[#1F3F24] sm:w-auto">
                    Return to Homepage
                </a>
            </div>
        </div>
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