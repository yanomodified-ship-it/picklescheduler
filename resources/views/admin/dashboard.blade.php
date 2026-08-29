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
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased flex flex-col" 
      x-data="adminDashboard({{ json_encode($bookings->load(['customer', 'court'])) }}, {{ json_encode($courts) }})">

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
        
        <!-- Flash Alerts -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-lime-500/30 bg-lime-900/20 p-4 text-lime-300 font-bold text-sm">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-900/20 p-4 text-red-300 font-bold text-sm">
                ✕ {{ session('error') }}
            </div>
        @endif

        <!-- TOP METRICS GRID -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4 mb-8">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Bookings</div>
                <div class="text-2xl font-black text-white mt-1">{{ $totalBookings }}</div>
            </div>
            <div class="bg-slate-900 border border-amber-500/30 rounded-2xl p-4 bg-amber-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Pending</div>
                <div class="text-2xl font-black text-amber-400 mt-1">{{ $pendingCount }}</div>
            </div>
            <div class="bg-slate-900 border border-emerald-500/30 rounded-2xl p-4 bg-emerald-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Confirmed</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ $confirmedCount }}</div>
            </div>
            <div class="bg-slate-900 border border-blue-500/30 rounded-2xl p-4 bg-blue-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Today's</div>
                <div class="text-2xl font-black text-blue-400 mt-1">{{ $todaysCount }}</div>
            </div>
            <div class="bg-slate-900 border border-red-500/30 rounded-2xl p-4 bg-red-500/5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-red-400">Cancelled</div>
                <div class="text-2xl font-black text-red-400 mt-1">{{ $cancelledCount }}</div>
            </div>
            
            <!-- REVENUE TRACKER WITH CLEAR FILTER BUTTON -->
            <div class="bg-slate-900 border border-lime-500/40 rounded-2xl p-4 bg-lime-500/5 col-span-2 flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2 gap-2">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-lime-400">Revenue Tracker</div>
                    <div class="flex gap-1 items-center">
                        <input type="date" x-model="revenueFilterDate" class="bg-slate-950 border border-lime-500/30 rounded-lg px-2 py-1 text-[10px] text-white outline-none focus:border-lime-400" title="Filter by Date">
                        <select x-model="revenueFilterCourt" class="bg-slate-950 border border-lime-500/30 rounded-lg px-2 py-1 text-[10px] text-white outline-none focus:border-lime-400" title="Filter by Court">
                            <option value="">All Courts</option>
                            <template x-for="c in courts" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                        </select>
                        <button type="button" @click="revenueFilterDate = ''; revenueFilterCourt = ''" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] px-2 py-1 rounded border border-slate-700 transition" title="Clear Filters">Clear</button>
                    </div>
                </div>
                <div class="text-2xl font-black text-lime-400 mt-1" x-text="calculatedRevenue"></div>
            </div>
        </div>

        <!-- NAVIGATION CONTROLS -->
        <div class="flex space-x-3 mb-6 border-b border-slate-800 pb-3">
            <button @click="activeTab = 'schedule'" :class="activeTab === 'schedule' ? 'bg-lime-400 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white'" class="px-5 py-2.5 rounded-xl text-sm transition">🗓️ Schedule Grid</button>
            <button @click="activeTab = 'bookings'" :class="activeTab === 'bookings' ? 'bg-lime-400 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white'" class="px-5 py-2.5 rounded-xl text-sm transition">📋 Booking History</button>
            <button @click="activeTab = 'courts'" :class="activeTab === 'courts' ? 'bg-lime-400 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white'" class="px-5 py-2.5 rounded-xl text-sm transition">⚙️ Court Settings</button>
        </div>

        <!-- SECTION 1: INTERACTIVE SCHEDULE GRID -->
        <div x-show="activeTab === 'schedule'" x-cloak class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
            <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Admin Live Schedule</div>
                    <h3 class="text-2xl font-black text-white mt-1">Select Court & Slot Verification</h3>
                </div>

                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Quick Day Nav</label>
                        <div class="flex items-center bg-slate-800 rounded-xl border border-slate-700 p-1">
                            <button type="button" @click="changeDate(-1)" class="px-2.5 py-1 text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition">◀ Prev</button>
                            <button type="button" @click="setToday()" class="px-2.5 py-1 text-xs font-bold text-lime-400 hover:bg-slate-700 rounded-lg transition">Today</button>
                            <button type="button" @click="changeDate(1)" class="px-2.5 py-1 text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition">Next ▶</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Date</label>
                        <input type="date" x-model="selectedDate" class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-1.5 text-white text-xs outline-none focus:border-lime-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Court</label>
                        <select x-model="selectedCourtId" @change="$dispatch('court-changed')" class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-1.5 text-white text-xs outline-none focus:border-lime-400">
                            <template x-for="c in courts" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Slot Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <template x-for="slot in allSlots" :key="slot">
                    <button @click="handleSlotClick(slot)" :class="getSlotStyle(slot)" class="p-3 rounded-2xl border text-center transition flex flex-col items-center justify-between min-h-[105px] relative overflow-hidden group">
                        <div class="text-xs font-bold text-slate-300" x-text="formatTime(slot)"></div>
                        <div class="my-1.5 w-full text-center">
                            <template x-if="getBookingForSlot(slot)">
                                <div class="flex flex-col items-center">
                                    <span class="text-[11px] font-black tracking-wider text-lime-400 truncate max-w-full" x-text="getBookingForSlot(slot).booking_reference"></span>
                                    <span class="text-[10px] font-medium text-slate-300 truncate max-w-full" x-text="getBookingForSlot(slot).customer?.name"></span>
                                </div>
                            </template>
                            <template x-if="!getBookingForSlot(slot)">
                                <span class="text-[11px] font-medium text-slate-600 group-hover:text-lime-400 transition">+ Walk-In</span>
                            </template>
                        </div>
                        <span class="text-[9px] uppercase px-2 py-0.5 rounded-md truncate max-w-full font-extrabold tracking-wide" :class="getBadgeStyle(slot)" x-text="getSlotStatusLabel(slot)"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- SECTION 2: BOOKING HISTORY TABLE -->
        <div x-show="activeTab === 'bookings'" x-cloak class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="bg-slate-950 p-4 border-b border-slate-800 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Filter by Date</label>
                    <input type="date" x-model="historyFilterDate" class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 text-white text-xs outline-none focus:border-lime-400">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Filter by Court</label>
                    <select x-model="historyFilterCourt" class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 text-white text-xs outline-none focus:border-lime-400">
                        <option value="">All Courts</option>
                        <template x-for="c in courts" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                    </select>
                </div>
                <button @click="historyFilterDate = ''; historyFilterCourt = ''" class="px-3 py-1.5 text-xs font-bold text-slate-400 hover:text-white bg-slate-800 rounded-lg">Clear Filters</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase border-b border-slate-800">
                        <tr>
                            <th class="p-4">Reference</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Court</th>
                            <th class="p-4">Time</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse ($bookings as $b)
                            <tr x-show="showHistoryRow('{{ $b->booking_date }}', '{{ $b->court_id }}')" class="hover:bg-slate-800/40 transition">
                                <td class="p-4 font-extrabold text-lime-400">{{ $b->booking_reference }}</td>
                                <td class="p-4 text-white font-bold">{{ $b->customer->name ?? 'N/A' }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($b->booking_date)->format('M d, Y') }}</td>
                                <td class="p-4 text-white font-bold">{{ $b->court->name ?? 'N/A' }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($b->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($b->end_time)->format('g:i A') }}</td>
                                <td class="p-4 font-bold text-white">₱{{ number_format($b->total_price, 2) }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold 
                                        {{ $b->payment_status === 'Verified' ? 'bg-emerald-500/20 text-emerald-400' : ($b->payment_status === 'Rejected' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400') }}">
                                        {{ $b->payment_status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-1">
                                    <button @click="selectedBooking = {{ json_encode($b->load(['customer', 'court'])) }}" class="bg-slate-800 hover:bg-slate-700 text-white text-[11px] px-2.5 py-1.5 rounded-lg border border-slate-700">View</button>
                                    <form method="POST" action="{{ route('admin.bookings.delete', $b->id) }}" class="inline" onsubmit="return confirm('Delete this booking?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-900/40 hover:bg-red-800/60 text-red-300 text-[11px] px-2.5 py-1.5 rounded-lg border border-red-700/50">Del</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-8 text-center text-slate-500">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 3: COURT MANAGEMENT -->
        <div x-show="activeTab === 'courts'" x-cloak>
            <div class="mb-6 flex justify-between items-center border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-xl font-bold text-white">Manage Courts</h2>
                    <p class="text-xs text-slate-400">Update rates, operational hours, or add/remove courts entirely.</p>
                </div>
                <button @click="showAddCourtModal = true" class="px-4 py-2 bg-lime-400 text-slate-950 text-sm font-bold rounded-xl hover:bg-lime-300 shadow-lg shadow-lime-400/20 flex items-center gap-2">
                    <span>+</span> Add New Court
                </button>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courts as $court)
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between relative overflow-hidden group">
                        
                        <form method="POST" action="{{ route('admin.courts.destroy', $court->id) }}" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition" onsubmit="return confirm('WARNING: Are you sure you want to completely delete {{ $court->name }}? This action cannot be undone.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-900/80 text-red-300 text-[10px] px-2 py-1 rounded border border-red-700/50 hover:bg-red-600 hover:text-white">Delete Court</button>
                        </form>

                        <form method="POST" action="{{ route('admin.courts.update', $court->id) }}" class="space-y-4 mt-4">
                            @csrf @method('PUT')
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">🏓 {{ $court->name }}</h3>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 font-semibold mb-1">Status</label>
                                <select name="status" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-xs outline-none">
                                    <option value="active" {{ $court->status === 'active' ? 'selected' : '' }}>🟢 Active</option>
                                    <option value="maintenance" {{ $court->status === 'maintenance' ? 'selected' : '' }}>🛠 Maintenance</option>
                                    <option value="disabled" {{ $court->status === 'disabled' ? 'selected' : '' }}>🔴 Disabled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 font-semibold mb-1">Classification</label>
                                <select name="classification" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-xs outline-none">
                                    <option value="Indoor" {{ $court->classification === 'Indoor' ? 'selected' : '' }}>Indoor</option>
                                    <option value="Outdoor" {{ $court->classification === 'Outdoor' ? 'selected' : '' }}>Outdoor</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-slate-400 font-semibold mb-1">Open Time</label>
                                    <input type="time" name="operating_hours_start" value="{{ $court->operating_hours_start }}" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-xs outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-400 font-semibold mb-1">Close Time</label>
                                    <input type="time" name="operating_hours_end" value="{{ $court->operating_hours_end }}" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-xs outline-none">
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl border border-slate-700 transition">Update Settings</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

    </main>

    <!-- MODAL: WALK-IN BOOKING -->
    <div x-show="showWalkInModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div @click.away="showWalkInModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md flex flex-col overflow-hidden shadow-2xl relative p-6">
            <button @click="showWalkInModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white">✕</button>
            <h2 class="text-xl font-black text-white mb-2">Walk-In Booking</h2>
            <div class="text-xs text-slate-400 mb-6 flex gap-2">
                <span class="bg-slate-800 px-2 py-1 rounded" x-text="'Date: ' + selectedDate"></span>
                <span class="bg-slate-800 px-2 py-1 rounded" x-text="'Time: ' + formatTime(walkInSlot)"></span>
            </div>
            
            <form method="POST" action="{{ route('admin.bookings.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="booking_date" :value="selectedDate">
                <input type="hidden" name="court_id" :value="selectedCourtId">
                <input type="hidden" name="start_time" :value="walkInSlot">
                <input type="hidden" name="end_time" :value="walkInSlot ? (parseInt(walkInSlot.split(':')[0]) + 1).toString().padStart(2, '0') + ':00' : ''">
                <input type="hidden" name="booking_status" value="Confirmed">
                
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Customer Name</label>
                    <input type="text" name="customer_name" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400" placeholder="Walk-in Customer Name">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Contact Number (Optional)</label>
                    <input type="text" name="contact_number" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400" placeholder="e.g. 09123456789">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 font-semibold mb-1">Amount Paid (₱)</label>
                        <input type="number" step="0.01" name="total_price" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400" placeholder="150.00">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 font-semibold mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400">
                            <option value="Verified">Paid (Verified)</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end gap-3 mt-4">
                    <button type="button" @click="showWalkInModal = false" class="px-4 py-2 text-slate-400 text-sm font-bold hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-lime-400 text-slate-950 text-sm font-bold rounded-xl hover:bg-lime-300">Confirm Walk-In</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD NEW COURT -->
    <div x-show="showAddCourtModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div @click.away="showAddCourtModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md flex flex-col overflow-hidden shadow-2xl relative p-6">
            <button @click="showAddCourtModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white">✕</button>
            <h2 class="text-xl font-black text-white mb-6">Add New Court</h2>
            
            <form method="POST" action="{{ route('admin.courts.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Court Name (e.g., Court 4)</label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Initial Status</label>
                    <select name="status" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400">
                        <option value="active">🟢 Active</option>
                        <option value="maintenance">🛠 Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Classification</label>
                    <select name="classification" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400">
                        <option value="Indoor">Indoor</option>
                        <option value="Outdoor">Outdoor</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 font-semibold mb-1">Open Time</label>
                        <input type="time" name="operating_hours_start" value="05:00" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 font-semibold mb-1">Close Time</label>
                        <input type="time" name="operating_hours_end" value="23:00" required class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-white text-sm outline-none focus:border-lime-400">
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-800 flex justify-end gap-3 mt-4">
                    <button type="button" @click="showAddCourtModal = false" class="px-4 py-2 text-slate-400 text-sm font-bold hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-lime-400 text-slate-950 text-sm font-bold rounded-xl hover:bg-lime-300">Create Court</button>
                </div>
            </form>
        </div>
    </div>

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
                            <div class="flex justify-between text-sm"><span class="text-slate-400">Players</span> <span class="font-bold text-white" x-text="selectedBooking?.number_of_players || 4"></span></div>
                        </div>
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                            <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Payment Details</h3>
                            <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Method</span> <span class="font-bold text-white" x-text="selectedBooking?.payment_method || 'Cash / Walk-in'"></span></div>
                            <div class="flex justify-between text-sm mb-2"><span class="text-slate-400">Total Due</span> <span class="font-black text-lime-400" x-text="'₱' + selectedBooking?.total_price"></span></div>
                            
                            <template x-if="selectedBooking?.payment_status === 'Verified'"><span class="inline-block px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">VERIFIED</span></template>
                            <template x-if="selectedBooking?.payment_status === 'Rejected'">
                                <div>
                                    <span class="inline-block px-2.5 py-1 rounded bg-red-500/20 text-red-400 text-[10px] font-bold mb-1">REJECTED</span>
                                    <div class="text-[11px] text-red-400" x-text="'Reason: ' + selectedBooking?.rejection_reason"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-slate-950 border border-blue-800 rounded-xl p-4 h-full flex flex-col justify-center text-center">
                            <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Verification Required</h3>
                            <div class="text-4xl mb-3">💬</div>
                            <p class="text-sm font-bold text-white mb-1">Check Facebook Inbox</p>
                            <p class="text-xs text-slate-400">Match the customer's name and reference number with the screenshot sent to the page.</p>
                        </div>
                    </div>
                </div>

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
                            <button @click="showRejectModal = true" type="button" class="px-5 py-2 bg-red-900/40 text-red-400 text-sm font-bold rounded-xl hover:bg-red-900/60 border border-red-900/50">✕ Reject</button>
                        </template>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-800" x-show="showRejectModal" x-cloak>
                    <form :action="'/admin/bookings/' + selectedBooking?.id + '/reject'" method="POST" class="flex flex-col gap-3">
                        @csrf @method('PATCH')
                        <label class="text-sm font-bold text-red-400">Reason for rejection:</label>
                        <textarea name="rejection_reason" required rows="2" class="w-full rounded-xl border border-red-900 bg-slate-950 px-4 py-3 text-white outline-none focus:border-red-500"></textarea>
                        <div class="flex justify-end gap-3 mt-2">
                            <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-slate-400 text-sm font-bold">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-500 shadow-lg shadow-red-600/20">Confirm Rejection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ALPINE COMPONENT -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminDashboard', (allBookings, allCourts) => ({
                activeTab: 'schedule',
                selectedBooking: null,
                showRejectModal: false,
                showAddCourtModal: false,
                showWalkInModal: false,
                walkInSlot: null,

                bookings: allBookings,
                courts: allCourts,

                // Schedule Variables
                selectedDate: new Date().toISOString().split('T')[0],
                selectedCourtId: allCourts.length > 0 ? String(allCourts[0].id) : '1',
                get allSlots() {
    const court = this.courts.find(c => String(c.id) === String(this.selectedCourtId)); // Use this.courtId for public booking view
    if (!court) return [];

    let startHour = parseInt((court.operating_hours_start || '00:00').substring(0, 2), 10);
    let endHour = parseInt((court.operating_hours_end || '00:00').substring(0, 2), 10);

    // 1. Handle 24-Hour Open (00:00 to 00:00)
    if (startHour === 0 && endHour === 0 && (court.operating_hours_start === court.operating_hours_end)) {
        let slots = [];
        for (let i = 0; i < 24; i++) {
            slots.push(String(i).padStart(2, '0') + ':00');
        }
        return slots;
    }

    // 2. Handle Overnight Schedules (e.g., 5:00 AM to 2:00 AM next day)
    if (endHour <= startHour) {
        endHour += 24;
    }

    // 3. Generate Slots
    let slots = [];
    for (let i = startHour; i <= endHour; i++) {
        let displayHour = i % 24;
        let hourString = String(displayHour).padStart(2, '0');
        slots.push(hourString + ':00');
    }

    return slots;
},

                // History Filters
                historyFilterDate: '',
                historyFilterCourt: '',

                // Revenue Filters
                revenueFilterDate: '',
                revenueFilterCourt: '',

                showHistoryRow(rowDate, rowCourtId) {
                    const matchDate = this.historyFilterDate === '' || rowDate === this.historyFilterDate;
                    const matchCourt = this.historyFilterCourt === '' || String(rowCourtId) === String(this.historyFilterCourt);
                    return matchDate && matchCourt;
                },

                get calculatedRevenue() {
                    let rev = 0;
                    this.bookings.forEach(b => {
                        const matchDate = this.revenueFilterDate === '' || b.booking_date === this.revenueFilterDate;
                        const matchCourt = this.revenueFilterCourt === '' || String(b.court_id) === String(this.revenueFilterCourt);
                        
                        if (matchDate && matchCourt && b.payment_status === 'Verified') {
                            rev += parseFloat(b.total_price || 0);
                        }
                    });
                    return '₱' + rev.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                changeDate(offsetDays) {
                    let current = new Date(this.selectedDate);
                    current.setDate(current.getDate() + offsetDays);
                    this.selectedDate = current.toISOString().split('T')[0];
                },
                setToday() { this.selectedDate = new Date().toISOString().split('T')[0]; },
                
                get activeBookings() {
                    return this.bookings.filter(b => String(b.court_id) === String(this.selectedCourtId) && b.booking_date === this.selectedDate && b.booking_status !== 'Cancelled' && b.booking_status !== 'Rejected');
                },
                getBookingForSlot(slotTime) {
                    return this.activeBookings.find(b => slotTime >= (b.start_time || '').substring(0, 5) && slotTime < (b.end_time || '').substring(0, 5));
                },
                getSlotStatusLabel(slotTime) {
                    const booking = this.getBookingForSlot(slotTime);
                    return booking ? (booking.booking_status || 'Booked') : 'Available';
                },
                getSlotStyle(slotTime) {
                    const booking = this.getBookingForSlot(slotTime);
                    if (!booking) return 'bg-slate-800 border-slate-700 text-slate-400 hover:border-slate-500 cursor-pointer';
                    const status = (booking.booking_status || '').toLowerCase();
                    if (status === 'confirmed') return 'bg-emerald-500/20 border-emerald-500/40 text-emerald-300 hover:border-emerald-400 cursor-pointer';
                    if (status.includes('pending') || status.includes('verification')) return 'bg-amber-500/20 border-amber-500/50 text-amber-300 animate-pulse hover:border-amber-400 cursor-pointer';
                    return 'bg-red-500/10 border-red-500/30 text-red-400 hover:border-red-400 cursor-pointer';
                },
                getBadgeStyle(slotTime) {
                    const booking = this.getBookingForSlot(slotTime);
                    if (!booking) return 'bg-slate-700 text-slate-400';
                    return (booking.booking_status || '').toLowerCase() === 'confirmed' ? 'bg-emerald-500/30 text-emerald-300' : 'bg-amber-500/30 text-amber-300';
                },
                handleSlotClick(slotTime) {
                    const booking = this.getBookingForSlot(slotTime);
                    if (booking) {
                        this.selectedBooking = booking;
                    } else {
                        this.walkInSlot = slotTime;
                        this.showWalkInModal = true;
                    }
                },
                formatTime(time) {
                    if (!time) return '';
                    const [hour, minute] = time.split(':').map(Number);
                    return `${hour % 12 || 12}:${String(minute).padStart(2, '0')} ${hour >= 12 && hour < 24 ? 'PM' : 'AM'}`;
                }
            }));
        });
    </script>
</body>
</html>