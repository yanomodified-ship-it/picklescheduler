<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Submitted | HomeCourt PickleHouse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-2">
                <span class="text-2xl sm:text-3xl">🏓</span>
                <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-lime-400">
                    HomeCourt<span class="text-white">PickleHouse</span>
                </span>
            </a>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="flex-grow flex flex-col items-center justify-center p-4 sm:p-6 w-full">
        
        @if (session('success'))
            <div class="mb-6 w-full max-w-2xl rounded-xl border border-lime-500/30 bg-lime-900/20 p-4 text-sm font-bold text-lime-300 text-center">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-12 text-center shadow-2xl mt-4 sm:mt-8">
            
            <!-- Checkmark Icon -->
            <div class="mx-auto flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-lime-500/20 text-lime-400 mb-6 border border-lime-500/30">
                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>

            <!-- Success Message -->
            <h1 class="text-2xl sm:text-3xl font-black text-white mb-3">Booking Request Submitted!</h1>
            <p class="text-sm sm:text-base text-slate-300 mb-8 leading-relaxed">
                Thank you for choosing PickleHouse. <br class="hidden sm:block">
            </p>

            <!-- Booking Reference Box -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 sm:p-6 mb-8 shadow-inner">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2">Your Booking Reference</p>
                <p class="text-2xl sm:text-3xl font-black text-lime-400 tracking-widest mb-2 sm:mb-3">{{ $booking->booking_reference }}</p>
                <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Please screenshot this code for your records.</p>
            </div>

            <!-- Booking Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 text-left bg-slate-950 border border-slate-800 rounded-2xl p-5 sm:p-6 mb-8">
                <div>
                    <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500 mb-1">Customer</p>
                    <p class="text-sm font-bold text-white break-words">{{ $booking->customer->name ?? $booking->customer->full_name ?? 'Guest Customer' }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500 mb-1">Court</p>
                    <p class="text-sm font-bold text-white">{{ $booking->court->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500 mb-1">Date & Time</p>
                    <p class="text-sm font-bold text-white">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }} <br>
                        <span class="text-slate-400 text-xs font-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</span>
                    </p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500 mb-1">Total</p>
                    <p class="text-sm font-black text-lime-400">
                        ₱{{ number_format((float) ($booking->total_price ?? $booking->total_amount ?? 0), 2) }}
                    </p>
                </div>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 sm:p-6 mb-6 text-left shadow-inner">
    <h3 class="text-xs font-bold uppercase tracking-widest text-lime-400 mb-3">Payment Details</h3>
    
    @if(($booking->payment_method ?? '') === 'Bank')
        <div class="mb-4 pb-4 border-b border-slate-800">
            <p class="text-xs text-slate-400">Send payment via Bank Transfer:</p>
            <p class="text-lg sm:text-xl font-black text-lime-400 mt-1">BDO: 001234567890</p>
            <p class="text-xs font-semibold text-slate-300">Account Name: HomeCourt PickleHouse</p>
        </div>
    @else
        <div class="mb-4 pb-4 border-b border-slate-800">
            <p class="text-xs text-slate-400">Send payment via GCash:</p>
            <p class="text-lg sm:text-xl font-black text-lime-400 mt-1">0912 345 6789</p>
            <p class="text-xs font-semibold text-slate-300">Account Name: HomeCourt PickleHouse</p>
        </div>
    @endif

    <div class="space-y-3">
        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
            Please send your payment screenshot and reservation infor to our facebook page to verify your booking.
        </p>

        <a href="https://facebook.com/yourpage" target="_blank" class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm rounded-xl transition">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Click here to redirect to our facebook page
        </a>
    </div>
</div>

            <!-- Return Home Button -->
            <a href="/" class="inline-flex w-full sm:w-auto items-center justify-center px-8 py-3.5 bg-lime-400 text-slate-950 font-bold rounded-xl hover:bg-lime-300 transition shadow-lg shadow-lime-400/20">
                Return to Homepage
            </a>
        </div>
    </main>

</body>
</html>