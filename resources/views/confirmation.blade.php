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
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=1">
</head>
<body class="flex min-h-screen flex-col bg-[#F6F8EE] text-[#1B2B1C] antialiased">

    {{-- Shared pickleball graphic, reused with <use>. --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="hc-ball" viewBox="0 0 200 200">
            <path fill="currentColor" fill-rule="evenodd" d="M4.00 100.00a96 96 0 1 0 192 0a96 96 0 1 0 -192 0zM117.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM105.80 120.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM81.80 120.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM69.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM81.80 79.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM105.80 79.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM144.03 113.46a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM130.57 136.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.26 150.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.34 150.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM57.03 136.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM43.57 113.46a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM43.57 86.54a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM57.03 63.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.34 49.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.26 49.77a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM130.57 63.23a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM144.03 86.54a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM172.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM168.04 127.02a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM154.32 150.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM133.30 168.42a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.52 177.80a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.08 177.80a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM54.30 168.42a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM33.28 150.78a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM19.56 127.02a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM14.80 100.00a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM19.56 72.98a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM33.28 49.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM54.30 31.58a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM80.08 22.20a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM107.52 22.20a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM133.30 31.58a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM154.32 49.22a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0zM168.04 72.98a6.2 6.2 0 1 0 12.4 0a6.2 6.2 0 1 0 -12.4 0z"/>
        </symbol>
    </svg>

    {{-- Navigation Header (kept pure white: the logo file has a white background) --}}
    @php
        $hcNow  = now()->hour + now()->minute / 60;
        $hcOpen = ($hcNow >= 4 || $hcNow < 2);
    @endphp

    <header class="hch"
            x-data="{
                scrolled: false,
                progress: 0,
                update() {
                    this.scrolled = window.scrollY > 8;
                    const h = document.documentElement.scrollHeight - window.innerHeight;
                    this.progress = h > 0 ? Math.min(100, (window.scrollY / h) * 100) : 0;
                }
            }"
            x-init="update()"
            @scroll.window.passive="update()"
            @resize.window="update()">
        <style>
            .hch{position:sticky;top:0;z-index:50}
            .hch-top{overflow:hidden;max-height:44px;background:#1F3F24;color:#CFDDAE;transition:max-height .35s ease,opacity .25s ease}
            .hch-top.off{max-height:0;opacity:0}
            .hch-top-in{display:flex;align-items:center;justify-content:center;gap:12px;max-width:1232px;margin:0 auto;padding:8px 24px;font-size:12px;font-weight:700}
            @media (min-width:640px){.hch-top-in{justify-content:space-between}}
            .hch-status{display:inline-flex;align-items:center;gap:8px;color:#E4F03A}
            .hch-status i{width:8px;height:8px;border-radius:9999px;background:#E4F03A;animation:hch-pulse 2s ease-in-out infinite}
            .hch-status.closed{color:#CFDDAE}
            .hch-status.closed i{background:#9CA3AF;animation:none}
            .hch-top-links{display:none;align-items:center;gap:20px}
            @media (min-width:640px){.hch-top-links{display:flex}}
            .hch-top-links a{display:inline-flex;align-items:center;gap:6px;color:#F6F8EE;text-decoration:none;transition:color .2s}
            .hch-top-links a:hover{color:#E4F03A}

            .hch-wrap{padding:10px 12px 0}
            @media (min-width:768px){.hch-wrap{padding:12px 24px 0}}
            .hch-bar{position:relative;display:flex;align-items:center;justify-content:space-between;gap:16px;max-width:1232px;margin:0 auto;border-radius:9999px;background:#fff;border:1px solid rgba(27,43,28,.08);padding:8px 10px 8px 20px;box-shadow:0 10px 30px -18px rgba(27,43,28,.45);transition:box-shadow .3s ease,padding .25s ease}
            .hch-bar.sc{padding-top:5px;padding-bottom:5px;box-shadow:0 18px 40px -16px rgba(27,43,28,.6)}
            .hch-logo{display:flex;align-items:center}
            .hch-logo img{display:block;width:auto;height:44px;max-height:44px;transition:height .25s ease}
            .hch-bar.sc .hch-logo img{height:36px}

            .hch-right{display:flex;align-items:center;gap:4px}
            .hch-link{display:none;border-radius:9999px;padding:9px 16px;font-size:14px;font-weight:700;color:rgba(27,43,28,.78);text-decoration:none;transition:background .25s,color .25s}
            @media (min-width:640px){.hch-link{display:inline-block}}
            .hch-link:hover{background:#E5ECD2;color:#1F3F24}
            .hch-cta{display:inline-flex;align-items:center;gap:8px;margin-left:6px;border-radius:9999px;background:#E4F03A;padding:11px 20px;font-size:14px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s,color .2s}
            .hch-cta:hover{background:#1F3F24;color:#E4F03A}
            .hch-cta svg{transition:transform .25s}
            .hch-cta:hover svg{transform:translateX(4px)}

            .hch-prog{position:absolute;left:34px;right:34px;bottom:0;height:3px;border-radius:3px;overflow:hidden;background:rgba(31,63,36,.08)}
            .hch-prog > div{height:100%;width:0;background:linear-gradient(90deg,#2F5D34,#E4F03A);transition:width .12s linear}

            @keyframes hch-pulse{0%,100%{opacity:1}50%{opacity:.45}}
            @media (prefers-reduced-motion:reduce){
                .hch-status i{animation:none}
                .hch-top,.hch-bar,.hch-logo img{transition:none}
            }
        </style>

        {{-- Top strip --}}
        <div class="hch-top" :class="{ 'off': scrolled }">
            <div class="hch-top-in">
                @if ($hcOpen)
                    <span class="hch-status"><i></i>Open now · until 2:00 AM</span>
                @else
                    <span class="hch-status closed"><i></i>Closed now · opens at 4:00 AM</span>
                @endif

                <div class="hch-top-links">
                    <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Questions? Message us
                    </a>
                </div>
            </div>
        </div>

        {{-- Floating bar --}}
        <div class="hch-wrap">
            <div class="hch-bar" :class="{ 'sc': scrolled }">
                <a href="{{ route('home') }}" class="hch-logo" aria-label="HomeCourt PickleHouse home">
                    <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high">
                </a>

                <nav class="hch-right" aria-label="Main">
                    <a href="{{ route('home') }}" class="hch-link">Home</a>
                    <a href="{{ route('booking.create') }}" class="hch-cta">
                        Book again
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </nav>

                <div class="hch-prog" aria-hidden="true"><div :style="{ width: progress + '%' }"></div></div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        @php
            $method   = (($booking->payment_method ?? '') === 'Maya') ? 'maya' : 'gcash';
            $payTotal = (float) ($totalAmount ?? $booking->total_price ?? $booking->total_amount ?? 0);

            $allBookings = $bookings ?? collect([$booking]);
            $anyRejected = $allBookings->contains(fn ($b) => $b->payment_status === 'Rejected' || $b->booking_status === 'Rejected');
            $anyCancelled = $allBookings->contains(fn ($b) => $b->booking_status === 'Cancelled');
            $allVerified = $allBookings->every(fn ($b) => $b->payment_status === 'Verified');
            $state = $anyRejected ? 'rejected' : ($anyCancelled ? 'cancelled' : ($allVerified ? 'confirmed' : 'pending'));
            $isBad = in_array($state, ['rejected', 'cancelled']);
            $rejectReason = $allBookings->pluck('rejection_reason')->filter()->first();

            $brandName = ['gcash' => 'GCash', 'maya' => 'Maya'];
            $accounts = [
                'gcash' => [
                    ['label' => 'Account 1', 'number' => '0960 302 4390', 'raw' => '09603024390', 'name' => 'Leah Jean Alimasag', 'qr' => 'gcash-qr.png',   'w' => 762, 'h' => 1115],
                    ['label' => 'Account 2', 'number' => '0960 428 9234', 'raw' => '09604289234', 'name' => 'Leah Jean Alimasag', 'qr' => 'gcash-qr-2.png', 'w' => 803, 'h' => 1118],
                ],
                'maya' => [
                    ['label' => 'Account 1', 'number' => '0917 324 5124', 'raw' => '09173245124', 'name' => 'Leah Jean Alimasag', 'qr' => 'maya-qr.png',     'w' => 820, 'h' => 920],
                ],
            ];
        @endphp

        <section class="hcq">
            <style>
                .hcq{position:relative;margin:12px 12px 24px;border-radius:36px;padding:44px 14px 48px;
                    background-color:#1F3F24;
                    background-image:radial-gradient(620px 360px at 92% 0%, rgba(228,240,58,.20), transparent 65%),radial-gradient(700px 420px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
                @media (min-width:768px){.hcq{margin:12px 24px 28px;border-radius:56px;padding:64px 40px 64px}}
                .hcq-deco{position:absolute;inset:0;overflow:hidden;border-radius:inherit;pointer-events:none}
                .hcq-lines{position:absolute;inset:0;width:100%;height:100%;opacity:.06}
                .hcq-ball{position:absolute;color:#E4F03A;animation:hcq-arc 7s ease-in-out infinite alternate}
                .hcq-ball svg{display:block;width:100%;height:100%}
                .hcq-ball.a{left:4%;top:24px;width:34px;height:34px}
                .hcq-ball.b{right:6%;top:34px;width:46px;height:46px;color:#F6F8EE;opacity:.8;animation-delay:-3s}
                .hcq-ball.c{left:8%;bottom:30px;width:40px;height:40px;color:#F6F8EE;opacity:.8;animation-delay:-5s}
                .hcq-ball.d{right:9%;bottom:34px;width:32px;height:32px;animation-delay:-2s}
                @media (max-width:767px){.hcq-ball.b,.hcq-ball.c{display:none}}
                .hcq-inner{position:relative;z-index:2;margin:0 auto;max-width:960px}

                /* hero */
                .hcq-hero{text-align:center}
                .hcq-check{position:relative;display:inline-flex;width:76px;height:76px;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24;animation:hcq-pop .6s cubic-bezier(.2,.9,.3,1.4) both}
                .hcq-check::before,.hcq-check::after{content:"";position:absolute;inset:0;border-radius:9999px;border:2px solid #E4F03A;animation:hcq-ring 2.6s ease-out infinite}
                .hcq-check::before{animation-delay:1.3s}
                .hcq-check path{stroke-dasharray:30;stroke-dashoffset:30;animation:hcq-draw .6s .35s ease forwards}
                .hcq-title{margin:22px 0 0;font-size:clamp(30px,5.2vw,52px);font-weight:800;line-height:1.05;color:#F6F8EE}
                .hcq-title em{font-style:normal;color:#E4F03A}
                .hcq-sub{margin:12px auto 0;max-width:520px;line-height:1.6;color:#CFDDAE}

                /* ticket */
                .hcq-ticket{position:relative;margin:28px auto 0;max-width:560px;border-radius:28px;background:#fff;padding:26px 24px 22px;text-align:center;box-shadow:0 22px 40px -26px rgba(0,0,0,.65)}
                .hcq-cap{margin:0;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:rgba(27,43,28,.6)}
                .hcq-ref{margin:8px 0 0;word-break:break-all;font-size:clamp(34px,7vw,52px);font-weight:800;letter-spacing:.04em;line-height:1.05;color:#2F5D34}
                .hcq-copy{margin-top:14px;display:inline-flex;align-items:center;justify-content:center;border-radius:9999px;border:2px solid rgba(47,93,52,.35);background:#fff;padding:9px 18px;font-size:13px;font-weight:800;color:#2F5D34;cursor:pointer;transition:background .2s,color .2s,border-color .2s}
                .hcq-copy:hover{background:#2F5D34;border-color:#2F5D34;color:#fff}
                .hcq-copy.sm{margin-top:10px;padding:6px 14px;font-size:12px}
                .hcq-perf{position:relative;margin:22px -24px 18px;border-top:2px dashed rgba(27,43,28,.18)}
                .hcq-perf::before,.hcq-perf::after{content:"";position:absolute;top:-13px;width:24px;height:24px;border-radius:9999px;background:#1F3F24}
                .hcq-perf::before{left:-12px}
                .hcq-perf::after{right:-12px}
                .hcq-stats{display:flex;flex-wrap:wrap;justify-content:center;gap:12px 36px}
                .hcq-stat span{display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:rgba(27,43,28,.55)}
                .hcq-stat b{display:inline-flex;align-items:center;gap:8px;margin-top:4px;font-size:20px;font-weight:800;color:#1B2B1C}
                .hcq-stat b.pend{font-size:15px;color:#B45309}
                .hcq-stat b.pend i{width:9px;height:9px;border-radius:9999px;background:#F59E0B;animation:hcq-pulse 2s ease-in-out infinite}
                .hcq-hint{margin:16px 0 0;font-size:13px;color:rgba(27,43,28,.65)}

                /* steps */
                .hcq-steps{display:flex;align-items:center;gap:8px;margin:26px auto 0;max-width:640px;border-radius:9999px;border:1px solid rgba(246,248,238,.18);background:rgba(246,248,238,.08);padding:10px 14px}
                .hcq-step{display:flex;min-width:0;flex:1;align-items:center;gap:10px}
                .hcq-dot{display:flex;width:32px;height:32px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:rgba(246,248,238,.14);color:#CFDDAE;font-size:14px;font-weight:800}
                .hcq-step.is-now .hcq-dot{background:#E4F03A;color:#1F3F24;box-shadow:0 0 0 4px rgba(228,240,58,.3)}
                .hcq-step.is-done .hcq-dot{background:#E4F03A;color:#1F3F24}
                .hcq-label{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;font-weight:800;color:rgba(246,248,238,.55)}
                .hcq-step.is-now .hcq-label,.hcq-step.is-done .hcq-label{color:#F6F8EE}
                .hcq-line{flex:0 0 20px;height:3px;border-radius:3px;background:rgba(246,248,238,.18)}
                .hcq-line.is-done{background:#E4F03A}
                
                /* cards */
                .hcq-stack{display:grid;gap:22px;margin-top:26px}
                .hcq-card{position:relative;overflow:hidden;border-radius:30px;background:#fff;padding:28px 22px;box-shadow:0 22px 40px -26px rgba(0,0,0,.65)}
                @media (min-width:640px){.hcq-card{padding:34px 34px}}
                .hcq-card::before{content:"";position:absolute;left:0;right:0;top:0;height:6px;background:linear-gradient(90deg,#2F5D34,#E4F03A,#2F5D34)}
                .hcq-head{display:flex;align-items:center;gap:12px;margin-bottom:22px}
                .hcq-num{display:flex;width:38px;height:38px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24;font-size:16px;font-weight:800}
                .hcq-dl{display:grid;gap:20px 32px;grid-template-columns:minmax(0,1fr)}
                @media (min-width:640px){.hcq-dl{grid-template-columns:repeat(2,minmax(0,1fr))}}
                .hcq-dl dt{font-size:13px;font-weight:700;color:rgba(27,43,28,.6)}
                .hcq-dl dd{margin:4px 0 0;font-weight:800;word-break:break-word;color:#1B2B1C}
                .hcq-slotchip{display:inline-flex;align-items:center;border-radius:9999px;background:#E5ECD2;padding:7px 14px;font-size:12px;font-weight:800;color:#1F3F24}

                /* payment */
                .hcq-amount{display:flex;align-items:center;justify-content:space-between;gap:12px;border-radius:22px;background:#1F3F24;padding:16px 20px;
                    background-image:radial-gradient(280px 140px at 100% 0%, rgba(228,240,58,.22), transparent 65%)}
                .hcq-amount span{font-size:13px;font-weight:700;color:#CFDDAE}
                .hcq-amount b{font-size:32px;font-weight:800;line-height:1.1;color:#E4F03A}
                .hcq-tabs{display:inline-flex;gap:6px;margin-top:20px;border-radius:9999px;background:#E5ECD2;padding:5px}
                .hcq-tab{border:0;border-radius:9999px;background:transparent;padding:10px 22px;font-size:14px;font-weight:800;color:rgba(27,43,28,.7);cursor:pointer;transition:background .2s,color .2s}
                .hcq-tab.on{background:#1F3F24;color:#E4F03A}
                .hcq-accs{display:grid;gap:16px;margin-top:20px;grid-template-columns:minmax(0,1fr)}
                @media (min-width:640px){.hcq-accs.two{grid-template-columns:repeat(2,minmax(0,1fr))}}
                .hcq-accs.one{max-width:400px;margin-left:auto;margin-right:auto}
                .hcq-acc{border-radius:26px;border:2px solid rgba(27,43,28,.1);background:#F6F8EE;padding:18px;text-align:center}
                .hcq-acc-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
                .hcq-brand{border-radius:9999px;padding:5px 13px;font-size:12px;font-weight:800;color:#fff}
                .hcq-brand.gcash{background:#0B63F6}
                .hcq-brand.maya{background:#00A859}
                .hcq-acc-label{font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:rgba(27,43,28,.6)}
                .hcq-qr{margin:14px auto 0;max-width:220px;overflow:hidden;border-radius:18px;background:#fff;box-shadow:0 14px 26px -18px rgba(27,43,28,.55)}
                .hcq-qr img{display:block;width:100%;height:auto}
                .hcq-save{display:inline-block;margin:10px 0 14px;font-size:13px;font-weight:800;color:#2F5D34;text-decoration:underline;text-underline-offset:4px}
                .hcq-save:hover{color:#1F3F24}
                .hcq-accnum{margin:4px 0 0;font-size:28px;font-weight:800;letter-spacing:.02em;color:#1F3F24}
                .hcq-accname{margin:4px 0 0;font-weight:800;color:#1B2B1C}
                .hcq-acc .hcq-cap{margin-top:12px}
                .hcq-note{margin:16px 0 0;font-size:12.5px;line-height:1.6;color:rgba(27,43,28,.68)}

                /* actions */
                .hcq-fb{display:flex;width:100%;align-items:center;justify-content:center;gap:10px;margin-top:18px;border-radius:9999px;background:#1F3F24;padding:16px 22px;font-size:15px;font-weight:800;color:#E4F03A;text-decoration:none;transition:background .2s,transform .2s}
                .hcq-fb:hover{background:#2F5D34;transform:translateY(-2px)}
                .hcq-btns{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-top:28px}
                .hcq-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:9999px;padding:15px 28px;font-size:15px;font-weight:800;text-decoration:none;transition:background .2s,color .2s,border-color .2s}
                .hcq-btn.main{background:#E4F03A;color:#1B2B1C}
                .hcq-btn.main:hover{background:#fff}
                .hcq-btn.ghost{border:2px solid rgba(246,248,238,.35);color:#F6F8EE}
                .hcq-btn.ghost:hover{background:#F6F8EE;color:#1F3F24;border-color:#F6F8EE}

                .hcq-check.bad{background:#F87171;color:#fff}
                .hcq-check.bad::before,.hcq-check.bad::after{border-color:#F87171}
                .hcq-check.bad path{stroke-dasharray:40;stroke-dashoffset:40}
                .hcq-title.bad em{color:#FCA5A5}
                .hcq-stat b i{display:inline-block;width:9px;height:9px;border-radius:9999px}
                .hcq-stat b.ok{font-size:15px;color:#15803D}
                .hcq-stat b.ok i{background:#22C55E;animation:none}
                .hcq-stat b.bad{font-size:15px;color:#B91C1C}
                .hcq-stat b.bad i{background:#EF4444;animation:none}
                .hcq-refresh{margin-left:8px;border:0;background:none;padding:0;font-size:13px;font-weight:800;color:#2F5D34;text-decoration:underline;text-underline-offset:4px;cursor:pointer}
                .hcq-reason{margin:14px 0 0;border-radius:18px;background:#FEF2F2;border:1px solid #FECACA;padding:12px 16px;font-size:14px;line-height:1.6;color:#7F1D1D}
                .hcq-go{display:flex;width:100%;align-items:center;justify-content:center;gap:10px;margin-top:18px;border-radius:9999px;background:#E4F03A;padding:16px 22px;font-size:15px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s}
                .hcq-go:hover{background:#1F3F24;color:#E4F03A}

                @keyframes hcq-pop{from{transform:scale(0)}to{transform:scale(1)}}
                @keyframes hcq-ring{0%{transform:scale(1);opacity:.8}100%{transform:scale(1.9);opacity:0}}
                @keyframes hcq-draw{to{stroke-dashoffset:0}}
                @keyframes hcq-pulse{0%,100%{opacity:1}50%{opacity:.45}}
                @keyframes hcq-arc{0%{transform:translate(0,0) rotate(0)}50%{transform:translate(26px,-24px) rotate(140deg)}100%{transform:translate(52px,0) rotate(280deg)}}
                @media (prefers-reduced-motion:reduce){
                    .hcq-ball,.hcq-check,.hcq-check::before,.hcq-check::after,.hcq-stat b.pend i{animation:none}
                    .hcq-check path{animation:none;stroke-dashoffset:0}
                }
            </style>

            {{-- background: court lines + floating pickleballs --}}
            <div class="hcq-deco" aria-hidden="true">
                <svg class="hcq-lines" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round">
                    <rect x="60" y="60" width="1080" height="480" rx="6"/>
                    <line x1="600" y1="60" x2="600" y2="540" stroke-dasharray="3 10"/>
                    <line x1="428" y1="60" x2="428" y2="540"/>
                    <line x1="772" y1="60" x2="772" y2="540"/>
                    <line x1="60" y1="300" x2="428" y2="300"/>
                    <line x1="772" y1="300" x2="1140" y2="300"/>
                </svg>
                <div class="hcq-ball a"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcq-ball b"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcq-ball c"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                <div class="hcq-ball d"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
            </div>

            <div class="hcq-inner">

                {{-- Success hero --}}
                <div class="hcq-hero">
                    <span class="hcq-check {{ $isBad ? 'bad' : '' }}">
                        @if ($isBad)
                            <svg width="34" height="34" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 6l12 12M18 6L6 18"></path></svg>
                        @else
                            <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </span>

                    <h1 class="hc-display hcq-title {{ $isBad ? 'bad' : '' }}">
                        @if ($state === 'confirmed')
                            Booking <em>Confirmed!</em>
                        @elseif ($state === 'rejected')
                            Booking <em>Not Approved</em>
                        @elseif ($state === 'cancelled')
                            Booking <em>Cancelled</em>
                        @else
                            Booking Request <em>Submitted!</em>
                        @endif
                    </h1>

                    <p class="hcq-sub">
                        @if ($state === 'confirmed')
                            Your payment is verified and your slot is reserved. See you on the court!
                        @elseif ($state === 'rejected')
                            We couldn't approve this booking. Please message us on Facebook and we'll help you sort it out.
                        @elseif ($state === 'cancelled')
                            This booking was cancelled. If you think this is a mistake, please message us on Facebook.
                        @else
                            Thank you for choosing PickleHouse. One more step: send your payment and receipt to confirm your slot.
                        @endif
                    </p>
                </div>

                {{-- Reference ticket --}}
                <div class="hcq-ticket">
                    <p class="hcq-cap">Your booking reference</p>
                    <p class="hc-display hcq-ref">{{ $booking->booking_reference }}</p>
                    <button type="button" class="hcq-copy" data-copy="{{ $booking->booking_reference }}" data-label="Copy reference" onclick="hcCopy(this)">Copy reference</button>

                    <div class="hcq-perf"></div>

                    <div class="hcq-stats">
                        <div class="hcq-stat"><span>Status</span>
                            @if ($state === 'confirmed')
                                <b class="ok"><i></i>Confirmed · Payment verified</b>
                            @elseif ($state === 'rejected')
                                <b class="bad"><i></i>Not approved</b>
                            @elseif ($state === 'cancelled')
                                <b class="bad"><i></i>Cancelled</b>
                            @else
                                <b class="pend"><i></i>Awaiting payment</b>
                            @endif
                        </div>
                        <div class="hcq-stat"><span>Total</span><b class="hc-display">₱{{ number_format($payTotal, 2) }}</b></div>
                    </div>
                    <p class="hcq-hint">Screenshot this reference for your records.
                        @if ($state === 'pending')
                            <button type="button" class="hcq-refresh" onclick="location.reload()">Refresh status</button>
                        @endif
                    </p>
                </div>

                <div class="hcq-stack">

                    {{-- Booking details --}}
                    <div class="hcq-card">
                        <div class="hcq-head">
                            <span class="hcq-num">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                            </span>
                            <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">Booking details</h2>
                        </div>

                        <dl class="hcq-dl">
                            <div>
                                <dt>Customer</dt>
                                <dd>{{ $booking->customer_name ?? $booking->customer->full_name ?? 'Guest Customer' }}</dd>
                            </div>
                            <div>
                                <dt>Contact number</dt>
                                <dd>{{ $booking->customer->contact_number ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt>Court</dt>
                                <dd>{{ $booking->court->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt>Date</dt>
                                <dd>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</dd>
                            </div>
                            <div style="grid-column:1 / -1;">
                                <dt>Time slot{{ (isset($bookings) && $bookings->count() > 1) ? 's' : '' }}</dt>
                                <dd style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
                                    @forelse (($bookings ?? collect([$booking])) as $slotBooking)
                                        <span class="hcq-slotchip">
                                            {{ \Carbon\Carbon::parse($slotBooking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slotBooking->end_time)->format('g:i A') }}
                                        </span>
                                    @empty
                                        <span style="font-size:12px;color:rgba(27,43,28,.6);">N/A</span>
                                    @endforelse
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @if ($state === 'pending')

                    {{-- Payment --}}
                    <div class="hcq-card" x-data="{ tab: '{{ $method }}' }">
                        <div class="hcq-head">
                            <span class="hcq-num">2</span>
                            <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">Send your payment</h2>
                        </div>

                        <div class="hcq-amount">
                            <span>Please send exactly</span>
                            <b class="hc-display">₱{{ number_format($payTotal, 2) }}</b>
                        </div>

                        <div class="hcq-tabs" role="tablist" aria-label="Payment method">
                            <button type="button" role="tab" class="hcq-tab" :class="{ 'on': tab === 'gcash' }" :aria-selected="tab === 'gcash'" @click="tab = 'gcash'">GCash</button>
                            <button type="button" role="tab" class="hcq-tab" :class="{ 'on': tab === 'maya' }" :aria-selected="tab === 'maya'" @click="tab = 'maya'">Maya</button>
                        </div>

                        @foreach ($accounts as $key => $list)
                            <div x-show="tab === '{{ $key }}'" x-cloak class="hcq-accs {{ count($list) > 1 ? 'two' : 'one' }}">
                                @foreach ($list as $acc)
                                    <div class="hcq-acc">
                                        <div class="hcq-acc-top">
                                            <span class="hcq-brand {{ $key }}">{{ $brandName[$key] }}</span>
                                            @if (count($list) > 1)
                                                <span class="hcq-acc-label">{{ $acc['label'] }}</span>
                                            @endif
                                        </div>

                                        <div class="hcq-qr">
                                            <img src="{{ asset('images/' . $acc['qr']) }}" alt="{{ $brandName[$key] }} QR code for HomeCourt PickleHouse payments, {{ $acc['label'] }}" width="{{ $acc['w'] }}" height="{{ $acc['h'] }}" loading="lazy">
                                        </div>
                                        <a class="hcq-save" href="{{ asset('images/' . $acc['qr']) }}" download="HomeCourt-{{ $brandName[$key] }}-QR-{{ $loop->iteration }}.png">Save QR image</a>

                                        <p class="hcq-cap">Account number</p>
                                        <p class="hc-display hcq-accnum">{{ $acc['number'] }}</p>
                                        <button type="button" class="hcq-copy sm" data-copy="{{ $acc['raw'] }}" data-label="Copy number" onclick="hcCopy(this)">Copy number</button>

                                        <p class="hcq-cap">Account name</p>
                                        <p class="hcq-accname">{{ $acc['name'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- Receipt --}}
                    <div class="hcq-card">
                        <div class="hcq-head">
                            <span class="hcq-num">3</span>
                            <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">Send your receipt</h2>
                        </div>

                        <p style="margin:0;line-height:1.65;color:rgba(27,43,28,.8);">
                            To verify your booking, kindly message our Facebook page with your booking reference and payment receipt.
                        </p>

                        <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="hcq-fb">
                            <svg width="20" height="20" style="flex:none" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <span>Open our Facebook page</span>
                        </a>
                    </div>
                    @else
                        <div class="hcq-card">
                            <div class="hcq-head">
                                <span class="hcq-num">
                                    @if ($isBad)
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                    @else
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                                    @endif
                                </span>
                                <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">
                                    @if ($state === 'confirmed') You're all set
                                    @elseif ($state === 'rejected') What happens now
                                    @else About this booking
                                    @endif
                                </h2>
                            </div>

                            @if ($state === 'confirmed')
                                <p style="margin:0;line-height:1.65;color:rgba(27,43,28,.8);">
                                    Please arrive before your booked time and show this reference to our staff. Your payment has been received, so there is nothing more to send.
                                </p>
                                <a href="https://www.google.com/maps/dir/?api=1&amp;destination=7.3144437,125.6915676" target="_blank" rel="noopener noreferrer" class="hcq-go">
                                    Get directions
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                                </a>
                            @else
                                @if ($state === 'rejected' && $rejectReason)
                                    <p class="hcq-reason"><strong>Reason:</strong> {{ $rejectReason }}</p>
                                @endif
                                <p style="margin:14px 0 0;line-height:1.65;color:rgba(27,43,28,.8);">
                                    Please do not send any payment for this booking. If you already paid, message our Facebook page right away with your booking reference and your receipt so we can sort it out.
                                </p>
                                <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="hcq-fb">
                                    <svg width="20" height="20" style="flex:none" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    <span>Message us on Facebook</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="hcq-btns">
                    <a href="{{ route('booking.create') }}" class="hcq-btn main">
                        Book Again
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('home') }}" class="hcq-btn ghost">Back to home</a>
                </div>
            </div>

            <script>
                function hcCopy(btn) {
                    var text = btn.getAttribute('data-copy');
                    var label = btn.getAttribute('data-label') || btn.textContent;
                    if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(text); }
                    btn.textContent = 'Copied!';
                    setTimeout(function () { btn.textContent = label; }, 1800);
                }
            </script>
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
        <footer class="hc-footer hcz">
            <style>
                .hcz{position:relative;overflow:hidden;border-radius:40px 40px 0 0;color:#CFDDAE;
                    background-color:#1F3F24;
                    background-image:radial-gradient(700px 380px at 100% 0%, rgba(228,240,58,.16), transparent 65%),radial-gradient(640px 380px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
                @media (min-width:768px){.hcz{border-radius:64px 64px 0 0}}
                .hcz-lines{position:absolute;inset:0;width:100%;height:100%;opacity:.05;pointer-events:none}
                .hcz-mark{position:absolute;left:50%;bottom:-.14em;z-index:1;transform:translateX(-50%);font-size:clamp(64px,15vw,210px);font-weight:800;line-height:.8;letter-spacing:-.02em;white-space:nowrap;color:rgba(246,248,238,.05);pointer-events:none;user-select:none}
                .hcz-inner{position:relative;z-index:2;margin:0 auto;max-width:1240px;padding:0 24px}

                .hcz-cta{position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:22px;padding:52px 0 44px;border-bottom:1px solid rgba(246,248,238,.14)}
                .hcz-cta h2{margin:0;font-size:clamp(28px,4.4vw,46px);font-weight:800;line-height:1.05;color:#F6F8EE}
                .hcz-cta h2 em{font-style:normal;color:#E4F03A}
                .hcz-cta p{margin:12px 0 0;max-width:430px;line-height:1.6;color:#CFDDAE}
                .hcz-fb{display:inline-flex;align-items:center;gap:10px;border-radius:9999px;background:#E4F03A;padding:16px 28px;font-size:16px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s}
                .hcz-fb:hover{background:#fff}
                .hcz-fb svg{flex-shrink:0}
                .hcz-ball{position:absolute;right:38%;top:14px;width:40px;height:40px;color:#E4F03A;animation:hcz-hop 2.4s ease-in-out infinite;pointer-events:none}
                .hcz-ball svg{display:block;width:100%;height:100%}
                @media (max-width:900px){.hcz-ball{display:none}}

                .hcz-bar{position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px 24px;padding:22px 0 30px;font-size:13px}
                .hcz-copy{margin:0;color:#CFDDAE}
                .hcz-copy b{color:#F6F8EE}
                .hcz-legal{display:flex;flex-wrap:wrap;align-items:center;gap:10px 22px}
                .hcz-legal button.l{background:none;border:0;padding:0;font-size:13px;font-weight:600;color:#F6F8EE;cursor:pointer;text-underline-offset:4px;transition:color .2s}
                .hcz-legal button.l:hover{color:#E4F03A;text-decoration:underline}
                .hcz-round{display:flex;width:42px;height:42px;align-items:center;justify-content:center;border-radius:9999px;border:2px solid rgba(246,248,238,.3);background:transparent;color:#F6F8EE;cursor:pointer;transition:background .2s,color .2s,border-color .2s}
                .hcz-round:hover{background:#E4F03A;border-color:#E4F03A;color:#1B2B1C}

                @keyframes hcz-hop{0%,100%{transform:translateY(0) rotate(0)}50%{transform:translateY(24px) rotate(180deg)}}
                @media (prefers-reduced-motion:reduce){.hcz-ball{animation:none}}
            </style>

            <svg class="hcz-lines" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round" aria-hidden="true">
                <rect x="60" y="40" width="1080" height="520" rx="6"/>
                <line x1="600" y1="40" x2="600" y2="560" stroke-dasharray="3 10"/>
                <line x1="428" y1="40" x2="428" y2="560"/>
                <line x1="772" y1="40" x2="772" y2="560"/>
                <line x1="60" y1="300" x2="428" y2="300"/>
                <line x1="772" y1="300" x2="1140" y2="300"/>
            </svg>
            <div class="hcz-mark hc-display" aria-hidden="true">HOMECOURT</div>

            <div class="hcz-inner">

                {{-- Receipt band --}}
                <div class="hcz-cta">
                    <div class="hcz-ball" aria-hidden="true"><svg viewBox="0 0 200 200"><use href="#hc-ball"/></svg></div>
                    <div>
                        <h2 class="hc-display">@if ($state === 'pending') Sent your <em>payment?</em> @else Need <em>help?</em> @endif</h2>
                        <p>Message our Facebook page with your booking reference and receipt so we can confirm your slot.</p>
                    </div>
                    <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="hcz-fb">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Send receipt on Facebook
                    </a>
                </div>

                {{-- Bottom bar --}}
                <div class="hcz-bar">
                    <p class="hcz-copy">© {{ date('Y') }} <b>HomeCourt PickleHouse</b>. All rights reserved.</p>

                    <div class="hcz-legal">
                        <button type="button" class="l" @click="$dispatch('open-house-rules')">House Rules</button>
                        <button type="button" class="l" @click="$dispatch('open-privacy')">Privacy Policy</button>
                        <button type="button" class="hcz-round" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" aria-label="Back to top">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                        </button>
                    </div>
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
    <script>
    console.log('%cHomeCourt PickleHouse%c  built by jondev',
        'background:#2F5D34;color:#E4F03A;padding:4px 8px;border-radius:4px 0 0 4px;font-weight:bold',
        'background:#E4F03A;color:#1B2B1C;padding:4px 8px;border-radius:0 4px 4px 0;font-weight:bold');
</script>
</body>
</html>