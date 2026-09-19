<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | HomeCourt PickleHouse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
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

    <!-- AJAX FLASH TOAST -->
    <div x-show="flashMessage" x-cloak x-transition
         class="fixed top-4 right-4 z-[60] max-w-sm rounded-2xl border p-4 text-sm font-bold shadow-2xl"
         :class="flashType === 'error' ? 'border-red-500/30 bg-red-900/90 text-red-200' : 'border-lime-500/30 bg-lime-900/90 text-lime-200'"
         x-text="flashMessage">
    </div>

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

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <template x-for="slot in allSlots" :key="slot">
                    <button @click="handleSlotClick(slot)" :class="getSlotStyle(slot)" class="p-3 rounded-2xl border text-center transition flex flex-col items-center justify-between min-h-[105px] relative overflow-hidden group">
                        <div class="text-xs font-bold text-slate-300" x-text="formatTime(slot)"></div>
                        <div class="my-1.5 w-full text-center">
                            <template x-if="getBookingForSlot(slot)">
                                <div class="flex flex-col items-center">
                                    <span class="text-[11px] font-black tracking-wider text-lime-400 truncate max-w-full" x-text="getBookingForSlot(slot).booking_reference"></span>
                                    <span class="text-[10px] font-medium text-slate-300 truncate max-w-full" x-text="getBookingForSlot(slot).customer?.full_name"></span>
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
                <button type="button" @click="showDeleteAllModal = true" class="ml-auto px-3 py-1.5 text-xs font-bold text-red-300 hover:text-white bg-red-900/40 hover:bg-red-800/60 rounded-lg border border-red-700/50">🗑 Delete All Bookings</button>
            </div>

            <!-- BULK VERIFICATION PANEL: customers with more than one pending booking -->
            <template x-if="pendingByCustomer.length > 0">
                <div class="p-4 border-b border-slate-800 bg-amber-500/5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-3">Multiple Pending Bookings — Verify Together</h4>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="group in pendingByCustomer" :key="group.customer_id">
                            <div class="bg-slate-950 border border-amber-500/30 rounded-xl p-3 flex flex-col gap-2">
                                <div>
                                    <div class="text-sm font-bold text-white" x-text="group.customer_name"></div>
                                    <div class="text-[11px] text-slate-400" x-text="group.bookings.length + ' pending bookings'"></div>
                                    <div class="text-[11px] text-lime-400 font-semibold" x-text="group.courts.join(', ')"></div>
                                    <div class="text-[11px] text-white font-bold mt-0.5" x-text="'Total: ₱' + group.totalPending.toFixed(2)"></div>
                                </div>
                                <button @click="selectedGroupId = group.customer_id" class="w-full bg-slate-800 hover:bg-slate-700 text-white text-[11px] font-bold py-1.5 rounded-lg border border-slate-700">Review & Verify</button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

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
                        <template x-if="filteredHistory.length === 0">
                            <tr><td colspan="8" class="p-8 text-center text-slate-500">No bookings found.</td></tr>
                        </template>
                        <template x-for="b in filteredHistory" :key="b.id">
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="p-4 font-extrabold text-lime-400" x-text="b.booking_reference"></td>
                                <td class="p-4 text-white font-bold" x-text="b.customer?.full_name || 'N/A'"></td>
                                <td class="p-4" x-text="formatDate(b.booking_date)"></td>
                                <td class="p-4 text-white font-bold" x-text="b.court?.name || 'N/A'"></td>
                                <td class="p-4" x-text="formatTime((b.start_time || '').substring(0,5)) + ' - ' + formatTime((b.end_time || '').substring(0,5))"></td>
                                <td class="p-4 font-bold text-white" x-text="'₱' + parseFloat(b.total_price || 0).toFixed(2)"></td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold"
                                          :class="b.payment_status === 'Verified' ? 'bg-emerald-500/20 text-emerald-400' : (b.payment_status === 'Rejected' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400')"
                                          x-text="b.payment_status"></span>
                                </td>
                                <td class="p-4 text-right space-x-1">
                                    <button @click="selectedBooking = b" class="bg-slate-800 hover:bg-slate-700 text-white text-[11px] px-2.5 py-1.5 rounded-lg border border-slate-700">View</button>
                                    <button @click="deleteBookingAction(b.id)" class="bg-red-900/40 hover:bg-red-800/60 text-red-300 text-[11px] px-2.5 py-1.5 rounded-lg border border-red-700/50">Del</button>
                                </td>
                            </tr>
                        </template>
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
        <div @click.away="selectedBooking = null; showRejectModal = false;" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl relative">
            <button @click="selectedBooking = null; showRejectModal = false;" class="absolute top-5 right-5 text-slate-400 hover:text-white">✕</button>
            <div class="p-6 sm:p-8 overflow-y-auto" id="printable-receipt">
                <div class="text-xs font-bold uppercase tracking-widest text-lime-400">Review Booking</div>
                <h2 class="text-2xl font-black text-white mt-1 mb-6" x-text="selectedBooking?.booking_reference"></h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                        <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Customer Info</h3>
                        <div class="font-bold text-white" x-text="selectedBooking?.customer?.full_name || selectedBooking?.customer_name || 'Walk-In Customer'"></div>
                        <div class="text-slate-400 text-sm" x-text="selectedBooking?.customer?.contact_number || selectedBooking?.contact_number || 'No contact provided'"></div>
                    </div>
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                        <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Booking Info</h3>
                        <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Court</span> <span class="font-bold text-white" x-text="selectedBooking?.court?.name"></span></div>
                        <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Date</span> <span class="font-bold text-white" x-text="selectedBooking?.booking_date"></span></div>
                        <div class="flex justify-between text-sm mb-1"><span class="text-slate-400">Time</span> <span class="font-bold text-white" x-text="selectedBooking?.start_time + ' - ' + selectedBooking?.end_time"></span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-400">Players</span> <span class="font-bold text-white" x-text="selectedBooking?.number_of_players || 4"></span></div>
                    </div>
                </div>

                <div class="mt-4 bg-slate-950 border border-slate-800 rounded-xl p-4">
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

                <div class="mt-8 pt-6 border-t border-slate-800 flex flex-wrap gap-3" x-show="!showRejectModal" data-html2canvas-ignore>
                    <button type="button" onclick="downloadInvoice()" class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-700 transition">📄 Download PDF</button>
                    <div class="ml-auto flex gap-3">
                        <template x-if="selectedBooking?.payment_status !== 'Verified'">
                            <button type="button" @click="approveBooking(selectedBooking.id)" class="px-5 py-2 bg-lime-400 text-slate-950 text-sm font-bold rounded-xl hover:bg-lime-300 shadow-lg shadow-lime-400/20">✓ Approve Payment</button>
                        </template>
                        <template x-if="selectedBooking?.payment_status !== 'Rejected'">
                            <button @click="showRejectModal = true" type="button" class="px-5 py-2 bg-red-900/40 text-red-400 text-sm font-bold rounded-xl hover:bg-red-900/60 border border-red-900/50">✕ Reject</button>
                        </template>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-800" x-show="showRejectModal" x-cloak data-html2canvas-ignore x-data="{ rejectReason: '' }">
                    <div class="flex flex-col gap-3">
                        <label class="text-sm font-bold text-red-400">Reason for rejection:</label>
                        <textarea x-model="rejectReason" rows="2" class="w-full rounded-xl border border-red-900 bg-slate-950 px-4 py-3 text-white outline-none focus:border-red-500"></textarea>
                        <div class="flex justify-end gap-3 mt-2">
                            <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-slate-400 text-sm font-bold">Cancel</button>
                            <button type="button"
                                    @click="if (rejectReason.trim()) { rejectBooking(selectedBooking.id, rejectReason); rejectReason = ''; } else { alert('Please enter a reason.'); }"
                                    class="px-5 py-2 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-500 shadow-lg shadow-red-600/20">Confirm Rejection</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: BULK VERIFY/REJECT FOR CUSTOMER -->
    <div x-show="selectedGroup !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div @click.away="selectedGroupId = null; showGroupRejectForm = false;" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl relative">
            <button @click="selectedGroupId = null; showGroupRejectForm = false;" class="absolute top-5 right-5 text-slate-400 hover:text-white">✕</button>
            <div class="p-6 sm:p-8 overflow-y-auto">
                <div class="text-xs font-bold uppercase tracking-widest text-amber-400">Multiple Pending Bookings</div>
                <h2 class="text-2xl font-black text-white mt-1 mb-1" x-text="selectedGroup?.customer_name"></h2>
                <p class="text-xs text-slate-400 mb-1" x-text="(selectedGroup?.bookings.length || 0) + ' pending booking(s) — ' + (selectedGroup?.courts || []).join(', ')"></p>
                <p class="text-xs text-lime-400 font-bold mb-6" x-text="'Total: ₱' + (selectedGroup?.totalPending || 0).toFixed(2)"></p>

                <div class="bg-slate-950 border border-slate-800 rounded-xl divide-y divide-slate-800 mb-6 max-h-64 overflow-y-auto">
                    <template x-for="b in (selectedGroup?.bookings || [])" :key="b.id">
                        <div class="p-3 flex justify-between items-center text-sm">
                            <div>
                                <div class="font-bold text-lime-400 text-xs" x-text="b.booking_reference"></div>
                                <div class="text-slate-300 text-xs" x-text="(b.court?.name || 'N/A') + ' • ' + formatDate(b.booking_date) + ' • ' + formatTime((b.start_time||'').substring(0,5)) + ' - ' + formatTime((b.end_time||'').substring(0,5))"></div>
                            </div>
                            <div class="font-bold text-white text-xs" x-text="'₱' + parseFloat(b.total_price || 0).toFixed(2)"></div>
                        </div>
                    </template>
                </div>

                <div class="flex flex-wrap gap-3" x-show="!showGroupRejectForm">
                    <button type="button" @click="verifyAllForCustomer(selectedGroup.customer_id); selectedGroupId = null;" class="px-5 py-2 bg-lime-400 text-slate-950 text-sm font-bold rounded-xl hover:bg-lime-300 shadow-lg shadow-lime-400/20">✓ Verify All</button>
                    <button type="button" @click="showGroupRejectForm = true" class="px-5 py-2 bg-red-900/40 text-red-400 text-sm font-bold rounded-xl hover:bg-red-900/60 border border-red-900/50">✕ Reject All</button>
                </div>

                <div class="mt-2" x-show="showGroupRejectForm" x-cloak x-data="{ groupRejectReason: '' }">
                    <div class="flex flex-col gap-3">
                        <label class="text-sm font-bold text-red-400">Reason for rejecting all these bookings:</label>
                        <textarea x-model="groupRejectReason" rows="2" class="w-full rounded-xl border border-red-900 bg-slate-950 px-4 py-3 text-white outline-none focus:border-red-500"></textarea>
                        <div class="flex justify-end gap-3 mt-2">
                            <button type="button" @click="showGroupRejectForm = false" class="px-4 py-2 text-slate-400 text-sm font-bold">Cancel</button>
                            <button type="button"
                                    @click="if (groupRejectReason.trim()) { rejectAllForCustomer(selectedGroup.customer_id, groupRejectReason); showGroupRejectForm = false; selectedGroupId = null; } else { alert('Please enter a reason.'); }"
                                    class="px-5 py-2 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-500 shadow-lg shadow-red-600/20">Confirm Rejection</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: DELETE ALL BOOKINGS -->
    <div x-show="showDeleteAllModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div @click.away="showDeleteAllModal = false; deleteAllConfirmText = ''" class="bg-slate-900 border border-red-800 rounded-3xl w-full max-w-md flex flex-col overflow-hidden shadow-2xl relative p-6">
            <button @click="showDeleteAllModal = false; deleteAllConfirmText = ''" class="absolute top-5 right-5 text-slate-400 hover:text-white">✕</button>
            <h2 class="text-xl font-black text-red-400 mb-2">⚠ Delete ALL Bookings</h2>
            <p class="text-sm text-slate-300 mb-4">
                This permanently deletes <span class="font-bold text-white" x-text="bookings.length"></span> booking(s) — every booking in the system, regardless of any filter currently applied. This cannot be undone.
            </p>
            <label class="block text-xs text-slate-400 font-semibold mb-1">Type <span class="font-mono text-red-400">DELETE ALL</span> to confirm</label>
            <input type="text" x-model="deleteAllConfirmText" class="w-full rounded-xl border border-red-900 bg-slate-950 px-3 py-2 text-white text-sm outline-none focus:border-red-500 mb-4" placeholder="DELETE ALL">
            <div class="flex justify-end gap-3">
                <button type="button" @click="showDeleteAllModal = false; deleteAllConfirmText = ''" class="px-4 py-2 text-slate-400 text-sm font-bold hover:text-white">Cancel</button>
                <button type="button"
                        :disabled="deleteAllConfirmText !== 'DELETE ALL'"
                        :class="deleteAllConfirmText === 'DELETE ALL' ? 'bg-red-600 hover:bg-red-500 cursor-pointer' : 'bg-red-900/40 cursor-not-allowed opacity-50'"
                        @click="deleteAllBookingsAction()"
                        class="px-5 py-2 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-600/20 transition">Delete Everything</button>
            </div>
        </div>
    </div>

    <!-- ALPINE COMPONENT -->
    <script>
        function loadHtml2Pdf() {
            return new Promise((resolve, reject) => {
                if (window.html2pdf) { resolve(); return; }
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Could not load the PDF library.'));
                document.head.appendChild(script);
            });
        }

        async function downloadInvoice() {
            try {
                await loadHtml2Pdf();
            } catch (e) {
                alert('Could not load the PDF library. Check your internet connection and try again.');
                return;
            }

            const receiptElement = document.getElementById('printable-receipt');
            const refName = receiptElement.querySelector('h2')?.innerText || 'Invoice';

            const opt = {
                margin:       0.5,
                filename:     `Invoice_${refName.trim()}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#020617'
                },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(receiptElement).save();
        }

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        async function apiPatch(url, body = {}) {
            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(body),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                throw new Error(data.message || 'Something went wrong. Please try again.');
            }

            return data;
        }

        async function apiDelete(url) {
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                throw new Error(data.message || 'Something went wrong. Please try again.');
            }

            return data;
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('adminDashboard', (allBookings, allCourts) => ({
                activeTab: 'schedule',
                selectedBooking: null,
                selectedGroupId: null,
                showRejectModal: false,
                showGroupRejectForm: false,
                showAddCourtModal: false,
                showWalkInModal: false,
                showDeleteAllModal: false,
                deleteAllConfirmText: '',
                walkInSlot: null,

                bookings: allBookings,
                courts: allCourts,

                flashMessage: '',
                flashType: 'success',
                showFlash(message, type = 'success') {
                    this.flashMessage = message;
                    this.flashType = type;
                    setTimeout(() => { this.flashMessage = ''; }, 4000);
                },

                selectedDate: new Date().toISOString().split('T')[0],
                selectedCourtId: allCourts.length > 0 ? String(allCourts[0].id) : '1',
                get allSlots() {
                    const court = this.courts.find(c => String(c.id) === String(this.selectedCourtId));
                    if (!court) return [];

                    let startHour = parseInt((court.operating_hours_start || '00:00').substring(0, 2), 10);
                    let endHour = parseInt((court.operating_hours_end || '00:00').substring(0, 2), 10);

                    if (startHour === 0 && endHour === 0 && (court.operating_hours_start === court.operating_hours_end)) {
                        let slots = [];
                        for (let i = 0; i < 24; i++) {
                            slots.push(String(i).padStart(2, '0') + ':00');
                        }
                        return slots;
                    }

                    if (endHour <= startHour) {
                        endHour += 24;
                    }

                    let slots = [];
                    for (let i = startHour; i <= endHour; i++) {
                        let displayHour = i % 24;
                        let hourString = String(displayHour).padStart(2, '0');
                        slots.push(hourString + ':00');
                    }

                    return slots;
                },

                historyFilterDate: '',
                historyFilterCourt: '',
                revenueFilterDate: '',
                revenueFilterCourt: '',

                showHistoryRow(rowDate, rowCourtId) {
                    const matchDate = this.historyFilterDate === '' || rowDate === this.historyFilterDate;
                    const matchCourt = this.historyFilterCourt === '' || String(rowCourtId) === String(this.historyFilterCourt);
                    return matchDate && matchCourt;
                },

                get filteredHistory() {
                    return this.bookings.filter(b => this.showHistoryRow(b.booking_date, b.court_id));
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr + 'T00:00:00');
                    return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
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
                },

                mergeBooking(updated) {
                    const idx = this.bookings.findIndex(b => b.id === updated.id);
                    if (idx !== -1) {
                        this.bookings[idx] = updated;
                    } else {
                        this.bookings.unshift(updated);
                    }
                    if (this.selectedBooking && this.selectedBooking.id === updated.id) {
                        this.selectedBooking = updated;
                    }
                },

                removeBooking(id) {
                    this.bookings = this.bookings.filter(b => b.id !== id);
                    if (this.selectedBooking && this.selectedBooking.id === id) {
                        this.selectedBooking = null;
                    }
                },

                async approveBooking(id) {
                    try {
                        const data = await apiPatch(`/admin/bookings/${id}/approve`);
                        this.mergeBooking(data.booking);
                        this.showFlash(data.message);
                    } catch (e) {
                        this.showFlash(e.message, 'error');
                    }
                },

                async rejectBooking(id, reason) {
                    try {
                        const data = await apiPatch(`/admin/bookings/${id}/reject`, { rejection_reason: reason });
                        this.mergeBooking(data.booking);
                        this.showRejectModal = false;
                        this.showFlash(data.message);
                    } catch (e) {
                        this.showFlash(e.message, 'error');
                    }
                },

                async deleteBookingAction(id) {
                    if (!confirm('Delete this booking?')) return;
                    try {
                        const data = await apiDelete(`/admin/bookings/${id}`);
                        this.removeBooking(id);
                        this.showFlash(data.message);
                    } catch (e) {
                        this.showFlash(e.message, 'error');
                    }
                },

                // Live-computed groups of customers with more than one pending
                // booking. This is a getter, not a stored snapshot, so it can
                // never go stale — it re-reads `this.bookings` fresh every
                // time it's accessed.
                get pendingByCustomer() {
                    const groups = {};
                    this.bookings.forEach(b => {
                        if (b.booking_status !== 'Pending Verification') return;
                        const key = b.customer_id;
                        if (!groups[key]) {
                            groups[key] = {
                                customer_id: key,
                                customer_name: b.customer?.full_name || 'Unknown Customer',
                                bookings: [],
                                courts: [],
                                totalPending: 0,
                            };
                        }
                        groups[key].bookings.push(b);
                        groups[key].totalPending += parseFloat(b.total_price || 0);
                        const courtName = b.court?.name;
                        if (courtName && !groups[key].courts.includes(courtName)) {
                            groups[key].courts.push(courtName);
                        }
                    });
                    return Object.values(groups).filter(g => g.bookings.length > 1);
                },

                // The bulk modal is now DERIVED from pendingByCustomer rather
                // than a stored snapshot, so it can't show stale data no
                // matter when/how a booking changes elsewhere on the page.
                get selectedGroup() {
                    if (this.selectedGroupId === null) return null;
                    return this.pendingByCustomer.find(g => g.customer_id === this.selectedGroupId) || null;
                },

                async verifyAllForCustomer(customerId) {
                    try {
                        const data = await apiPatch(`/admin/customers/${customerId}/verify-all`);
                        data.bookings.forEach(b => this.mergeBooking(b));
                        this.showFlash(data.message);
                    } catch (e) {
                        this.showFlash(e.message, 'error');
                    }
                },

                async rejectAllForCustomer(customerId, reason) {
                    try {
                        const data = await apiPatch(`/admin/customers/${customerId}/reject-all`, { rejection_reason: reason });
                        data.bookings.forEach(b => this.mergeBooking(b));
                        this.showFlash(data.message);
                    } catch (e) {
                        this.showFlash(e.message, 'error');
                    }
                },

                async deleteAllBookingsAction() {
                    if (this.deleteAllConfirmText !== 'DELETE ALL') return;
                    try {
                        const data = await apiDelete(`/admin/bookings`);
                        this.bookings = [];
                        this.selectedBooking = null;
                        this.selectedGroupId = null;
                        this.showDeleteAllModal = false;
                        this.deleteAllConfirmText = '';
                        this.showFlash(data.message);
                    } catch (e) {
                        this.showFlash(e.message, 'error');
                    }
                },
            }));
        });
    </script>
</body>
</html>