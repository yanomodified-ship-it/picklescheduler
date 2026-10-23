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
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=1">
</head>

<body class="flex min-h-screen flex-col bg-[#F6F8EE] text-[#1B2B1C] antialiased">

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

            /* top strip */
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

            /* floating bar */
            .hch-wrap{padding:10px 12px 0}
            @media (min-width:768px){.hch-wrap{padding:12px 24px 0}}
            .hch-bar{position:relative;display:flex;align-items:center;justify-content:space-between;gap:16px;max-width:1232px;margin:0 auto;border-radius:9999px;background:#fff;border:1px solid rgba(27,43,28,.08);padding:8px 10px 8px 20px;box-shadow:0 10px 30px -18px rgba(27,43,28,.45);transition:box-shadow .3s ease,padding .25s ease}
            .hch-bar.sc{padding-top:5px;padding-bottom:5px;box-shadow:0 18px 40px -16px rgba(27,43,28,.6)}
            .hch-logo{display:flex;align-items:center}
            .hch-logo img{display:block;width:auto;height:44px;max-height:44px;transition:height .25s ease}
            .hch-bar.sc .hch-logo img{height:36px}

            /* right side */
            .hch-right{display:flex;align-items:center;gap:4px}
            .hch-link{display:none;border-radius:9999px;padding:9px 16px;font-size:14px;font-weight:700;color:rgba(27,43,28,.78);text-decoration:none;transition:background .25s,color .25s}
            @media (min-width:640px){.hch-link{display:inline-block}}
            .hch-link:hover{background:#E5ECD2;color:#1F3F24}
            .hch-home{display:inline-flex;align-items:center;gap:8px;margin-left:6px;border-radius:9999px;background:#E4F03A;padding:11px 20px;font-size:14px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s,color .2s}
            .hch-home:hover{background:#1F3F24;color:#E4F03A}
            .hch-home svg{transition:transform .25s}
            .hch-home:hover svg{transform:translateX(-4px)}

            /* scroll progress */
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
                    <a href="{{ route('home') }}#rates" class="hch-link">Rates</a>
                    <a href="{{ route('home') }}#rules" class="hch-link">Rules</a>
                    <a href="{{ route('home') }}#find-us" class="hch-link">Find us</a>

                    <a href="{{ route('home') }}" class="hch-home">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                        Home
                    </a>
                </nav>

                {{-- Scroll progress line --}}
                <div class="hch-prog" aria-hidden="true"><div :style="{ width: progress + '%' }"></div></div>
            </div>
        </div>
    </header>

    {{-- Flash / Validation Messages --}}
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

        {{-- Booking Form Section --}}
        <section id="booking" class="hcw">
            <style>
                .hcw{position:relative;margin:12px 12px 24px;border-radius:36px;padding:38px 14px 100px;
                    background-color:#1F3F24;
                    background-image:radial-gradient(620px 360px at 92% 0%, rgba(228,240,58,.20), transparent 65%),radial-gradient(700px 420px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
                @media (min-width:768px){.hcw{margin:12px 24px 28px;border-radius:56px;padding:54px 40px 64px}}
                @media (min-width:1024px){.hcw{padding-bottom:64px}}
                .hcw-deco{position:absolute;inset:0;overflow:hidden;border-radius:inherit;pointer-events:none}
                .hcw-lines{position:absolute;inset:0;width:100%;height:100%;opacity:.06}
                .hcw-ball{position:absolute;color:#E4F03A;animation:hcw-arc 7s ease-in-out infinite alternate}
                .hcw-ball svg{display:block;width:100%;height:100%}
                .hcw-ball.a{left:3%;top:22px;width:34px;height:34px}
                .hcw-ball.b{right:6%;top:30px;width:46px;height:46px;color:#F6F8EE;opacity:.8;animation-delay:-3s}
                .hcw-ball.c{left:9%;bottom:24px;width:40px;height:40px;color:#F6F8EE;opacity:.8;animation-delay:-5s}
                @media (max-width:767px){.hcw-ball.b,.hcw-ball.c{display:none}}
                .hcw-inner{position:relative;z-index:2;margin:0 auto;max-width:1240px}

                .hcw-head{margin-bottom:24px}
                .hcw-eyebrow{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:rgba(228,240,58,.14);padding:6px 14px;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#E4F03A}
                .hcw-eyebrow i{width:7px;height:7px;border-radius:9999px;background:#E4F03A;animation:hcw-pulse 2s ease-in-out infinite}
                .hcw-title{margin:14px 0 0;font-size:clamp(30px,5vw,48px);font-weight:800;line-height:1.05;color:#F6F8EE}
                .hcw-title em{font-style:normal;color:#E4F03A}
                .hcw-sub{margin:10px 0 0;font-size:14px;font-weight:600;color:#CFDDAE}

                /* progress steps */
                .hcw-steps{display:flex;align-items:center;gap:8px;margin:0 0 22px;border-radius:9999px;border:1px solid rgba(246,248,238,.18);background:rgba(246,248,238,.08);padding:10px 14px}
                .hcw-step{display:flex;min-width:0;flex:1;align-items:center;gap:10px}
                .hcw-dot{display:flex;width:32px;height:32px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:rgba(246,248,238,.14);color:#CFDDAE;font-size:14px;font-weight:800;transition:background .3s,color .3s,box-shadow .3s}
                .hcw-step.is-now .hcw-dot{background:#E4F03A;color:#1F3F24;box-shadow:0 0 0 4px rgba(228,240,58,.3)}
                .hcw-step.is-done .hcw-dot{background:#E4F03A;color:#1F3F24}
                .hcw-label{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;font-weight:800;color:rgba(246,248,238,.55)}
                .hcw-step.is-now .hcw-label,.hcw-step.is-done .hcw-label{color:#F6F8EE}
                .hcw-line{flex:0 0 26px;height:3px;border-radius:3px;background:rgba(246,248,238,.18);transition:background .3s}
                .hcw-line.is-done{background:#E4F03A}
                @media (max-width:639px){.hcw-label{display:none}.hcw-step.is-now .hcw-label{display:block}.hcw-step{flex:0 1 auto}.hcw-line{flex:1 1 18px}}

                /* white cards */
                .hcw-card{position:relative;overflow:hidden;border-radius:30px;background:#fff;padding:26px 22px;box-shadow:0 22px 40px -26px rgba(0,0,0,.65)}
                @media (min-width:640px){.hcw-card{padding:34px 32px}}
                .hcw-card::before{content:"";position:absolute;left:0;right:0;top:0;height:6px;background:linear-gradient(90deg,#2F5D34,#E4F03A,#2F5D34)}
                .hcw-cardhead{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-bottom:22px}
                .hcw-ctitle{display:flex;align-items:center;gap:12px}
                .hcw-num{display:flex;width:38px;height:38px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24;font-size:16px;font-weight:800}

                /* empty state */
                .hcw-empty{display:flex;flex-direction:column;align-items:center;gap:8px;margin-top:24px;border-radius:24px;border:2px dashed rgba(47,93,52,.3);background:#F6F8EE;padding:32px 20px;text-align:center}
                .hcw-empty .ball{width:44px;height:44px;color:#E4F03A;animation:hcw-hop 1.8s ease-in-out infinite}
                .hcw-empty .ball svg{display:block;width:100%;height:100%;filter:drop-shadow(0 8px 8px rgba(31,63,36,.25))}

                /* time slots */
                .hcw-slots{display:grid;gap:10px;grid-template-columns:repeat(2,minmax(0,1fr))}
                @media (min-width:640px){.hcw-slots{grid-template-columns:repeat(3,minmax(0,1fr))}}
                @media (min-width:768px){.hcw-slots{grid-template-columns:repeat(4,minmax(0,1fr))}}
                @media (min-width:1024px){.hcw-slots{grid-template-columns:repeat(3,minmax(0,1fr))}}
                @media (min-width:1200px){.hcw-slots{grid-template-columns:repeat(4,minmax(0,1fr))}}
                .hcw-slot{display:flex;flex-direction:column;align-items:flex-start;gap:8px;border-radius:18px;border:2px solid rgba(27,43,28,.12);background:#fff;padding:12px;text-align:left;cursor:pointer;transition:transform .2s,border-color .2s,background .2s,box-shadow .2s}
                .hcw-slot:hover:not(:disabled):not(.is-sel){transform:translateY(-3px);border-color:#2F5D34;background:#F3F8E4;box-shadow:0 14px 22px -16px rgba(27,43,28,.5)}
                .hcw-slot.is-sel:hover{transform:translateY(-3px);border-color:#2F5D34;background:#2F5D34}
                .hcw-slot:disabled{cursor:not-allowed}
                .hcw-time{font-size:12.5px;font-weight:800;line-height:1.25;color:#1B2B1C}
                .hcw-meta{display:flex;width:100%;align-items:center;justify-content:space-between;gap:6px}
                .hcw-rate{border-radius:9999px;padding:2px 8px;font-size:11px;font-weight:800}
                .hcw-rate.day{background:#EEF6B0;color:#4D6B12}
                .hcw-rate.night{background:#1F3F24;color:#E4F03A}
                .hcw-state{font-size:11px;font-weight:800;color:#2F5D34}
                .hcw-slot.is-sel{border-color:#1F3F24;background:#1F3F24;box-shadow:0 14px 24px -14px rgba(31,63,36,.8)}
                .hcw-slot.is-sel .hcw-time{color:#fff}
                .hcw-slot.is-sel .hcw-state{color:#E4F03A}
                .hcw-slot.is-sel .hcw-rate{background:rgba(255,255,255,.16);color:#F6F8EE}
                .hcw-slot.is-pending{border-color:#FDE68A;background:#FFFBEB}
                .hcw-slot.is-pending .hcw-time,.hcw-slot.is-pending .hcw-state{color:#B45309}
                .hcw-slot.is-booked{border-color:#FECACA;background:#FEF2F2}
                .hcw-slot.is-booked .hcw-time{color:rgba(185,28,28,.65)}
                .hcw-slot.is-booked .hcw-state{color:#B91C1C}
                .hcw-slot.is-pending .hcw-rate,.hcw-slot.is-booked .hcw-rate{background:rgba(0,0,0,.06);color:rgba(27,43,28,.5)}
                .hcw-summary{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:20px;border-radius:20px;background:#1F3F24;padding:14px 18px;color:#CFDDAE;font-size:14px;font-weight:700}
                .hcw-summary b{color:#fff}
                .hcw-summary .tot{font-size:22px;font-weight:800;color:#E4F03A}
                .hcw-legend{display:flex;flex-wrap:wrap;gap:8px 16px;margin-top:18px;font-size:12px;font-weight:700;color:rgba(27,43,28,.65)}
                .hcw-legend i{display:inline-block;width:10px;height:10px;margin-right:6px;border-radius:3px;vertical-align:-1px}

                /* payment note */
                .hcw-pay{display:flex;align-items:center;gap:12px;border-radius:20px;background:#E5ECD2;padding:14px 16px;font-size:14px;line-height:1.5;color:rgba(27,43,28,.82)}
                .hcw-pay .ic{display:flex;width:38px;height:38px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:#1F3F24;color:#E4F03A}

                /* review ticket */
                .hcw-review{position:relative;overflow:hidden;border-radius:30px;background:#fff;padding:26px 22px;box-shadow:0 22px 40px -26px rgba(0,0,0,.65);scroll-margin-top:90px}
                @media (min-width:640px){.hcw-review{padding:30px 28px}}
                @media (min-width:1024px){.hcw-review{position:sticky;top:96px}}
                .hcw-review::before{content:"";position:absolute;left:0;right:0;top:0;height:6px;background:linear-gradient(90deg,#2F5D34,#E4F03A,#2F5D34)}
                .hcw-rhead{display:flex;align-items:center;gap:12px;margin-bottom:6px}
                .hcw-rico{display:flex;width:38px;height:38px;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24}
                .hcw-rows{margin-top:10px;font-size:14px}
                .hcw-row{display:flex;justify-content:space-between;gap:16px;padding:11px 0;border-bottom:1px solid rgba(27,43,28,.08)}
                .hcw-row span{color:rgba(27,43,28,.6)}
                .hcw-row b{text-align:right;font-weight:800;color:#1B2B1C}
                .hcw-total{margin-top:18px;border-radius:22px;background:#1F3F24;padding:16px 18px;
                    background-image:radial-gradient(260px 140px at 100% 0%, rgba(228,240,58,.22), transparent 65%)}
                .hcw-total .t{font-size:13px;font-weight:700;color:#CFDDAE}
                .hcw-total .v{margin-top:2px;font-size:38px;font-weight:800;line-height:1.1;color:#E4F03A}
                .hcw-agree{display:flex;cursor:pointer;align-items:flex-start;gap:12px;margin-top:16px;border-radius:20px;border:1px solid #FECACA;background:#FEF2F2;padding:14px;font-size:13px;line-height:1.6;color:rgba(27,43,28,.82)}
                .hcw-agree strong{color:#B91C1C}
                .hcw-submit{display:flex;width:100%;align-items:center;justify-content:center;gap:8px;margin-top:16px;border:0;border-radius:9999px;background:#1F3F24;padding:16px;font-size:16px;font-weight:800;color:#E4F03A;cursor:pointer;transition:background .2s,transform .2s}
                .hcw-submit:hover:not(:disabled){background:#2F5D34;transform:translateY(-2px)}
                .hcw-submit:disabled{opacity:.4;cursor:not-allowed}
                .hcw-note{margin:14px 0 0;text-align:center;font-size:12px;line-height:1.6;color:rgba(27,43,28,.65)}

                /* mobile bottom bar */
                .hcw-bar{position:fixed;left:12px;right:12px;bottom:12px;z-index:40;display:flex;align-items:center;justify-content:space-between;gap:12px;border-radius:9999px;background:#1F3F24;padding:10px 10px 10px 22px;box-shadow:0 18px 36px -12px rgba(0,0,0,.6)}
                @media (min-width:1024px){.hcw-bar{display:none !important}}
                .hcw-bar .t{font-size:11px;font-weight:700;color:#CFDDAE}
                .hcw-bar .v{font-size:20px;font-weight:800;line-height:1.1;color:#E4F03A}
                .hcw-bar a{flex-shrink:0;border-radius:9999px;background:#E4F03A;padding:12px 20px;font-size:14px;font-weight:800;color:#1B2B1C;text-decoration:none}

                @keyframes hcw-pulse{0%,100%{opacity:1}50%{opacity:.45}}
                @keyframes hcw-arc{0%{transform:translate(0,0) rotate(0)}50%{transform:translate(26px,-24px) rotate(140deg)}100%{transform:translate(52px,0) rotate(280deg)}}
                @keyframes hcw-hop{0%,100%{transform:translateY(0) rotate(0)}50%{transform:translateY(-14px) rotate(160deg)}}
                @media (prefers-reduced-motion:reduce){
                    .hcw-ball,.hcw-empty .ball,.hcw-eyebrow i{animation:none}
                    .hcw-slot,.hcw-submit,.hcw-dot{transition:none}
                }
            </style>

            {{-- background: court lines + floating pickleballs --}}
            <div class="hcw-deco" aria-hidden="true">
                <svg class="hcw-lines" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round">
                    <rect x="60" y="60" width="1080" height="480" rx="6"/>
                    <line x1="600" y1="60" x2="600" y2="540" stroke-dasharray="3 10"/>
                    <line x1="428" y1="60" x2="428" y2="540"/>
                    <line x1="772" y1="60" x2="772" y2="540"/>
                    <line x1="60" y1="300" x2="428" y2="300"/>
                    <line x1="772" y1="300" x2="1140" y2="300"/>
                </svg>
                <div class="hcw-ball a"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".85"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/><circle cx="50" cy="29" r="5"/><circle cx="50" cy="71" r="5"/></g></svg></div>
                <div class="hcw-ball b"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".35"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/></g></svg></div>
                <div class="hcw-ball c"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".35"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/></g></svg></div>
            </div>

            <div class="hcw-inner">

                {{-- Heading --}}
                <div class="hcw-head">
                    <span class="hcw-eyebrow"><i></i>Book online</span>
                    <h1 class="hc-display hcw-title">Reserve your <em>court</em></h1>
                    <p class="hcw-sub">Open 4:00 AM – 2:00 AM &nbsp;·&nbsp; ₱200 day &nbsp;·&nbsp; ₱300 night</p>
                </div>

                <form id="bookingForm"
                      method="POST"
                      action="{{ route('booking.store') }}"
                      x-data="bookingForm({{ json_encode($courts) }})"
                      @submit="prepareSubmit">
                    @csrf

                    {{-- Hidden Inputs for Form Submission: one input per selected hour slot --}}
                    <template x-for="slot in [...selectedSlots].sort()" :key="slot">
                        <input type="hidden" name="slots[]" :value="slot">
                    </template>

                    {{-- Progress steps --}}
                    <div class="hcw-steps" aria-label="Booking progress">
                        <div class="hcw-step" :class="{ 'is-done': (courtId && selectedSlots.length > 0), 'is-now': !(courtId && selectedSlots.length > 0) }">
                            <span class="hcw-dot">
                                <span x-show="!(courtId && selectedSlots.length > 0)">1</span>
                                <svg x-show="(courtId && selectedSlots.length > 0)" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                            </span>
                            <span class="hcw-label">Court &amp; time</span>
                        </div>
                        <span class="hcw-line" :class="{ 'is-done': (courtId && selectedSlots.length > 0) }"></span>

                        <div class="hcw-step" :class="{ 'is-done': (String(customerName || '').trim().length > 1 && String(contactNumber || '').replace(/\D/g, '').length >= 7 && Number(players) >= 1), 'is-now': (courtId && selectedSlots.length > 0) && !(String(customerName || '').trim().length > 1 && String(contactNumber || '').replace(/\D/g, '').length >= 7 && Number(players) >= 1) }">
                            <span class="hcw-dot">
                                <span x-show="!(String(customerName || '').trim().length > 1 && String(contactNumber || '').replace(/\D/g, '').length >= 7 && Number(players) >= 1)">2</span>
                                <svg x-show="(String(customerName || '').trim().length > 1 && String(contactNumber || '').replace(/\D/g, '').length >= 7 && Number(players) >= 1)" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                            </span>
                            <span class="hcw-label">Your details</span>
                        </div>
                        <span class="hcw-line" :class="{ 'is-done': (String(customerName || '').trim().length > 1 && String(contactNumber || '').replace(/\D/g, '').length >= 7 && Number(players) >= 1) }"></span>

                        <div class="hcw-step" :class="{ 'is-done': agreed, 'is-now': (courtId && selectedSlots.length > 0) && (String(customerName || '').trim().length > 1 && String(contactNumber || '').replace(/\D/g, '').length >= 7 && Number(players) >= 1) && !agreed }">
                            <span class="hcw-dot">
                                <span x-show="!agreed">3</span>
                                <svg x-show="agreed" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                            </span>
                            <span class="hcw-label">Confirm</span>
                        </div>
                    </div>

                    <div class="grid gap-8 lg:grid-cols-3">

                        <div class="space-y-6 lg:col-span-2">

                            {{-- STEP 1: SCHEDULE & AVAILABILITY GRID --}}
                            <div class="hcw-card">
                                <div class="hcw-cardhead">
                                    <div class="hcw-ctitle">
                                        <span class="hcw-num">1</span>
                                        <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">Select court &amp; schedule</h2>
                                    </div>

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
                                                    $type = $court->classification ?? 'Indoor';
                                                    $rate = $court->price_per_hour ?? 500;
                                                @endphp
                                                <option value="{{ $court->id }}" data-rate="{{ $rate }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                                    {{ $court->name }} — {{ ucfirst($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Empty state (shown until a court is chosen) --}}
                                <div x-show="!courtId" x-cloak class="hcw-empty">
                                    <div class="ball">
                                        <svg viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".85"><circle cx="50" cy="50" r="4.5"/><circle cx="67.3" cy="60" r="4.5"/><circle cx="50" cy="70" r="4.5"/><circle cx="32.7" cy="60" r="4.5"/><circle cx="32.7" cy="40" r="4.5"/><circle cx="50" cy="30" r="4.5"/><circle cx="67.3" cy="40" r="4.5"/><circle cx="86" cy="50" r="4.5"/><circle cx="68" cy="81" r="4.5"/><circle cx="32" cy="81" r="4.5"/><circle cx="14" cy="50" r="4.5"/><circle cx="32" cy="19" r="4.5"/><circle cx="68" cy="19" r="4.5"/></g></svg>
                                    </div>
                                    <p class="hc-display" style="margin:0;font-size:18px;font-weight:800;color:#1B2B1C;">Pick a court to see available times</p>
                                    <p style="margin:0;font-size:13px;color:rgba(27,43,28,.65);">Choose a court above and its open hours will appear here.</p>
                                </div>

                                {{-- TIME SLOT GRID --}}
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

                                    <div class="hcw-slots">
                                        <template x-for="slot in allSlots" :key="slot">
                                            <button
                                                type="button"
                                                :disabled="isSlotBooked(slot)"
                                                :class="[
                                                    isSlotBooked(slot)
                                                        ? (isSlotPending(slot) ? 'is-pending' : 'is-booked')
                                                        : (selectedSlots.includes(slot) ? 'is-sel' : '')
                                                ]"
                                                @click="selectSlot(slot)"
                                                class="hcw-slot">

                                                <span class="hcw-time" x-text="getSlotRangeLabel(slot)"></span>

                                                <span class="hcw-meta">
                                                    <span class="hcw-rate"
                                                          :class="(parseInt(slot) >= 4 && parseInt(slot) < 17) ? 'day' : 'night'"
                                                          x-text="(parseInt(slot) >= 4 && parseInt(slot) < 17) ? '₱200' : '₱300'"></span>
                                                    <span class="hcw-state">
                                                        <span x-text="isSlotBooked(slot) ? (isSlotPending(slot) ? 'Pending' : 'Booked') : (selectedSlots.includes(slot) ? 'Selected' : 'Available')"></span>
                                                    </span>
                                                </span>
                                            </button>
                                        </template>
                                    </div>

                                    {{-- Live selection summary --}}
                                    <div x-show="selectedSlots.length > 0" x-cloak class="hcw-summary">
                                        <span><b x-text="durationLabel"></b> selected</span>
                                        <span class="tot hc-display" x-text="formattedTotal"></span>
                                    </div>

                                    <div class="hcw-legend">
                                        <span><i style="background:#fff;border:2px solid #CFDDAE"></i>Available</span>
                                        <span><i style="background:#1F3F24"></i>Selected</span>
                                        <span><i style="background:#FDE68A"></i>Pending</span>
                                        <span><i style="background:#FECACA"></i>Booked</span>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 2: CUSTOMER DETAILS --}}
                            <div class="hcw-card">
                                <div class="hcw-cardhead">
                                    <div class="hcw-ctitle">
                                        <span class="hcw-num">2</span>
                                        <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">Your details</h2>
                                    </div>
                                </div>
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

                            {{-- STEP 3: PAYMENT METHOD --}}
                            <div class="hcw-card">
                                <style>
                                    .hcw-pm{display:grid;gap:12px;grid-template-columns:repeat(2,minmax(0,1fr))}
                                    .hcw-pm button{position:relative;display:flex;flex-direction:column;align-items:flex-start;gap:12px;border-radius:22px;border:2px solid rgba(27,43,28,.12);background:#fff;padding:16px;text-align:left;cursor:pointer;transition:transform .2s,border-color .2s,background .2s,box-shadow .2s}
                                    .hcw-pm button:hover{transform:translateY(-3px);border-color:#2F5D34}
                                    .hcw-pm .logo{display:flex;width:44px;height:44px;align-items:center;justify-content:center;border-radius:14px;font-size:20px;font-weight:800;color:#fff}
                                    .hcw-pm .gcash .logo{background:#0B63F6}
                                    .hcw-pm .maya .logo{background:#00A859}
                                    .hcw-pm b{display:block;font-size:16px;font-weight:800;color:#1B2B1C}
                                    .hcw-pm small{display:block;margin-top:2px;font-size:12px;font-weight:600;line-height:1.4;color:rgba(27,43,28,.6)}
                                    .hcw-pm .tick{position:absolute;right:12px;top:12px;display:none;width:24px;height:24px;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24}
                                    .hcw-pm button.is-on{border-color:#1F3F24;background:#F3F8E4;box-shadow:0 14px 24px -18px rgba(31,63,36,.8)}
                                    .hcw-pm button.is-on .tick{display:flex}
                                    .hcw-pm-note{display:flex;align-items:center;gap:10px;margin-top:16px;font-size:13px;line-height:1.5;color:rgba(27,43,28,.7)}
                                    .hcw-pm-note svg{flex-shrink:0;color:#2F5D34}
                                </style>

                                <div class="hcw-cardhead">
                                    <div class="hcw-ctitle">
                                        <span class="hcw-num">3</span>
                                        <h2 class="hc-display text-xl font-bold text-[#1B2B1C]">Payment Method</h2>
                                    </div>
                                </div>

                                <input type="hidden" name="payment_method" value="GCash" :value="paymentMethod">

                                <div class="hcw-pm" role="radiogroup" aria-label="Mode of payment">
                                    <button type="button" class="gcash" role="radio" :aria-checked="paymentMethod === 'GCash'" :class="{ 'is-on': paymentMethod === 'GCash' }" @click="paymentMethod = 'GCash'">
                                        <span class="tick"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg></span>
                                        <span class="logo">G</span>
                                        <span><b>GCash</b><small>Scan a QR or send by number</small></span>
                                    </button>

                                    <button type="button" class="maya" role="radio" :aria-checked="paymentMethod === 'Maya'" :class="{ 'is-on': paymentMethod === 'Maya' }" @click="paymentMethod = 'Maya'">
                                        <span class="tick"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg></span>
                                        <span class="logo">M</span>
                                        <span><b>Maya</b><small>Scan a QR or send by number</small></span>
                                    </button>
                                </div>

                                <p class="hcw-pm-note">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg>
                                    You pay after submitting. The account numbers and QR codes appear on the next screen.
                                </p>
                            </div>

                        </div>

                        {{-- SIDEBAR SUMMARY --}}
                        <aside class="lg:col-span-1">
                            <div id="hcw-review" class="hcw-review">
                                <div class="hcw-rhead">
                                    <span class="hcw-rico">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 3h14v18l-3-2-2 2-2-2-2 2-2-2-3 2z"/><path d="M9 8h6"/><path d="M9 12h6"/></svg>
                                    </span>
                                    <h2 class="hc-display text-2xl font-bold text-[#1B2B1C]">Review booking</h2>
                                </div>

                                <div class="hcw-rows">
                                    <div class="hcw-row"><span>Customer</span><b x-text="customerName || 'Not provided'"></b></div>
                                    <div class="hcw-row"><span>Contact Number</span><b x-text="contactNumber || 'Not provided'"></b></div>
                                    <div class="hcw-row"><span>Date</span><b x-text="formattedDate"></b></div>
                                    <div class="hcw-row"><span>Court</span><b x-text="courtName"></b></div>
                                    <div class="hcw-row"><span>Time</span><b x-text="formattedTime"></b></div>
                                    <div class="hcw-row"><span>Duration</span><b x-text="durationLabel"></b></div>
                                    <div class="hcw-row" style="border-bottom:0"><span>Players</span><b x-text="players || '—'"></b></div>
                                </div>

                                <div class="hcw-total">
                                    <div class="t">Total Price</div>
                                    <div class="v hc-display" x-text="formattedTotal"></div>
                                </div>

                                {{-- Agreement: required before the booking can be submitted --}}
                                <label class="hcw-agree">
                                    <input type="checkbox" name="agree_terms" value="1" x-model="agreed" class="mt-1 rounded" style="width:1rem;height:1rem;flex:none;accent-color:#2F5D34">
                                    <span>I understand that this booking is <strong>non-refundable and non-cancellable</strong> once submitted. I have reviewed my court, date and time slots and am ready to proceed.</span>
                                </label>

                                <button type="submit" :disabled="submitting || isFullyBooked || selectedSlots.length === 0 || !agreed" class="hcw-submit">
                                    <span x-show="!submitting">Submit Booking</span>
                                    <span x-show="submitting" x-cloak>Submitting...</span>
                                </button>

                                <p class="hcw-note">
                                    Please read our
                                    <button type="button" @click="$dispatch('open-house-rules')" class="font-semibold text-[#2F5D34] underline underline-offset-2 hover:text-[#1F3F24]">House Rules</button>
                                    and
                                    <button type="button" @click="$dispatch('open-privacy')" class="font-semibold text-[#2F5D34] underline underline-offset-2 hover:text-[#1F3F24]">Privacy Policy</button>.
                                    We use your name and contact number only to manage your booking.
                                </p>
                            </div>
                        </aside>
                    </div>

                    {{-- Mobile bottom bar: total + jump to review --}}
                    <div class="hcw-bar" x-show="selectedSlots.length > 0" x-cloak>
                        <div>
                            <div class="t"><span x-text="durationLabel"></span></div>
                            <div class="v hc-display" x-text="formattedTotal"></div>
                        </div>
                        <a href="#hcw-review">Review &amp; book</a>
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

        {{-- Footer --}}
        <footer class="hc-footer hcz">
            <style>
                .hcz{position:relative;overflow:hidden;border-radius:40px 40px 0 0;color:#CFDDAE;
                    background-color:#1F3F24;
                    background-image:radial-gradient(700px 380px at 100% 0%, rgba(228,240,58,.16), transparent 65%),radial-gradient(640px 380px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
                @media (min-width:768px){.hcz{border-radius:64px 64px 0 0}}
                @media (max-width:1023px){.hcz{padding-bottom:64px}}
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

                {{-- Help band --}}
                <div class="hcz-cta">
                    <div class="hcz-ball" aria-hidden="true">
                        <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".85"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/><circle cx="50" cy="29" r="5"/><circle cx="50" cy="71" r="5"/></g></svg>
                    </div>
                    <div>
                        <h2 class="hc-display">Questions about your <em>booking?</em></h2>
                        <p>Send us a message on Facebook with your booking reference and we'll help you out.</p>
                    </div>
                    <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="hcz-fb">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Message us on Facebook
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
                serverNow: @js(now()->format('Y-m-d\TH:i:s')),   
                loadedAt: Date.now(),

                
                get allSlots() {
                    if (!this.courtId) return [];
                    const court = this.courts.find(c => String(c.id) === String(this.courtId));
                    if (!court) return [];

                    let startHour = parseInt((court.operating_hours_start || '04:00').substring(0, 2), 10);
                    let endHour = parseInt((court.operating_hours_end || '01:00').substring(0, 2), 10);

                    
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

                
                isSlotPast(slotTime) {
                    const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
                    const [h] = slotTime.split(':').map(Number);
                    const slotStart = new Date(`${this.bookingDate}T00:00:00`);
                    slotStart.setHours(h < 4 ? h + 24 : h, 0, 0, 0); 
                    return slotStart.getTime() + 3600000 <= now.getTime();
                },

                isSlotPast(slotTime) {
                    const nowMs = new Date(this.serverNow).getTime() + (Date.now() - this.loadedAt);
                    const [h] = slotTime.split(':').map(Number);
                    const start = new Date(`${this.bookingDate}T00:00:00`);
                    start.setHours(h < 4 ? h + 24 : h, 0, 0, 0);
                    return start.getTime() + 3600000 <= nowMs;
                },

                isSlotBooked(slotTime) {
                    if (this.isSlotPast(slotTime)) return true;
                    return this.activeBookingsForSelected.some(b => {
                        const start = b.start_time.substring(0, 5);
                        const end = b.end_time.substring(0, 5);
                        const status = (b.booking_status || '').toLowerCase();
                        const isTaken = status === 'confirmed' || status.includes('pending');
                        return isTaken && slotTime >= start && slotTime < end;
                    });
                },


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
    <script>
    console.log('%cHomeCourt PickleHouse%c  built by jondev',
        'background:#2F5D34;color:#E4F03A;padding:4px 8px;border-radius:4px 0 0 4px;font-weight:bold',
        'background:#E4F03A;color:#1B2B1C;padding:4px 8px;border-radius:0 4px 4px 0;font-weight:bold');
</script>
</body>
</html>