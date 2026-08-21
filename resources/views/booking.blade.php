<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pickleball Courts | Book a Court</title>

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
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-2xl sm:text-3xl">🏓</span>
                <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-lime-400">
                    HomeCourt<span class="text-white">PickleHouse</span>
                </span>
            </a>
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-300 hover:text-white transition">Home</a>
            </div>
        </div>
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

    <main class="flex-grow">
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
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Book Now</div>
                    <h2 class="mt-1 text-3xl font-extrabold text-white sm:text-4xl">Reserve your court</h2>
                    <p class="mt-2 text-slate-400">Complete the form below to reserve your court.</p>
                </div>

                <form id="bookingForm" 
                      method="POST" 
                      action="{{ route('booking.store') }}" 
                      x-data="bookingForm()" 
                      @submit="prepareSubmit">
                    @csrf

                    <!-- Hidden Inputs for Form Submission -->
                    <input type="hidden" name="start_time" :value="startTime">
                    <input type="hidden" name="end_time" :value="endTime">

                    <div class="grid gap-8 lg:grid-cols-3">

                        <div class="space-y-6 lg:col-span-2">
                            
                            <!-- STEP 1: SCHEDULE & AVAILABILITY GRID -->
                            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Step 1</div>
                                        <h3 class="text-xl font-bold text-white mt-1">Select court & schedule</h3>
                                    </div>

                                    <template x-if="courtId && isFullyBooked">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                            🔴 Fully Booked
                                        </span>
                                    </template>
                                    <template x-if="courtId && !isFullyBooked">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-lime-400/10 text-lime-400 border border-lime-400/20">
                                            🟢 <span x-text="availableHoursCount"></span> hours available
                                        </span>
                                    </template>
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="booking_date" class="mb-2 block text-sm font-semibold text-slate-300">Booking Date</label>
                                        <input id="booking_date" name="booking_date" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('booking_date', now()->format('Y-m-d')) }}" required x-model="bookingDate" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div>
                                        <label for="court_id" class="mb-2 block text-sm font-semibold text-slate-300">Court</label>
<select id="court_id" name="court_id" required x-model="courtId" @change="selectedSlots = []" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
    <option value="">Select a court</option>
    @foreach ($courts as $court)
        @php
            $type = $court->id <= 4 ? 'Outdoor' : 'Indoor';
            // Fallback to 500 if the database column is empty
            $rate = $court->price_per_hour ?? 500; 
        @endphp
        <option value="{{ $court->id }}" data-rate="{{ $rate }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
            {{ $court->name }} — {{ $type }}
        </option>
    @endforeach
</select>
                                    </div>
                                </div>

                                <!-- TIME SLOT GRID -->
                                <div x-show="courtId && bookingDate" class="mt-6 pt-6 border-t border-slate-800">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">
                                            Court Availability for <span x-text="formattedDate" class="text-lime-400"></span>
                                        </label>
                                        <template x-if="selectedSlots.length > 0">
                                            <button type="button" @click="selectedSlots = []" class="text-xs text-slate-400 hover:text-lime-400 underline">
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
                                                        ? 'bg-red-500/10 border-red-500/30 text-red-400/60 cursor-not-allowed' 
                                                        : (selectedSlots.includes(slot) 
                                                            ? 'bg-lime-400 border-lime-400 text-slate-950 font-bold shadow-lg shadow-lime-400/20' 
                                                            : 'bg-slate-800 border-slate-700 text-slate-200 hover:border-lime-400 hover:text-lime-400 cursor-pointer')
                                                ]"
                                                @click="selectSlot(slot)"
                                                class="p-3 rounded-xl border text-center transition flex flex-col items-center justify-center">
                                                
                                                <span class="text-xs font-bold" x-text="getSlotRangeLabel(slot)"></span>
                                                
                                                <span 
                                                    class="text-[10px] mt-1 font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md" 
                                                    :class="isSlotBooked(slot) ? 'bg-red-500/20 text-red-400' : (selectedSlots.includes(slot) ? 'bg-slate-950/20 text-slate-950' : 'bg-slate-700/50 text-slate-400')">
                                                    <span x-text="isSlotBooked(slot) ? 'Booked' : (selectedSlots.includes(slot) ? 'Selected' : 'Available')"></span>
                                                </span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="courtId && bookingDate" class="mt-4 text-xs text-slate-400 flex items-center gap-2">
                                    <span>💡 Click multiple slots to reserve contiguous time blocks.</span>
                                </div>
                            </div>

                            <!-- STEP 2: CUSTOMER DETAILS -->
                            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                                <div class="mb-6">
                                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Step 2</div>
                                    <h3 class="text-xl font-bold text-white mt-1">Your details</h3>
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-300">Full Name</label>
                                        <input id="name" name="name" type="text" maxlength="100" autocomplete="name" value="{{ old('name') }}" required x-model="customerName" placeholder="Juan Dela Cruz" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div>
                                        <label for="contact_number" class="mb-2 block text-sm font-semibold text-slate-300">Contact Number</label>
                                        <input id="contact_number" name="contact_number" type="tel" maxlength="30" autocomplete="tel" value="{{ old('contact_number') }}" required placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="number_of_players" class="mb-2 block text-sm font-semibold text-slate-300">Number of Players</label>
                                        <input id="number_of_players" name="number_of_players" type="number" min="1" max="30" value="{{ old('number_of_players', 2) }}" required x-model.number="players" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: PAYMENT METHOD -->
                            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                                <div class="mb-6">
                                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Step 3</div>
                                    <h3 class="text-xl font-bold text-white mt-1">Payment Method</h3>
                                </div>
                                <div class="space-y-5">
                                    <!-- Mode of Payment Dropdown -->
                                    <div>
                                        <label for="payment_method" class="mb-2 block text-sm font-semibold text-slate-300">Mode of Payment</label>
                                        <select id="payment_method" name="payment_method" x-model="paymentMethod" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none focus:border-lime-400">
                                            <option value="GCash">GCash</option>
                                            <option value="Bank">Bank Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Account Details Box (Dynamic) -->
                                    <div x-show="paymentMethod === 'GCash'" class="rounded-xl border border-lime-500/30 bg-lime-900/10 p-4">
                                        <div class="text-sm text-slate-300 mb-1">Please send your payment to our GCash account:</div>
                                        <div class="text-xl font-black tracking-wider text-lime-400">0912 345 6789</div>
                                        <div class="text-sm font-bold text-white mt-1">Account Name: HomeCourt PickleHouse</div>
                                    </div>

                                    <div x-cloak x-show="paymentMethod === 'Bank'" class="rounded-xl border border-lime-500/30 bg-lime-900/10 p-4">
                                        <div class="text-sm text-slate-300 mb-1">Please send your payment to our Bank account:</div>
                                        <div class="text-xl font-black tracking-wider text-lime-400">BDO: 001234567890</div>
                                        <div class="text-sm font-bold text-white mt-1">Account Name: HomeCourt PickleHouse</div>
                                    </div>

                                    <!-- NEW: Facebook Verification Instructions -->
                                    <div class="mt-4 rounded-xl border border-blue-500/30 bg-blue-900/10 p-4">
                                        <p class="text-sm text-slate-300 font-semibold mb-2">📸 Next Steps:</p>
                                        <p class="text-xs text-slate-400">
                                            Before clicking "Submit Booking", please send a screenshot of your payment to our 
                                            <a href="https://facebook.com/yourpage" target="_blank" class="text-blue-400 font-bold underline">Facebook Page</a>. 
                                            We will manually verify it and send you a confirmation text!
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- SIDEBAR SUMMARY -->
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

                                <button type="submit" :disabled="submitting || isFullyBooked || selectedSlots.length === 0" class="w-full rounded-xl bg-lime-400 py-4 font-bold text-slate-950 hover:bg-lime-300 transition shadow-lg shadow-lime-400/20 disabled:opacity-50">
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
    <footer class="border-t border-slate-800 py-8 bg-slate-950 mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-slate-500">
            <p>© {{ date('Y') }} HomeCourt PickleHouse. All rights reserved.</p>
        </div>
    </footer>

    <!-- Alpine.js Script -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function bookingForm() {
            return {
                bookingDate: @js(old('booking_date', now()->format('Y-m-d'))),
                courtId: new URLSearchParams(window.location.search).get('court_id') || @js(old('court_id', '')),
                selectedSlots: [],
                customerName: @js(old('name', '')),
                players: @js((int) old('number_of_players', 2)),
                paymentMethod: 'GCash', 
                submitting: false,
                existingBookings: @js($existingBookings ?? []),

                allSlots: [
                    '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
                    '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
                    '18:00', '19:00', '20:00', '21:00', '22:00'
                ],

                get startTime() {
                    if (this.selectedSlots.length === 0) return '';
                    const sorted = [...this.selectedSlots].sort();
                    return sorted[0];
                },

                get endTime() {
                    if (this.selectedSlots.length === 0) return '';
                    const sorted = [...this.selectedSlots].sort();
                    const lastSlot = sorted[sorted.length - 1];
                    const [hour, minute] = lastSlot.split(':').map(Number);
                    return `${String(hour + 1).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
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

                    if (this.selectedSlots.includes(slot)) {
                        this.selectedSlots = this.selectedSlots.filter(s => s !== slot);
                    } else {
                        if (this.selectedSlots.length === 0) {
                            this.selectedSlots.push(slot);
                        } else {
                            const sorted = [...this.selectedSlots].sort();
                            const firstIdx = this.allSlots.indexOf(sorted[0]);
                            const lastIdx = this.allSlots.indexOf(sorted[sorted.length - 1]);
                            const currentIdx = this.allSlots.indexOf(slot);

                            if (currentIdx < firstIdx) {
                                for (let i = currentIdx; i <= lastIdx; i++) {
                                    const s = this.allSlots[i];
                                    if (!this.isSlotBooked(s) && !this.selectedSlots.includes(s)) {
                                        this.selectedSlots.push(s);
                                    }
                                }
                            } else if (currentIdx > lastIdx) {
                                for (let i = firstIdx; i <= currentIdx; i++) {
                                    const s = this.allSlots[i];
                                    if (!this.isSlotBooked(s) && !this.selectedSlots.includes(s)) {
                                        this.selectedSlots.push(s);
                                    }
                                }
                            } else {
                                this.selectedSlots.push(slot);
                            }
                        }
                    }
                },

                get activeBookingsForSelected() {
                    if (!this.courtId || !this.bookingDate) return [];
                    return this.existingBookings.filter(b => 
                        String(b.court_id) === String(this.courtId) && 
                        b.booking_date === this.bookingDate
                    );
                },

                isSlotBooked(slotTime) {
                    return this.activeBookingsForSelected.some(b => {
                        const start = b.start_time.substring(0, 5);
                        const end = b.end_time.substring(0, 5);
                        return slotTime >= start && slotTime < end;
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
                    const suffix = hour >= 12 ? 'PM' : 'AM';
                    const displayHour = hour % 12 || 12;
                    return `${displayHour}:${String(minute).padStart(2, '0')} ${suffix}`;
                },

                get formattedTime() {
                    if (!this.startTime || !this.endTime) return 'Not selected';
                    return `${this.formatTime(this.startTime)} - ${this.formatTime(this.endTime)}`;
                },

                get totalPrice() {
                    return this.selectedSlots.length * this.hourlyRate;
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