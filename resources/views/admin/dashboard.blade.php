<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | HomeCourt PickleHouse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        @media print {
            body * { visibility: hidden; }
            #printable-receipt, #printable-receipt * { visibility: visible; }
            #printable-receipt { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased flex flex-col" x-data="{ activeTab: 'bookings', selectedBooking: null, showRejectModal: false, rejectReason: '' }">

    <!-- Top Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">🏓</span>
                <span class="text-xl font-extrabold text-lime-400">HomeCourt <span class="text-white">Admin</span></span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-xs font-semibold text-slate-400 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">👤 {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-300 transition">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-8">
        
        <!-- Flash Alert -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-lime-500/30 bg-lime-900/20 p-4 text-lime-300 font-bold text-sm">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- TOP METRICS GRID -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Bookings</div>
                <div class="text-2xl font-black text-white mt-1">{{ $totalBookings }}</div>
            </div>
            <div class="bg-slate-900 border border-amber-500/30 rounded-2xl p-4 bg-amber-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Pending Verification</div>
                <div class="text-2xl font-black text-amber-400 mt-1">{{ $pendingCount }}</div>
            </div>
            <div class="bg-slate-900 border border-emerald-500/30 rounded-2xl p-4 bg-emerald-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Confirmed</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ $confirmedCount }}</div>
            </div>
            <div class="bg-slate-900 border border-blue-500/30 rounded-2xl p-4 bg-blue-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Today's Bookings</div>
                <div class="text-2xl font-black text-blue-400 mt-1">{{ $todaysCount }}</div>
            </div>
            <div class="bg-slate-900 border border-red-500/30 rounded-2xl p-4 bg-red-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-red-400">Cancelled / Rejected</div>
                <div class="text-2xl font-black text-red-400 mt-1">{{ $cancelledCount }}</div>
            </div>
            <div class="bg-slate-900 border border-lime-500/30 rounded-2xl p-4 bg-lime-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-lime-400">Total Revenue</div>
                <div class="text-xl font-black text-lime-400 mt-1">₱{{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="flex space-x-3 mb-6 border-b border-slate-800 pb-3">
            <button @click="activeTab = 'bookings'" :class="activeTab === 'bookings' ? 'bg-lime-400 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white'" class="px-5 py-2.5 rounded-xl text-sm transition">
                📋 Booking Management
            </button>
            <button @click="activeTab = 'courts'" :class="activeTab === 'courts' ? 'bg-lime-400 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white'" class="px-5 py-2.5 rounded-xl text-sm transition">
                ⚙️ Court Management (1–7)
            </button>
        </div>

        <!-- SECTION 1: BOOKING MANAGEMENT TABLE -->
        <div x-show="activeTab === 'bookings'" class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase border-b border-slate-800">
                        <tr>
                            <th class="p-4">Reference</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Contact</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Court</th>
                            <th class="p-4">Time</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Payment Status</th>
                            <th class="p-4">Booking Status</th>
                            <th class="p-4">Receipt</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse ($bookings as $b)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="p-4 font-extrabold text-lime-400">{{ $b->booking_reference }}</td>
                                <td class="p-4 text-white font-bold">{{ $b->customer->name ?? 'N/A' }}</td>
                                <td class="p-4">{{ $b->customer->contact_number ?? 'N/A' }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($b->booking_date)->format('M d, Y') }}</td>
                                <td class="p-4 text-white font-bold">{{ $b->court->name ?? 'N/A' }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($b->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($b->end_time)->format('g:i A') }}</td>
                                <td class="p-4 font-bold text-white">₱{{ number_format($b->total_price, 2) }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                        {{ $b->payment_status === 'Verified' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($b->payment_status === 'Rejected' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400') }}">
                                        {{ $b->payment_status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                        {{ $b->booking_status === 'Confirmed' ? 'bg-emerald-500/20 text-emerald-400' : ($b->booking_status === 'Rejected' || $b->booking_status === 'Cancelled' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400') }}">
                                        {{ $b->booking_status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if ($b->receipt_path)
                                        <a href="{{ route('admin.receipt.show', $b->id) }}" target="_blank" class="text-lime-400 underline hover:text-lime-300 font-bold">View Receipt</a>
                                    @else
                                        <span class="text-slate-500">None</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right space-x-1">
                                    <button @click="selectedBooking = {{ json_encode($b->load(['customer', 'court'])) }}" class="bg-slate-800 hover:bg-slate-700 text-white text-[11px] px-2.5 py-1.5 rounded-lg border border-slate-700">
                                        View
                                    </button>
                                    <form method="POST" action="{{ route('admin.bookings.delete', $b->id) }}" class="inline" onsubmit="return confirm('Delete this booking permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-900/40 hover:bg-red-800/60 text-red-300 text-[11px] px-2.5 py-1.5 rounded-lg border border-red-700/50">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="p-8 text-center text-slate-500">No bookings available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 2: COURT MANAGEMENT (1-7) -->
        <div x-show="activeTab === 'courts'" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($courts as $court)
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between">
                    <form method="POST" action="{{ route('admin.courts.update', $court->id) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>🏓 {{ $court->name }}</span>
                            </h3>
                            <span class="text-xs px-2.5 py-1 rounded-full font-bold uppercase tracking-wider
                                {{ $court->status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : ($court->status === 'maintenance' ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400') }}">
                                {{ $court->status }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 font-semibold mb-1">Status</label>
                            <select name="status" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-xs outline-none">
                                <option value="active" {{ $court->status === 'active' ? 'selected' : '' }}>🟢 Active / Enabled</option>
                                <option value="maintenance" {{ $court->status === 'maintenance' ? 'selected' : '' }}>🛠 Maintenance</option>
                                <option value="disabled" {{ $court->status === 'disabled' ? 'selected' : '' }}>🔴 Disabled</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 font-semibold mb-1">Hourly Rate (₱)</label>
                            <input type="number" step="0.01" name="price_per_hour" value="{{ $court->price_per_hour }}" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-xs outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-slate-400 font-semibold mb-1">Open Time</label>
                                <input type="time" name="operating_hours_start" value="{{ $court->operating_hours_start }}" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-xs outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-400 font-semibold mb-1">Close Time</label>
                                <input type="time" name="operating_hours_end" value="{{ $court->operating_hours_end }}" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py```html
                                    text-white text-xs outline-none">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl border border-slate-700 transition">
                            Save Court Settings
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

    </main>

    <!-- MODAL: BOOKING VERIFICATION -->
    <div x-show="selectedBooking !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div @click.away="selectedBooking = null; showRejectModal = false;" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl relative">
            
            <button @click="selectedBooking = null; showRejectModal = false;" class="absolute top-5 right-5 text-slate-400 hover:text-white">✕</button>

            <div class="p-6 sm:p-8 overflow-y-auto" id="printable-receipt">
                <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Review Booking</div>
                <h2 class="text-2xl font-black text-white mt-1 mb-6" x-text="selectedBooking?.booking_reference"></h2>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                            <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Customer Info</h3>
                            <div class="font-bold text-white" x-text="selectedBooking?.customer?.name"></div>
                            <div class="text-slate-400 text-sm" x-text="selectedBooking?.customer?.contact_number"></div>
                        </div>
                        
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                            <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Booking Info</h3>
                            <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Court</span> <span class="font-bold text-white" x-text="selectedBooking?.court?.name"></span></div>
                            <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Date</span> <span class="font-bold text-white" x-text="selectedBooking?.booking_date"></span></div>
                            <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Time</span> <span class="font-bold text-white" x-text="selectedBooking?.start_time + ' - ' + selectedBooking?.end_time"></span></div>
                            <div class="flex justify-between text-sm"><span class="text-slate-400">Players</span> <span class="font-bold text-white" x-text="selectedBooking?.number_of_players"></span></div>
                        </div>

                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                            <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Payment Details</h3>
                            <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Method</span> <span class="font-bold text-white" x-text="selectedBooking?.payment_method || 'GCash'"></span></div>
                            <div class="flex justify-between text-sm mb-2"><span class="text-slate-400">Total Due</span> <span class="font-black text-lime-400" x-text="'₱' + selectedBooking?.total_price"></span></div>
                            
                            <template x-if="selectedBooking?.payment_status === 'Verified'">
                                <span class="inline-block px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">VERIFIED</span>
                            </template>
                            <template x-if="selectedBooking?.payment_status === 'Rejected'">
                                <div>
                                    <span class="inline-block px-2.5 py-1 rounded bg-red-500/20 text-red-400 text-[10px] font-bold mb-1">REJECTED</span>
                                    <div class="text-[11px] text-red-400" x-text="'Reason: ' + selectedBooking?.rejection_reason"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 h-full flex flex-col">
                            <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Payment Receipt</h3>
                            <div class="flex-grow flex items-center justify-center border-2 border-dashed border-slate-800 rounded-lg bg-slate-900/50 p-2 overflow-hidden">
                                <template x-if="selectedBooking?.receipt_path">
                                    <img :src="'/admin/receipt/' + selectedBooking?.id" class="max-h-48 object-contain rounded" alt="Receipt">
                                </template>
                                <template x-if="!selectedBooking?.receipt_path">
                                    <span class="text-slate-500 text-sm">No receipt uploaded</span>
                                </template>
                            </div>
                            <template x-if="selectedBooking?.receipt_path">
                                <a :href="'/admin/receipt/' + selectedBooking?.id" target="_blank" class="mt-3 block text-center text-xs font-bold text-lime-400 hover:text-lime-300">View Full Image ↗</a>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Admin Action Buttons -->
                <div class="mt-8 pt-6 border-t border-slate-800 flex flex-wrap gap-3" x-show="!showRejectModal">
                    <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-700">🖨️ Print</button>
                    
                    <div class="ml-auto flex gap-3">
                        <template x-if="selectedBooking?.payment_status !== 'Verified'">
                            <form :action="'/admin/bookings/' + selectedBooking?.id + '/approve'" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-5 py-2 bg-lime-400 text-slate-950 text-sm font-bold rounded-xl hover:bg-lime-300 shadow-lg shadow-lime-400/20">✓ Approve Payment</button>
                            </form>
                        </template>

                        <template x-if="selectedBooking?.payment_status !== 'Rejected'">
                            <button @click="showRejectModal = true" type="button" class="px-5 py-2 bg-red-900/40 text-red-400 text-sm font-bold rounded-xl hover:bg-red-900/60 border border-red-900/50">✕ Reject Payment</button>
                        </template>
                    </div>
                </div>

                <!-- Rejection Form Input -->
                <div class="mt-8 pt-6 border-t border-slate-800" x-show="showRejectModal" x-cloak>
                    <form :action="'/admin/bookings/' + selectedBooking?.id + '/reject'" method="POST" class="flex flex-col gap-3">
                        @csrf @method('PATCH')
                        <label class="text-sm font-bold text-red-400">Reason for rejection:</label>
                        <textarea name="rejection_reason" required rows="2" class="w-full rounded-xl border border-red-900 bg-slate-950 px-4 py-3 text-white outline-none focus:border-red-500" placeholder="e.g., Receipt is blurry, or payment amount is incorrect..."></textarea>
                        <div class="flex justify-end gap-3 mt-2">
                            <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-slate-400 text-sm font-bold">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-500 shadow-lg shadow-red-600/20">Confirm Rejection</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>
</html>