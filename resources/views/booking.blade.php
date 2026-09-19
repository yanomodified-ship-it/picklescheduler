<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pickleball Courts | Book a Court</title>

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
    <header class="border-b border-gray-200 bg-white/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high" class="h-10 sm:h-12 w-auto" style="height: 44px; width: auto; max-height: 44px;">
            </a>
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition">Home</a>
            </div>
        </div>
    </header>

    <!-- Flash / Validation Messages -->
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-6 pt-6 w-full">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
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
            <div class="rounded-2xl border border-lime-200 bg-lime-50 p-4 text-lime-700">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="flex-grow">
    @if (isset($confirmationBooking))

        <!-- Confirmation View -->
        <section class="min-h-[70vh] px-6 py-12">
            <div class="max-w-3xl mx-auto">
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl">
                    <div class="bg-gradient-to-r from-lime-600 to-emerald-600 px-6 py-10 text-center text-white">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-3xl text-lime-600 font-bold">
                            ✓
                        </div>
                        <h1 class="mt-5 text-3xl font-extrabold sm:text-4xl">Booking Submitted!</h1>
                        <p class="mx-auto mt-3 max-w-xl text-white/90 font-medium">
                            Your booking request has been received and is awaiting payment verification.
                        </p>
                    </div>

                    <div class="p-6 sm:p-10 space-y-6">
                        <div class="rounded-2xl bg-gray-50 border border-gray-200 p-5 text-center">
                            <div class="text-xs font-bold uppercase tracking-widest text-gray-500">Booking Reference</div>
                            <div class="mt-2 text-3xl font-black tracking-wider text-lime-600">
                                {{ $confirmationBooking->booking_reference }}
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase text-gray-500">Customer</div>
                                <div class="mt-1 font-bold text-gray-900">{{ $confirmationBooking->customer?->name ?? '—' }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase text-gray-500">Court</div>
                                <div class="mt-1 font-bold text-gray-900">{{ $confirmationBooking->court?->name ?? '—' }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase text-gray-500">Date</div>
                                <div class="mt-1 font-bold text-gray-900">{{ \Carbon\Carbon::parse($confirmationBooking->booking_date)->format('F d, Y') }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase text-gray-500">Time</div>
                                <div class="mt-1 font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($confirmationBooking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($confirmationBooking->end_time)->format('g:i A') }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase text-gray-500">Players</div>
                                <div class="mt-1 font-bold text-gray-900">{{ $confirmationBooking->number_of_players ?? '—' }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase text-gray-500">Total</div>
                                <div class="mt-1 font-bold text-lime-600">₱{{ number_format((float) $confirmationBooking->total_price, 2) }}</div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5">
                                <div class="font-bold text-amber-700">Booking Status</div>
                                <div class="mt-2 inline-flex rounded-full bg-amber-100 px-3 py-1 text-sm font-bold text-amber-700">
                                    {{ $confirmationBooking->booking_status }}
                                </div>
                            </div>
                            <div class="rounded-2xl bg-blue-50 border border-blue-200 p-5">
                                <div class="font-bold text-blue-700">Payment Status</div>
                                <div class="mt-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-bold text-blue-700">
                                    {{ $confirmationBooking->payment_status }}
                                </div>
                            </div>
                        </div>

                        <div class="text-center pt-4">
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-lime-600 px-8 py-3 font-bold text-white transition hover:bg-lime-500">
                                Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @else

        <!-- Booking Form Section -->
        <section id="booking" class="px-6 py-10">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mb-10">
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Book Now</div>
                    <h2 class="mt-1 text-3xl font-extrabold text-gray-900 sm:text-4xl">Reserve your court</h2>
                    <p class="mt-2 text-gray-500">Complete the form below to reserve your court.</p>
                </div>

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
                            <div class="rounded-3xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
                                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Step 1</div>
                                        <h3 class="text-xl font-bold text-gray-900 mt-1">Select court & schedule</h3>
                                    </div>

                                    <template x-if="courtId && isFullyBooked">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                            🔴 Fully Booked
                                        </span>
                                    </template>
                                    <template x-if="courtId && !isFullyBooked">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-lime-50 text-lime-700 border border-lime-200">
                                            🟢 <span x-text="availableHoursCount"></span> hours available
                                        </span>
                                    </template>
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="booking_date" class="mb-2 block text-sm font-semibold text-gray-700">Booking Date</label>
                                        <input id="booking_date" name="booking_date" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('booking_date', now()->format('Y-m-d')) }}" required x-model="bookingDate" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 outline-none focus:border-lime-500 focus:bg-white">
                                    </div>
                                    <div>
                                        <label for="court_id" class="mb-2 block text-sm font-semibold text-gray-700">Court</label>
                                        <select id="court_id" name="court_id" required x-model="courtId" @change="selectedSlots = []" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 outline-none focus:border-lime-500 focus:bg-white">
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
                                <div x-show="courtId && bookingDate" class="mt-6 pt-6 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500">
                                            Court Availability for <span x-text="formattedDate" class="text-lime-600"></span>
                                        </label>
                                        <template x-if="selectedSlots.length > 0">
                                            <button type="button" @click="selectedSlots = []" class="text-xs text-gray-500 hover:text-lime-600 underline">
                                                Clear Selection
                                            </button>
                                        </template>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                                        <template x-for="slot in allSlots" :key="slot">
                                            <button 
                                                type="button"
                                                :disabled="isSlotBooked(slot)"
                                                :class="[
                                                    isSlotBooked(slot) 
                                                        ? (isSlotPending(slot)
                                                            ? 'bg-amber-50 border-amber-200 text-amber-500 cursor-not-allowed'
                                                            : 'bg-red-50 border-red-200 text-red-400 cursor-not-allowed')
                                                        : (selectedSlots.includes(slot) 
                                                            ? 'bg-lime-600 border-lime-600 text-white font-bold shadow-lg shadow-lime-600/20' 
                                                            : 'bg-gray-50 border-gray-200 text-gray-700 hover:border-lime-500 hover:text-lime-600 cursor-pointer')
                                                ]"
                                                @click="selectSlot(slot)"
                                                class="p-3 rounded-xl border text-center transition flex flex-col items-center justify-center">
                                                
                                                <span class="text-xs font-bold" x-text="getSlotRangeLabel(slot)"></span>
                                                
                                                <span 
                                                    class="text-[10px] mt-1 font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md" 
                                                    :class="isSlotBooked(slot) ? (isSlotPending(slot) ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') : (selectedSlots.includes(slot) ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-500')">
                                                    <span x-text="isSlotBooked(slot) ? (isSlotPending(slot) ? 'Pending' : 'Booked') : (selectedSlots.includes(slot) ? 'Selected' : 'Available')"></span>
                                                </span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="courtId && bookingDate" class="mt-4 text-xs text-gray-500 flex items-center gap-2">
                                    <span>💡 Click any available slots to select them individually — they don't need to be next to each other.</span>
                                </div>
                            </div>

                            <!-- STEP 2: CUSTOMER DETAILS -->
                            <div class="rounded-3xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
                                <div class="mb-6">
                                    <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Step 2</div>
                                    <h3 class="text-xl font-bold text-gray-900 mt-1">Your details</h3>
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Facebook Name </label>
                                        <input id="name" name="name" type="text" maxlength="100" autocomplete="name" value="{{ old('name') }}" required x-model="customerName" placeholder="Juan Dela Cruz" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 outline-none focus:border-lime-500 focus:bg-white">
                                    </div>
                                    <div>
                                        <label for="contact_number" class="mb-2 block text-sm font-semibold text-gray-700">Contact Number</label>
                                        <input id="contact_number" name="contact_number" type="tel" maxlength="30" autocomplete="tel" value="{{ old('contact_number') }}" required x-model="contactNumber" placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 outline-none focus:border-lime-500 focus:bg-white">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="number_of_players" class="mb-2 block text-sm font-semibold text-gray-700">Number of Players</label>
                                        <input id="number_of_players" name="number_of_players" type="number" min="1" max="30" value="{{ old('number_of_players', 2) }}" required x-model.number="players" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 outline-none focus:border-lime-500 focus:bg-white">
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: PAYMENT METHOD -->
                            <div class="rounded-3xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
                                <div class="mb-6">
                                    <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Step 3</div>
                                    <h3 class="text-xl font-bold text-gray-900 mt-1">Payment Method</h3>
                                </div>
                                <div class="space-y-5">
                                    <!-- Mode of Payment Dropdown -->
                                    <div>
                                        <label for="payment_method" class="mb-2 block text-sm font-semibold text-gray-700">Mode of Payment</label>
                                        <select id="payment_method" name="payment_method" x-model="paymentMethod" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 outline-none focus:border-lime-500 focus:bg-white">
                                            <option value="GCash">GCash</option>
                                            <option value="Bank">Bank Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Account details are shown on the confirmation page after
                                         submitting, to avoid presenting payment info twice. -->
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
                                        You'll see the account details to send your payment to right after you submit this booking.
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- SIDEBAR SUMMARY -->
                        <aside class="lg:col-span-1">
                            <div class="sticky top-24 rounded-3xl border border-gray-200 bg-white shadow-xl p-6 space-y-4">
                                <div class="text-xs font-bold uppercase tracking-widest text-lime-600">Summary</div>
                                <h3 class="text-2xl font-bold text-gray-900">Review booking</h3>

                                <div class="divide-y divide-gray-200 text-sm">
                                    <div class="flex justify-between py-3">
                                        <span class="text-gray-500">Customer</span>
                                        <span class="font-bold text-gray-900 text-right" x-text="customerName || 'Not provided'"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-gray-500">Contact Number</span>
                                        <span class="font-bold text-gray-900 text-right" x-text="contactNumber || 'Not provided'"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-gray-500">Date</span>
                                        <span class="font-bold text-gray-900 text-right" x-text="formattedDate"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-gray-500">Court</span>
                                        <span class="font-bold text-gray-900 text-right" x-text="courtName"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-gray-500">Time</span>
                                        <span class="font-bold text-gray-900 text-right" x-text="formattedTime"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-gray-500">Duration</span>
                                        <span class="font-bold text-gray-900 text-right" x-text="durationLabel"></span>
                                    </div>
                                    <div class="flex justify-between py-3">
                                        <span class="text-gray-500">Players</span>
                                        <span class="font-bold text-gray-900 text-right" x-text="players || '—'"></span>
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-gray-50 p-4 border border-gray-200">
                                    <div class="text-xs font-bold text-gray-500">Total Price</div>
                                    <div class="text-3xl font-black text-lime-600 mt-1" x-text="formattedTotal"></div>
                                </div>

                                <button type="submit" :disabled="submitting || isFullyBooked || selectedSlots.length === 0" class="w-full rounded-xl bg-lime-600 py-4 font-bold text-white hover:bg-lime-500 transition shadow-lg shadow-lime-600/20 disabled:opacity-50">
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
    <footer class="border-t border-gray-200 py-8 bg-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-500">
            <p>© {{ date('Y') }} HomeCourt PickleHouse. All rights reserved.</p>
        </div>
    </footer>

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
                submitting: false,
                existingBookings: @js($existingBookings ?? []),

                // Replace the static array with this dynamic getter
                get allSlots() {
    if (!this.courtId) return [];
    const court = this.courts.find(c => String(c.id) === String(this.courtId));
    if (!court) return [];

    let startHour = parseInt((court.operating_hours_start || '05:00').substring(0, 2), 10);
    let endHour = parseInt((court.operating_hours_end || '23:00').substring(0, 2), 10);

    // If close time is 00:00 (midnight), treat it as 24
    if (endHour === 0 && (court.operating_hours_end || '').startsWith('00')) {
        endHour = 24;
    }

    let slots = [];
    for (let i = startHour; i <= endHour; i++) {
        let hourString = String(i === 24 ? 0 : i).padStart(2, '0');
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
                    const sorted = [...this.selectedSlots].sort();
                    return sorted.map(slot => this.getSlotRangeLabel(slot)).join(', ');
                },

                get totalPrice() {
                    return this.selectedSlots.reduce((total, slot) => {
                        const hour = parseInt(slot.split(':')[0], 10);
                        const rate = (hour >= 5 && hour < 17) ? 150 : 300;
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
                    this.submitting = true;
                }
            };
        }
    </script>
</body>
</html>