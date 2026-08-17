{{-- =========================================================================
resources/views/welcome.blade.php
Pickleball Court Booking & Management System
Laravel 11 + Blade + Tailwind CSS
============================================================================ --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Pickleball Courts | Court Booking
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        html {
            scroll-behavior: smooth;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">

{{-- =========================================================================
NAVIGATION
============================================================================ --}}

<header
    class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur"
    x-data="{ mobileMenu: false }"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between">

            <a
                href="{{ route('home') }}"
                class="flex min-h-11 items-center gap-3"
            >
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-600 text-xl text-white shadow-sm"
                >
                    🏓
                </span>

                <div>
                    <div class="font-black tracking-tight text-slate-900">
                        Pickleball Courts
                    </div>

                    <div class="hidden text-xs text-slate-500 sm:block">
                        Book your court with ease
                    </div>
                </div>
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden items-center gap-1 md:flex">
                <a
                    href="{{ route('home') }}"
                    class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-green-700"
                >
                    Home
                </a>

                <a
                    href="#courts"
                    class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-green-700"
                >
                    Courts
                </a>

                <a
                    href="#rates"
                    class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-green-700"
                >
                    Rates
                </a>

                <a
                    href="#rules"
                    class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-green-700"
                >
                    Rules
                </a>

                <a
                    href="#booking"
                    class="ml-2 inline-flex min-h-11 items-center rounded-xl bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    Book Now
                </a>
            </nav>

            {{-- Mobile hamburger --}}
            <button
                type="button"
                @click="mobileMenu = !mobileMenu"
                :aria-expanded="mobileMenu.toString()"
                aria-controls="mobile-navigation"
                class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 md:hidden"
            >
                <span class="sr-only">
                    Open navigation menu
                </span>

                <svg
                    x-show="!mobileMenu"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>

                <svg
                    x-show="mobileMenu"
                    x-cloak
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        {{-- Mobile navigation --}}
        <nav
            id="mobile-navigation"
            x-show="mobileMenu"
            x-cloak
            x-transition
            class="border-t border-slate-100 py-3 md:hidden"
        >
            <a
                href="{{ route('home') }}"
                @click="mobileMenu = false"
                class="block min-h-11 rounded-lg px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
            >
                Home
            </a>

            <a
                href="#courts"
                @click="mobileMenu = false"
                class="block min-h-11 rounded-lg px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
            >
                Courts
            </a>

            <a
                href="#rates"
                @click="mobileMenu = false"
                class="block min-h-11 rounded-lg px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
            >
                Rates
            </a>

            <a
                href="#rules"
                @click="mobileMenu = false"
                class="block min-h-11 rounded-lg px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
            >
                Rules
            </a>

            <a
                href="#booking"
                @click="mobileMenu = false"
                class="mt-2 block min-h-11 rounded-xl bg-green-600 px-4 py-3 text-center text-sm font-bold text-white"
            >
                Book Now
            </a>
        </nav>
    </div>
</header>


{{-- =========================================================================
FLASH / VALIDATION MESSAGES
============================================================================ --}}

@if ($errors->any())
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <div
            class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800"
        >
            <div class="font-bold">
                Please correct the following:
            </div>

            <ul class="mt-2 list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if (session('success'))
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <div
            class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800"
        >
            {{ session('success') }}
        </div>
    </div>
@endif


{{-- =========================================================================
CONFIRMATION VIEW
============================================================================ --}}

@if (isset($confirmationBooking))

    <main class="min-h-[70vh] px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">

            <div
                class="overflow-hidden rounded-3xl border border-green-200 bg-white shadow-xl"
            >
                <div class="bg-green-600 px-6 py-10 text-center text-white sm:px-10">

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-3xl text-green-600"
                    >
                        ✓
                    </div>

                    <h1 class="mt-5 text-3xl font-black sm:text-4xl">
                        Booking Submitted!
                    </h1>

                    <p class="mx-auto mt-3 max-w-xl text-green-50">
                        Your booking request has been received and is awaiting
                        payment verification.
                    </p>
                </div>

                <div class="p-6 sm:p-10">

                    <div class="rounded-2xl bg-slate-50 p-5 text-center">
                        <div class="text-xs font-bold uppercase tracking-widest text-slate-500">
                            Booking Reference
                        </div>

                        <div class="mt-2 text-3xl font-black tracking-wider text-slate-900">
                            {{ $confirmationBooking->booking_reference }}
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">

                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="text-xs font-semibold uppercase text-slate-500">
                                Customer
                            </div>

                            <div class="mt-1 font-bold">
                                {{ $confirmationBooking->customer?->name ?? '—' }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="text-xs font-semibold uppercase text-slate-500">
                                Court
                            </div>

                            <div class="mt-1 font-bold">
                                {{ $confirmationBooking->court?->name ?? '—' }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="text-xs font-semibold uppercase text-slate-500">
                                Date
                            </div>

                            <div class="mt-1 font-bold">
                                {{ \Carbon\Carbon::parse($confirmationBooking->booking_date)->format('F d, Y') }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="text-xs font-semibold uppercase text-slate-500">
                                Time
                            </div>

                            <div class="mt-1 font-bold">
                                {{ \Carbon\Carbon::parse($confirmationBooking->start_time)->format('g:i A') }}
                                -
                                {{ \Carbon\Carbon::parse($confirmationBooking->end_time)->format('g:i A') }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="text-xs font-semibold uppercase text-slate-500">
                                Players
                            </div>

                            <div class="mt-1 font-bold">
                                {{ $confirmationBooking->number_of_players ?? '—' }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="text-xs font-semibold uppercase text-slate-500">
                                Total
                            </div>

                            <div class="mt-1 font-bold">
                                ₱{{ number_format((float) $confirmationBooking->total_price, 2) }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">

                        <div class="rounded-2xl bg-amber-50 p-5">
                            <div class="font-bold text-amber-900">
                                Booking Status
                            </div>

                            <div class="mt-2 inline-flex rounded-full bg-amber-200 px-3 py-1 text-sm font-bold text-amber-900">
                                {{ $confirmationBooking->booking_status }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-blue-50 p-5">
                            <div class="font-bold text-blue-900">
                                Payment Status
                            </div>

                            <div class="mt-2 inline-flex rounded-full bg-blue-200 px-3 py-1 text-sm font-bold text-blue-900">
                                {{ $confirmationBooking->payment_status }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
                        <h2 class="font-black text-slate-900">
                            What happens next?
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Our team will verify your payment receipt. Once
                            payment has been confirmed, your booking status
                            will be updated to Confirmed.
                        </p>
                    </div>

                    <div class="mt-8 text-center">
                        <a
                            href="{{ route('home') }}"
                            class="inline-flex min-h-12 items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-bold text-white transition hover:bg-green-700"
                        >
                            Back to Home
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </main>

@else

{{-- =========================================================================
HERO
============================================================================ --}}

<section class="relative overflow-hidden bg-slate-950">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-green-500 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-emerald-400 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-28">

        <div class="grid items-center gap-12 lg:grid-cols-2">

            <div>
                <div class="mb-5 inline-flex rounded-full border border-green-400/30 bg-green-400/10 px-4 py-2 text-sm font-bold text-green-300">
                    7 Courts • Easy Online Booking
                </div>

                <h1 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Your next pickleball game starts here.
                </h1>

                <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 sm:text-lg">
                    Choose your court, select your schedule, submit your
                    payment receipt, and get your booking reference in just
                    a few simple steps.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                    <a
                        href="#booking"
                        class="inline-flex min-h-12 items-center justify-center rounded-xl bg-green-500 px-6 py-3 font-black text-white shadow-lg transition hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 focus:ring-offset-slate-950"
                    >
                        Book a Court
                    </a>

                    <a
                        href="#courts"
                        class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-600 px-6 py-3 font-bold text-white transition hover:bg-white/10"
                    >
                        View Courts
                    </a>

                </div>
            </div>

            <div class="hidden lg:block">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">

                    <div class="grid grid-cols-2 gap-4">

                        <div class="rounded-2xl bg-white p-6">
                            <div class="text-3xl font-black text-green-600">
                                7
                            </div>
                            <div class="mt-1 text-sm font-semibold text-slate-500">
                                Active Courts
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-6">
                            <div class="text-3xl font-black text-green-600">
                                24/7
                            </div>
                            <div class="mt-1 text-sm font-semibold text-slate-500">
                                Online Requests
                            </div>
                        </div>

                        <div class="col-span-2 rounded-2xl bg-green-600 p-6 text-white">
                            <div class="text-sm font-semibold text-green-100">
                                Simple process
                            </div>

                            <div class="mt-2 text-2xl font-black">
                                Select → Pay → Upload → Confirm
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- =========================================================================
COURTS
============================================================================ --}}

<section
    id="courts"
    class="scroll-mt-20 px-4 py-16 sm:px-6 lg:px-8"
>
    <div class="mx-auto max-w-7xl">

        <div class="max-w-2xl">
            <div class="text-sm font-black uppercase tracking-widest text-green-600">
                Our Courts
            </div>

            <h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                Choose your court
            </h2>

            <p class="mt-4 text-slate-600">
                We currently have {{ $courts->count() }} active courts
                available for booking.
            </p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

            @forelse ($courts as $court)

                <article
                    class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex h-36 items-center justify-center bg-gradient-to-br from-green-700 to-emerald-500">
                        <span class="text-6xl transition group-hover:scale-110">
                            🏓
                        </span>
                    </div>

                    <div class="p-5">

                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-lg font-black">
                                {{ $court->name }}
                            </h3>

                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700">
                                Active
                            </span>
                        </div>

                        @if (!empty($court->description))
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                {{ $court->description }}
                            </p>
                        @else
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Professional pickleball court available
                                for scheduled games.
                            </p>
                        @endif

                        <a
                            href="#booking"
                            class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-bold text-green-700 transition hover:bg-green-600 hover:text-white"
                        >
                            Book this court
                        </a>
                    </div>
                </article>

            @empty

                <div class="col-span-full rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-800">
                    No active courts are currently available.
                </div>

            @endforelse

        </div>
    </div>
</section>


{{-- =========================================================================
RATES
============================================================================ --}}

<section
    id="rates"
    class="scroll-mt-20 bg-white px-4 py-16 sm:px-6 lg:px-8"
>
    <div class="mx-auto max-w-7xl">

        <div class="mx-auto max-w-2xl text-center">
            <div class="text-sm font-black uppercase tracking-widest text-green-600">
                Rates
            </div>

            <h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                Simple and transparent pricing
            </h2>

            <p class="mt-4 text-slate-600">
                The standard booking rate is calculated according to the
                selected court and duration.
            </p>
        </div>

        <div class="mx-auto mt-10 max-w-lg">
            <div class="rounded-3xl border border-green-200 bg-green-50 p-8 text-center shadow-sm">

                <div class="text-sm font-bold uppercase tracking-widest text-green-700">
                    Standard Court Rate
                </div>

                <div class="mt-3 text-5xl font-black text-slate-900">
                    ₱{{ number_format((float) env('PICKLEBALL_HOURLY_RATE', 500), 2) }}
                </div>

                <div class="mt-2 text-slate-600">
                    per hour
                </div>

                <a
                    href="#booking"
                    class="mt-7 inline-flex min-h-12 items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-bold text-white hover:bg-green-700"
                >
                    Book Now
                </a>

            </div>
        </div>

    </div>
</section>


{{-- =========================================================================
RULES
============================================================================ --}}

<section
    id="rules"
    class="scroll-mt-20 px-4 py-16 sm:px-6 lg:px-8"
>
    <div class="mx-auto max-w-7xl">

        <div class="max-w-2xl">
            <div class="text-sm font-black uppercase tracking-widest text-green-600">
                Court Rules
            </div>

            <h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                Play fair. Play safe.
            </h2>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

            @foreach ([
                ['icon' => '⏱️', 'title' => 'Arrive Early', 'text' => 'Please arrive before your scheduled booking time.'],
                ['icon' => '👟', 'title' => 'Proper Shoes', 'text' => 'Use appropriate non-marking court footwear.'],
                ['icon' => '🤝', 'title' => 'Respect Others', 'text' => 'Keep the court area clean and respect other players.'],
                ['icon' => '🏓', 'title' => 'Play Safely', 'text' => 'Follow facility instructions and play responsibly.'],
            ] as $rule)

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="text-3xl">
                        {{ $rule['icon'] }}
                    </div>

                    <h3 class="mt-4 font-black">
                        {{ $rule['title'] }}
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        {{ $rule['text'] }}
                    </p>
                </div>

            @endforeach

        </div>
    </div>
</section>


{{-- =========================================================================
BOOKING SECTION
============================================================================ --}}

<section
    id="booking"
    class="scroll-mt-20 bg-slate-950 px-4 py-16 sm:px-6 lg:px-8"
>
    <div class="mx-auto max-w-7xl">

        <div class="max-w-2xl">
            <div class="text-sm font-black uppercase tracking-widest text-green-400">
                Book Now
            </div>

            <h2 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">
                Reserve your court
            </h2>

            <p class="mt-4 text-slate-400">
                Complete the form below. Your booking remains pending until
                your payment has been verified.
            </p>
        </div>

        <form
            id="bookingForm"
            method="POST"
            action="{{ route('booking.store') }}"
            enctype="multipart/form-data"
            class="mt-10"
            x-data="bookingForm()"
            @submit="prepareSubmit"
        >
            @csrf

            <div class="grid gap-8 lg:grid-cols-3">

                {{-- =========================================================
                BOOKING DETAILS
                ========================================================== --}}

                <div class="space-y-6 lg:col-span-2">

                    {{-- Schedule --}}
                    <div class="rounded-3xl bg-white p-5 shadow-xl sm:p-7">

                        <div class="mb-6">
                            <div class="text-xs font-black uppercase tracking-widest text-green-600">
                                Step 1
                            </div>

                            <h3 class="mt-1 text-xl font-black">
                                Select your schedule
                            </h3>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">

                            <div>
                                <label
                                    for="booking_date"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Booking Date
                                </label>

                                <input
                                    id="booking_date"
                                    name="booking_date"
                                    type="date"
                                    min="{{ now()->format('Y-m-d') }}"
                                    value="{{ old('booking_date') }}"
                                    required
                                    x-model="bookingDate"
                                    class="min-h-12 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                >
                            </div>

                            <div>
                                <label
                                    for="court_id"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Court
                                </label>

                                <select
                                    id="court_id"
                                    name="court_id"
                                    required
                                    x-model="courtId"
                                    class="min-h-12 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                >
                                    <option value="">
                                        Select a court
                                    </option>

                                    @foreach ($courts as $court)
                                        <option
                                            value="{{ $court->id }}"
                                            data-rate="{{ $court->price_per_hour ?? $court->hourly_rate ?? $court->rate ?? env('PICKLEBALL_HOURLY_RATE', 500) }}"
                                            {{ old('court_id') == $court->id ? 'selected' : '' }}
                                        >
                                            {{ $court->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    for="start_time"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Start Time
                                </label>

                                <select
                                    id="start_time"
                                    name="start_time"
                                    required
                                    x-model="startTime"
                                    class="min-h-12 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                >
                                    <option value="">
                                        Select start time
                                    </option>

                                    @for ($hour = 6; $hour <= 22; $hour++)
                                        @foreach ([0, 30] as $minute)
                                            @php
                                                $time = sprintf('%02d:%02d', $hour, $minute);
                                            @endphp

                                            @if ($time < '23:00')
                                                <option
                                                    value="{{ $time }}"
                                                    {{ old('start_time') === $time ? 'selected' : '' }}
                                                >
                                                    {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label
                                    for="end_time"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    End Time
                                </label>

                                <select
                                    id="end_time"
                                    name="end_time"
                                    required
                                    x-model="endTime"
                                    class="min-h-12 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                >
                                    <option value="">
                                        Select end time
                                    </option>

                                    @for ($hour = 6; $hour <= 23; $hour++)
                                        @foreach ([0, 30] as $minute)
                                            @php
                                                $time = sprintf('%02d:%02d', $hour, $minute);
                                            @endphp

                                            @if ($time <= '23:00')
                                                <option
                                                    value="{{ $time }}"
                                                    {{ old('end_time') === $time ? 'selected' : '' }}
                                                >
                                                    {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endfor
                                </select>
                            </div>

                        </div>
                    </div>


                    {{-- Customer --}}
                    <div class="rounded-3xl bg-white p-5 shadow-xl sm:p-7">

                        <div class="mb-6">
                            <div class="text-xs font-black uppercase tracking-widest text-green-600">
                                Step 2
                            </div>

                            <h3 class="mt-1 text-xl font-black">
                                Your details
                            </h3>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">

                            <div class="sm:col-span-2">
                                <label
                                    for="name"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Full Name
                                </label>

                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    maxlength="100"
                                    autocomplete="name"
                                    value="{{ old('name') }}"
                                    required
                                    x-model="customerName"
                                    class="min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                    placeholder="Juan Dela Cruz"
                                >
                            </div>

                            <div>
                                <label
                                    for="contact_number"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Contact Number
                                </label>

                                <input
                                    id="contact_number"
                                    name="contact_number"
                                    type="tel"
                                    maxlength="30"
                                    autocomplete="tel"
                                    value="{{ old('contact_number') }}"
                                    required
                                    class="min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                    placeholder="09XXXXXXXXX"
                                >
                            </div>

                            <div>
                                <label
                                    for="email"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Email Address
                                    <span class="font-normal text-slate-400">
                                        (Optional)
                                    </span>
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    maxlength="150"
                                    autocomplete="email"
                                    value="{{ old('email') }}"
                                    class="min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                    placeholder="you@example.com"
                                >
                            </div>

                            <div>
                                <label
                                    for="number_of_players"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Number of Players
                                </label>

                                <input
                                    id="number_of_players"
                                    name="number_of_players"
                                    type="number"
                                    min="1"
                                    max="30"
                                    inputmode="numeric"
                                    value="{{ old('number_of_players', 2) }}"
                                    required
                                    x-model.number="players"
                                    class="min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                >
                            </div>

                        </div>
                    </div>


                    {{-- Payment --}}
                    <div class="rounded-3xl bg-white p-5 shadow-xl sm:p-7">

                        <div class="mb-6">
                            <div class="text-xs font-black uppercase tracking-widest text-green-600">
                                Step 3
                            </div>

                            <h3 class="mt-1 text-xl font-black">
                                Payment & receipt
                            </h3>
                        </div>

                        <div class="rounded-2xl bg-slate-900 p-5 text-white">
                            <div class="text-sm font-bold text-green-400">
                                Payment Instructions
                            </div>

                            <div class="mt-3 space-y-2 text-sm text-slate-300">
                                <p>
                                    <strong class="text-white">GCash:</strong>
                                    09XX-XXX-XXXX
                                </p>

                                <p>
                                    <strong class="text-white">Account Name:</strong>
                                    Pickleball Court
                                </p>

                                <p class="pt-2 text-xs text-slate-400">
                                    Please replace these sample payment
                                    details with your actual GCash or bank
                                    transfer information.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-5">

                            <div>
                                <label
                                    for="payment_reference"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Payment / Transaction Reference Number
                                </label>

                                <input
                                    id="payment_reference"
                                    name="payment_reference"
                                    type="text"
                                    maxlength="100"
                                    value="{{ old('payment_reference') }}"
                                    required
                                    class="min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20"
                                    placeholder="Enter your transaction reference"
                                >
                            </div>

                            <div>
                                <label
                                    for="payment_receipt"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Payment Receipt
                                </label>

                                <input
                                    id="payment_receipt"
                                    name="payment_receipt"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"
                                    required
                                    class="block min-h-12 w-full cursor-pointer rounded-xl border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:min-h-12 file:border-0 file:bg-green-50 file:px-4 file:font-bold file:text-green-700 hover:file:bg-green-100"
                                >

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Accepted formats: JPG, JPEG, PNG, PDF.
                                    Maximum file size: 5MB.
                                </p>
                            </div>

                        </div>
                    </div>

                </div>


                {{-- =========================================================
                SUMMARY
                ========================================================== --}}

                <aside class="lg:col-span-1">
                    <div class="sticky top-24 rounded-3xl bg-white p-5 shadow-xl sm:p-7">

                        <div class="text-xs font-black uppercase tracking-widest text-green-600">
                            Booking Summary
                        </div>

                        <h3 class="mt-1 text-2xl font-black">
                            Review your booking
                        </h3>

                        <div class="mt-6 divide-y divide-slate-100">

                            <div class="flex justify-between gap-4 py-4">
                                <span class="text-sm text-slate-500">
                                    Customer
                                </span>

                                <span
                                    class="text-right text-sm font-bold"
                                    x-text="customerName || 'Not provided'"
                                ></span>
                            </div>

                            <div class="flex justify-between gap-4 py-4">
                                <span class="text-sm text-slate-500">
                                    Date
                                </span>

                                <span
                                    class="text-right text-sm font-bold"
                                    x-text="formattedDate"
                                ></span>
                            </div>

                            <div class="flex justify-between gap-4 py-4">
                                <span class="text-sm text-slate-500">
                                    Court
                                </span>

                                <span
                                    class="text-right text-sm font-bold"
                                    x-text="courtName"
                                ></span>
                            </div>

                            <div class="flex justify-between gap-4 py-4">
                                <span class="text-sm text-slate-500">
                                    Time
                                </span>

                                <span
                                    class="text-right text-sm font-bold"
                                    x-text="formattedTime"
                                ></span>
                            </div>

                            <div class="flex justify-between gap-4 py-4">
                                <span class="text-sm text-slate-500">
                                    Duration
                                </span>

                                <span
                                    class="text-right text-sm font-bold"
                                    x-text="durationLabel"
                                ></span>
                            </div>

                            <div class="flex justify-between gap-4 py-4">
                                <span class="text-sm text-slate-500">
                                    Players
                                </span>

                                <span
                                    class="text-right text-sm font-bold"
                                    x-text="players || '—'"
                                ></span>
                            </div>

                        </div>

                        <div class="mt-5 rounded-2xl bg-green-50 p-5">

                            <div class="text-sm font-bold text-green-700">
                                Total Price
                            </div>

                            <div
                                class="mt-1 text-3xl font-black text-slate-900"
                                x-text="formattedTotal"
                            ></div>

                        </div>

                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                            Your booking will initially be marked
                            <strong>Pending Verification</strong>.
                            The payment will be checked by the administrator
                            before the booking becomes confirmed.
                        </div>

                        <button
                            type="submit"
                            class="mt-6 inline-flex min-h-14 w-full items-center justify-center rounded-xl bg-green-600 px-5 py-3 text-base font-black text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="submitting"
                        >
                            <span x-show="!submitting">
                                Submit Booking
                            </span>

                            <span
                                x-show="submitting"
                                x-cloak
                            >
                                Submitting...
                            </span>
                        </button>

                        <p class="mt-4 text-center text-xs leading-5 text-slate-500">
                            By submitting this form, you confirm that the
                            information and payment details provided are
                            accurate.
                        </p>

                    </div>
                </aside>

            </div>
        </form>

    </div>
</section>

@endif


{{-- =========================================================================
FOOTER
============================================================================ --}}

<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <div class="font-black">
                    Pickleball Courts
                </div>

                <div class="mt-1 text-sm text-slate-500">
                    Court booking and management system
                </div>
            </div>

            <div class="text-sm text-slate-500">
                © {{ date('Y') }} Pickleball Courts. All rights reserved.
            </div>

        </div>
    </div>
</footer>


{{-- =========================================================================
ALPINE.JS
============================================================================ --}}

<script
    defer
    src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
></script>

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
                if (!this.courtId) {
                    return 'Not selected';
                }

                const select = document.getElementById('court_id');
                const option = select?.querySelector(
                    `option[value="${this.courtId}"]`
                );

                return option?.textContent.trim() || 'Not selected';
            },

            get hourlyRate() {
                if (!this.courtId) {
                    return Number(
                        @json((float) env('PICKLEBALL_HOURLY_RATE', 500))
                    );
                }

                const select = document.getElementById('court_id');
                const option = select?.querySelector(
                    `option[value="${this.courtId}"]`
                );

                return Number(
                    option?.dataset.rate ||
                    @json((float) env('PICKLEBALL_HOURLY_RATE', 500))
                );
            },

            get durationMinutes() {
                if (!this.startTime || !this.endTime) {
                    return 0;
                }

                const [startHour, startMinute] =
                    this.startTime.split(':').map(Number);

                const [endHour, endMinute] =
                    this.endTime.split(':').map(Number);

                const start =
                    (startHour * 60) + startMinute;

                const end =
                    (endHour * 60) + endMinute;

                return Math.max(0, end - start);
            },

            get durationLabel() {
                const minutes = this.durationMinutes;

                if (!minutes) {
                    return 'Not selected';
                }

                const hours = Math.floor(minutes / 60);
                const remaining = minutes % 60;

                if (remaining === 0) {
                    return `${hours} ${hours === 1 ? 'hour' : 'hours'}`;
                }

                return `${hours}h ${remaining}m`;
            },

            get formattedDate() {
                if (!this.bookingDate) {
                    return 'Not selected';
                }

                const date = new Date(
                    `${this.bookingDate}T00:00:00`
                );

                if (Number.isNaN(date.getTime())) {
                    return 'Not selected';
                }

                return new Intl.DateTimeFormat(
                    'en-US',
                    {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }
                ).format(date);
            },

            formatTime(time) {
                if (!time) {
                    return '';
                }

                const [hour, minute] =
                    time.split(':').map(Number);

                const suffix = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;

                return `${displayHour}:${String(minute).padStart(2, '0')} ${suffix}`;
            },

            get formattedTime() {
                if (!this.startTime || !this.endTime) {
                    return 'Not selected';
                }

                return `${this.formatTime(this.startTime)} - ${this.formatTime(this.endTime)}`;
            },

            get totalPrice() {
                if (!this.durationMinutes) {
                    return 0;
                }

                return (
                    this.hourlyRate *
                    (this.durationMinutes / 60)
                );
            },

            get formattedTotal() {
                return new Intl.NumberFormat(
                    'en-PH',
                    {
                        style: 'currency',
                        currency: 'PHP',
                        minimumFractionDigits: 2
                    }
                ).format(this.totalPrice);
            },

            prepareSubmit(event) {
                if (
                    !this.bookingDate ||
                    !this.courtId ||
                    !this.startTime ||
                    !this.endTime
                ) {
                    return;
                }

                if (this.durationMinutes <= 0) {
                    event.preventDefault();

                    alert(
                        'Please select a valid start and end time.'
                    );

                    return;
                }

                if (this.durationMinutes < 30) {
                    event.preventDefault();

                    alert(
                        'A booking must be at least 30 minutes long.'
                    );

                    return;
                }

                this.submitting = true;
            }
        };
    }
</script>

</body>
</html>