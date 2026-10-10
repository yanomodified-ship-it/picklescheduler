<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Book a Court | HomeCourt PickleHouse</title>

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
        html { scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }

        a:focus-visible, button:focus-visible { outline: 2px solid #2F5D34; outline-offset: 3px; }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition-duration: 0.01ms !important; }
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

    <!-- Navigation Header (kept pure white: the logo file has a white background) -->
    <header class="sticky top-0 z-50 border-b border-[#1B2B1C]/10 bg-white/95 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3">
            <a href="{{ route('home') }}" class="flex items-center" aria-label="HomeCourt PickleHouse home">
                <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high" class="h-10 w-auto sm:h-12" style="height: 44px; width: auto; max-height: 44px;">
            </a>
            <a href="{{ route('home') }}" class="text-sm font-semibold text-[#1B2B1C]/80 transition hover:text-[#2F5D34]">Home</a>
        </div>
    </header>

    <!-- Flash / Validation Messages -->
    @if ($errors->any())
        <div class="mx-auto w-full max-w-7xl px-6 pt-6">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700" role="alert">
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
        <div class="mx-auto w-full max-w-7xl px-6 pt-6">
            <div class="rounded-2xl bg-[#E5ECD2] p-4 font-semibold text-[#2F5D34]">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="flex-grow">

        <!-- Page heading panel (echoes the home page hero) -->
        <section class="mx-3 mt-3 rounded-[2rem] bg-[#CFDDAE] px-6 py-10 md:mx-6 md:rounded-[3rem] md:px-10">
            <div class="mx-auto max-w-7xl">
                <h1 class="hc-display text-4xl font-extrabold text-[#1B2B1C] sm:text-5xl">Reserve your court</h1>
                <p class="mt-3 max-w-xl text-[#1B2B1C]/80">Complete the form below to reserve your court.</p>
            </div>
        </section>

        <!-- Booking Form Section -->
        <section id="booking" class="px-6 py-10">
            <div class="mx-auto max-w-7xl">

                <form id="bookingForm"
                      method="POST"
                      action="{{ route('booking.store') }}"
                      x-data="bookingForm({{ json_encode($courts) }})"
                      @submit="prepareSubmit">
                    @csrf

                    <!-- Hidden Inputs for Form Submission: one input per selected hour slot -->
                    <template x-for="slot in [...selectedSlots].sort()" :key="slot">
                        <input type="hidden" name="slots[]" :value="slot">
                    </template>

                    <div class="grid gap-8 lg:grid-cols-3">

                        <div class="space-y-6 lg:col-span-2">

                            <!-- STEP 1: SCHEDULE & AVAILABILITY GRID -->
                            <div class="rounded-[2rem] border border-[#1B2B1C]/10 bg-white p-6 sm:p-8">
                                <div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                    <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">Select court &amp; schedule</h2>

                                    <template x-if="courtId && isFullyBooked">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-bold text-red-700">
                                            <span class="h-2 w-2 rounded-full bg-red-500"></span> Fully Booked
                                        </span>
                                    </template>
                                    <template x-if="courtId && !isFullyBooked">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-[#E5ECD2] px-3 py-1 text-xs font-bold text-[#2F5D34]">
                                            <span class="h-2 w-2 rounded-full bg-[#2F5D34]"></span> <span x-text="availableHoursCount"></span> hours available
                                        </span>
                                    </template>
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="booking_date" class="mb-2 block text-sm font-semibold text-[#1B2B1C]">Booking Date</label>
                                        <input id="booking_date" name="booking_date" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('booking_date', now()->format('Y-m-d')) }}" required x-model="bookingDate" class="w-full rounded-xl border border-[#1B2B1C]/20 bg-[#F6F8EE] px-4 py-3 text-[#1B2B1C] outline-none transition focus:border-[#2F5D34] focus:bg-white focus:ring-2 focus:ring-[#2F5D34]/20">
                                    </div>
                                    <div>
                                        <label for="court_id" class="mb-2 block text-sm font-semibold text-[#1B2B1C]">Court</label>
                                        <select id="court_id" name="court_id" required x-model="courtId" @change="selectedSlots = []" class="w-full rounded-xl border border-[#1B2B1C]/20 bg-[#F6F8EE] px-4 py-3 text-[#1B2B1C] outline-none transition focus:border-[#2F5D34] focus:bg-white focus:ring-2 focus:ring-[#2F5D34]/20">
                                            <option value="">Select a court</option>
                                            @foreach ($courts as $court)
                                                @php
                                                    $type = $court->classification ?? 'Outdoor';
                                                    $rate = $court->price_per_hour ?? 500;
                                                @endphp
                                                <option value="{{ $court->id }}" data-rate="{{ $rate }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                                    {{ $court->name }} — {{ ucfirst($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- TIME SLOT GRID -->
                                <div x-show="courtId && bookingDate" class="mt-6 border-t border-[#1B2B1C]/10 pt-6">
                                    <div class="mb-3 flex items-center justify-between">
                                        <p class="text-sm font-semibold text-[#1B2B1C]">
                                            Court availability for <span x-text="formattedDate" class="text-[#2F5D34]"></span>
                                        </p>
                                        <template x-if="selectedSlots.length > 0">
                                            <button type="button" @click="selectedSlots = []" class="text-xs font-semibold text-[#1B2B1C]/70 underline hover:text-[#2F5D34]">
                                                Clear Selection
                                            </button>
                                        </template>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 md:grid-cols-4">
                                        <template x-for="slot in allSlots" :key="slot">
                                            <button
                                                type="button"
                                                :disabled="isSlotBooked(slot)"
                                                :class="[
                                                    isSlotBooked(slot)
                                                        ? (isSlotPending(slot)
                                                            ? 'border-amber-200 bg-amber-50 text-amber-600 cursor-not-allowed'
                                                            : 'border-red-200 bg-red-50 text-red-400 cursor-not-allowed')
                                                        : (selectedSlots.includes(slot)
                                                            ? 'border-[#2F5D34] bg-[#2F5D34] text-white font-bold'
                                                            : 'border-[#1B2B1C]/15 bg-white text-[#1B2B1C] hover:border-[#2F5D34] hover:bg-[#E5ECD2]/60 cursor-pointer')
                                                ]"
                                                @click="selectSlot(slot)"
                                                class="flex flex-col items-center justify-center rounded-xl border p-3 text-center transition">

                                                <span class="text-xs font-bold" x-text="getSlotRangeLabel(slot)"></span>

                                                <span
                                                    class="mt-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                                                    :class="isSlotBooked(slot) ? (isSlotPending(slot) ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') : (selectedSlots.includes(slot) ? 'bg-white/25 text-white' : 'bg-[#E5ECD2] text-[#2F5D34]')">
                                                    <span x-text="isSlotBooked(slot) ? (isSlotPending(slot) ? 'Pending' : 'Booked') : (selectedSlots.includes(slot) ? 'Selected' : 'Available')"></span>
                                                </span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="courtId && bookingDate" class="mt-4 text-sm text-[#1B2B1C]/70">
                                    <span>Click any available slots to select them individually — they don't need to be next to each other.</span>
                                </div>
                            </div>

                            <!-- STEP 2: CUSTOMER DETAILS -->
                            <div class="rounded-[2rem] border border-[#1B2B1C]/10 bg-white p-6 sm:p-8">
                                <h2 class="hc-display mb-6 text-xl font-bold text-[#1B2B1C]">Your details</h2>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="name" class="mb-2 block text-sm font-semibold text-[#1B2B1C]">Facebook Name</label>
                                        <input id="name" name="name" type="text" maxlength="100" autocomplete="name" value="{{ old('name') }}" required x-model="customerName" placeholder="Juan Dela Cruz" class="w-full rounded-xl border border-[#1B2B1C]/20 bg-[#F6F8EE] px-4 py-3 text-[#1B2B1C] outline-none transition placeholder:text-[#1B2B1C]/40 focus:border-[#2F5D34] focus:bg-white focus:ring-2 focus:ring-[#2F5D34]/20">
                                    </div>
                                    <div>
                                        <label for="contact_number" class="mb-2 block text-sm font-semibold text-[#1B2B1C]">Contact Number</label>
                                        <input id="contact_number"
                                            name="contact_number"
                                            type="tel"
                                            inputmode="numeric"
                                            pattern="[0-9+\-\s]*"
                                            maxlength="30"
                                            autocomplete="tel"
                                            value="{{ old('contact_number') }}"
                                            required
                                            x-model="contactNumber"
                                            @input="contactNumber = contactNumber.replace(/[^0-9+\-\s]/g, '')"
                                            placeholder="09XXXXXXXXX"
                                            class="w-full rounded-xl border border-[#1B2B1C]/20 bg-[#F6F8EE] px-4 py-3 text-[#1B2B1C] outline-none transition placeholder:text-[#1B2B1C]/40 focus:border-[#2F5D34] focus:bg-white focus:ring-2 focus:ring-[#2F5D34]/20">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="number_of_players" class="mb-2 block text-sm font-semibold text-[#1B2B1C]">Number of Players</label>
                                        <input id="number_of_players" name="number_of_players" type="number" min="1" max="30" value="{{ old('number_of_players', 2) }}" required x-model.number="players" class="w-full rounded-xl border border-[#1B2B1C]/20 bg-[#F6F8EE] px-4 py-3 text-[#1B2B1C] outline-none transition focus:border-[#2F5D34] focus:bg-white focus:ring-2 focus:ring-[#2F5D34]/20">
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: PAYMENT METHOD -->
                            <div class="rounded-[2rem] border border-[#1B2B1C]/10 bg-white p-6 sm:p-8">
                                <h2 class="hc-display mb-6 text-xl font-bold text-[#1B2B1C]">Payment Method</h2>
                                <div class="space-y-5">
                                    <!-- Mode of Payment Dropdown -->
                                    <div>
                                        <label for="payment_method" class="mb-2 block text-sm font-semibold text-[#1B2B1C]">Mode of Payment</label>
                                        <select id="payment_method" name="payment_method" x-model="paymentMethod" class="w-full rounded-xl border border-[#1B2B1C]/20 bg-[#F6F8EE] px-4 py-3 text-[#1B2B1C] outline-none transition focus:border-[#2F5D34] focus:bg-white focus:ring-2 focus:ring-[#2F5D34]/20">
                                            <option value="GCash">GCash</option>
                                            <option value="Maya">Maya</option>
                                        </select>
                                    </div>

                                    <!-- Account details are shown on the confirmation page after
                                         submitting, to avoid presenting payment info twice. -->
                                    <div class="rounded-xl bg-[#E5ECD2] p-4 text-sm text-[#1B2B1C]/80">
                                        Payment details will appear on your screen as soon as you submit your booking.
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- SIDEBAR SUMMARY -->
                        <aside class="lg:col-span-1">
                            <div class="space-y-5 rounded-[2rem] border-2 border-[#1B2B1C]/10 bg-white p-6 lg:sticky lg:top-24">
                                <h2 class="hc-display text-2xl font-bold text-[#1B2B1C]">Review booking</h2>

                                <div class="divide-y divide-[#1B2B1C]/10 text-sm">
                                    <div class="flex justify-between gap-4 py-3">
                                        <span class="text-[#1B2B1C]/65">Customer</span>
                                        <span class="text-right font-bold text-[#1B2B1C]" x-text="customerName || 'Not provided'"></span>
                                    </div>
                                    <div class="flex justify-between gap-4 py-3">
                                        <span class="text-[#1B2B1C]/65">Contact Number</span>
                                        <span class="text-right font-bold text-[#1B2B1C]" x-text="contactNumber || 'Not provided'"></span>
                                    </div>
                                    <div class="flex justify-between gap-4 py-3">
                                        <span class="text-[#1B2B1C]/65">Date</span>
                                        <span class="text-right font-bold text-[#1B2B1C]" x-text="formattedDate"></span>
                                    </div>
                                    <div class="flex justify-between gap-4 py-3">
                                        <span class="text-[#1B2B1C]/65">Court</span>
                                        <span class="text-right font-bold text-[#1B2B1C]" x-text="courtName"></span>
                                    </div>
                                    <div class="flex justify-between gap-4 py-3">
                                        <span class="text-[#1B2B1C]/65">Time</span>
                                        <span class="text-right font-bold text-[#1B2B1C]" x-text="formattedTime"></span>
                                    </div>
                                    <div class="flex justify-between gap-4 py-3">
                                        <span class="text-[#1B2B1C]/65">Duration</span>
                                        <span class="text-right font-bold text-[#1B2B1C]" x-text="durationLabel"></span>
                                    </div>
                                    <div class="flex justify-between gap-4 py-3">
                                        <span class="text-[#1B2B1C]/65">Players</span>
                                        <span class="text-right font-bold text-[#1B2B1C]" x-text="players || '—'"></span>
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-[#1F3F24] p-4">
                                    <div class="text-sm font-semibold text-[#CFDDAE]">Total Price</div>
                                    <div class="hc-display mt-1 text-4xl font-extrabold text-[#E4F03A]" x-text="formattedTotal"></div>
                                </div>

                                <!-- Agreement: required before the booking can be submitted -->
                                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-red-200 bg-red-50/70 p-4 text-sm leading-relaxed text-[#1B2B1C]/80" style="background-color:#FEF2F2;border-color:#FECACA">
                                    <input type="checkbox" name="agree_terms" value="1" x-model="agreed" class="mt-1 rounded" style="width:1rem;height:1rem;flex:none;accent-color:#2F5D34">
                                    <span>I understand that this booking is <strong class="font-bold text-red-700">non-refundable and non-cancellable</strong> once submitted. I have reviewed my court, date and time slots and am ready to proceed.</span>
                                </label>

                                <div>
                                    <button type="submit" :disabled="submitting || isFullyBooked || selectedSlots.length === 0 || !agreed" class="w-full rounded-full bg-[#2F5D34] py-4 text-base font-bold text-white transition hover:bg-[#1F3F24] disabled:cursor-not-allowed disabled:opacity-50">
                                        <span x-show="!submitting">Submit Booking</span>
                                        <span x-show="submitting" x-cloak>Submitting...</span>
                                    </button>
                                </div>

                                <p class="text-center text-xs leading-relaxed text-[#1B2B1C]/65">
                                    Please read our
                                    <button type="button" @click="$dispatch('open-house-rules')" class="font-semibold text-[#2F5D34] underline underline-offset-2 hover:text-[#1F3F24]">House Rules</button>
                                    and
                                    <button type="button" @click="$dispatch('open-privacy')" class="font-semibold text-[#2F5D34] underline underline-offset-2 hover:text-[#1F3F24]">Privacy Policy</button>.
                                    We use your name and contact number only to manage your booking.
                                </p>
                            </div>
                        </aside>
                    </div>
                </form>
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

    <script>
        function bookingForm(courtsData) {
            return {
                courts: courtsData,
                bookingDate: @js(old('booking_date', now()->format('Y-m-d'))),
                courtId: new URLSearchParams(window.location.search).get('court_id') || @js(old('court_id', '')),
                selectedSlots: [],
                customerName: @js(old('name', '')),
                contactNumber: @js(old('contact_number', '')),
                players: @js((int) old('number_of_players', 2)),
                paymentMethod: 'GCash', 
                agreed: @js((bool) old('agree_terms')),
                submitting: false,
                existingBookings: @js($existingBookings ?? []),

                // Replace the static array with this dynamic getter
                get allSlots() {
    if (!this.courtId) return [];
    const court = this.courts.find(c => String(c.id) === String(this.courtId));
    if (!court) return [];

    let startHour = parseInt((court.operating_hours_start || '04:00').substring(0, 2), 10);
    let endHour = parseInt((court.operating_hours_end || '01:00').substring(0, 2), 10);

    // Closing earlier than opening means it runs past midnight (04:00 -> 01:00 becomes 4 -> 25)
    if (endHour < startHour) {
        endHour += 24;
    }

    let slots = [];
        for (let i = startHour; i <= endHour; i++) {
        let hourString = String(i % 24).padStart(2, '0');
        slots.push(hourString + ':00');
    }
    return slots;
},

                getSlotEndTime(slotTime) {
                    if (!slotTime) return '';
                    const [hour, minute] = slotTime.split(':').map(Number);
                    return `${String(hour + 1).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                },

                getSlotRangeLabel(slotTime) {
                    const start = this.formatTime(slotTime);
                    const end = this.formatTime(this.getSlotEndTime(slotTime));
                    return `${start} - ${end}`;
                },

                selectSlot(slot) {
                    if (this.isSlotBooked(slot)) return;

                    // Toggle ONLY the clicked slot - no auto-filling of the hours in between.
                    if (this.selectedSlots.includes(slot)) {
                        this.selectedSlots = this.selectedSlots.filter(s => s !== slot);
                    } else {
                        this.selectedSlots.push(slot);
                    }
                },

                get activeBookingsForSelected() {
                    if (!this.courtId || !this.bookingDate) return [];
                    return this.existingBookings.filter(b => 
                        String(b.court_id) === String(this.courtId) && 
                        b.booking_date === this.bookingDate
                    );
                },

                // A slot is unavailable to new customers if it's already CONFIRMED
                // or still PENDING verification (prevents two customers submitting
                // the same slot while the first is awaiting approval).
                isSlotBooked(slotTime) {
                    return this.activeBookingsForSelected.some(b => {
                        const start = b.start_time.substring(0, 5);
                        const end = b.end_time.substring(0, 5);
                        const status = (b.booking_status || '').toLowerCase();
                        // Actual status string is "Pending Verification", so match with
                        // includes() rather than an exact equality check.
                        const isTaken = status === 'confirmed' || status.includes('pending');
                        return isTaken && slotTime >= start && slotTime < end;
                    });
                },

                // Distinguishes a "Pending" slot from a fully "Confirmed" one so the
                // UI can label it correctly instead of just showing "Booked".
                isSlotPending(slotTime) {
                    return this.activeBookingsForSelected.some(b => {
                        const start = b.start_time.substring(0, 5);
                        const end = b.end_time.substring(0, 5);
                        const status = (b.booking_status || '').toLowerCase();
                        return status.includes('pending') && slotTime >= start && slotTime < end;
                    });
                },

                get isFullyBooked() {
                    if (!this.courtId || !this.bookingDate) return false;
                    return this.allSlots.every(slot => this.isSlotBooked(slot));
                },

                get availableHoursCount() {
                    if (!this.courtId || !this.bookingDate) return this.allSlots.length;
                    return this.allSlots.filter(slot => !this.isSlotBooked(slot)).length;
                },

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

                get durationLabel() {
                    const hours = this.selectedSlots.length;
                    if (!hours) return 'Not selected';
                    return `${hours} ${hours === 1 ? 'hour' : 'hours'}`;
                },

                get formattedDate() {
                    if (!this.bookingDate) return 'Not selected';
                    const date = new Date(`${this.bookingDate}T00:00:00`);
                    return Number.isNaN(date.getTime()) ? 'Not selected' : new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(date);
                },

                formatTime(time) {
                    if (!time) return '';
                    const [hour, minute] = time.split(':').map(Number);
                    const suffix = (hour >= 12 && hour < 24) ? 'PM' : 'AM';
                    const displayHour = hour % 12 || 12;
                    return `${displayHour}:${String(minute).padStart(2, '0')} ${suffix}`;
                },

                get formattedTime() {
                    if (this.selectedSlots.length === 0) return 'Not selected';
                                        const dayOrder = s => {
                        const h = parseInt(s.split(':')[0], 10);
                        return h < 4 ? h + 24 : h;
                    };
                    const sorted = [...this.selectedSlots].sort((a, b) => dayOrder(a) - dayOrder(b));
                    return sorted.map(slot => this.getSlotRangeLabel(slot)).join(', ');
                },

                get totalPrice() {
                    return this.selectedSlots.reduce((total, slot) => {
                        const hour = parseInt(slot.split(':')[0], 10);
                        const rate = (hour >= 4 && hour < 17) ? 200 : 300;
                        return total + rate;
                    }, 0);
                },

                get formattedTotal() {
                    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 2 }).format(this.totalPrice);
                },

                prepareSubmit(event) {
                    if (!this.bookingDate || !this.courtId || this.selectedSlots.length === 0) {
                        event.preventDefault();
                        alert('Please select at least one time slot.');
                        return;
                    }
                    if (!this.agreed) {
                        event.preventDefault();
                        alert('Please tick the box to confirm you understand the booking terms.');
                        return;
                    }
                    this.submitting = true;
                }
            };
        }
    </script>
</body>
</html>