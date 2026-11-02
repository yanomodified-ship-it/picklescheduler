<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Open Play | HomeCourt PickleHouse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; background: #F6F8EE; color: #1B2B1C; font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; }
        .hc-display { font-family: 'Bricolage Grotesque', 'Inter', system-ui, sans-serif; letter-spacing: -0.025em; }
        [x-cloak] { display: none !important; }
        a:focus-visible, button:focus-visible { outline: 2px solid #2F5D34; outline-offset: 3px; }
    </style>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=1">
</head>
<body>

    @php
        $hcNow  = now()->hour + now()->minute / 60;
        $hcOpen = ($hcNow >= 4);
        $groups = ['today' => $today, 'upcoming' => $upcoming];
    @endphp

    {{-- Header --}}
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
        </style>

        <div class="hch-top" :class="{ 'off': scrolled }">
            <div class="hch-top-in">
                @if ($hcOpen)
                    <span class="hch-status"><i></i>Open now · until 12:00 AM</span>
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

        <div class="hch-wrap">
            <div class="hch-bar" :class="{ 'sc': scrolled }">
                <a href="{{ route('home') }}" class="hch-logo" aria-label="HomeCourt PickleHouse home">
                    <img src="{{ asset('images/logo.png') }}" alt="HomeCourt PickleHouse" width="222" height="120" fetchpriority="high">
                </a>

                <nav class="hch-right" aria-label="Main">
                    <a href="{{ route('home') }}" class="hch-link">Home</a>
                    <a href="{{ route('booking.create') }}" class="hch-cta">
                        Book a court
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </nav>

                <div class="hch-prog" aria-hidden="true"><div :style="{ width: progress + '%' }"></div></div>
            </div>
        </div>
    </header>

    {{-- Open play section --}}
    <main>
        <section class="hcp" x-data="{ tab: '{{ $defaultTab }}', rosterId: null }" @keydown.escape.window="rosterId = null" x-effect="document.body.style.overflow = rosterId ? 'hidden' : ''">
            <style>
                .hcp{position:relative;margin:12px 12px 24px;border-radius:36px;padding:44px 14px 52px;
                    background-color:#1F3F24;
                    background-image:radial-gradient(620px 360px at 92% 0%, rgba(228,240,58,.20), transparent 65%),radial-gradient(700px 420px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
                @media (min-width:768px){.hcp{margin:12px 24px 28px;border-radius:56px;padding:64px 40px 68px}}
                .hcp-deco{position:absolute;inset:0;overflow:hidden;border-radius:inherit;pointer-events:none}
                .hcp-lines{position:absolute;inset:0;width:100%;height:100%;opacity:.06}
                .hcp-ball{position:absolute;color:#E4F03A;animation:hcp-arc 7s ease-in-out infinite alternate}
                .hcp-ball svg{display:block;width:100%;height:100%}
                .hcp-ball.a{left:4%;top:24px;width:34px;height:34px}
                .hcp-ball.b{right:6%;top:34px;width:46px;height:46px;color:#F6F8EE;opacity:.8;animation-delay:-3s}
                .hcp-ball.c{left:8%;bottom:30px;width:40px;height:40px;color:#F6F8EE;opacity:.8;animation-delay:-5s}
                @media (max-width:767px){.hcp-ball.b,.hcp-ball.c{display:none}}
                .hcp-inner{position:relative;z-index:2;margin:0 auto;max-width:1000px}

                .hcp-hero{text-align:center}
                .hcp-eyebrow{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:rgba(228,240,58,.14);padding:6px 14px;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#E4F03A}
                .hcp-eyebrow i{width:7px;height:7px;border-radius:9999px;background:#E4F03A;animation:hcp-pulse 2s ease-in-out infinite}
                .hcp-title{margin:16px 0 0;font-size:clamp(32px,5.4vw,54px);font-weight:800;line-height:1.05;color:#F6F8EE}
                .hcp-title em{font-style:normal;color:#E4F03A}
                .hcp-sub{margin:12px auto 0;max-width:520px;line-height:1.6;color:#CFDDAE}
                .hcp-chips{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-top:20px}
                .hcp-chips span{border-radius:9999px;background:rgba(246,248,238,.1);border:1px solid rgba(246,248,238,.16);padding:7px 14px;font-size:13px;font-weight:700;color:#F6F8EE}

                .hcp-tabs{display:flex;justify-content:center;margin-top:30px}
                .hcp-tabs-in{display:inline-flex;gap:6px;border-radius:9999px;background:rgba(246,248,238,.1);border:1px solid rgba(246,248,238,.16);padding:5px}
                .hcp-tab{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:9999px;background:transparent;padding:11px 22px;font-family:inherit;font-size:14px;font-weight:800;color:#CFDDAE;cursor:pointer;transition:background .2s,color .2s}
                .hcp-tab.on{background:#E4F03A;color:#1F3F24}
                .hcp-tab small{border-radius:9999px;background:rgba(246,248,238,.16);padding:1px 8px;font-size:12px;font-weight:800}
                .hcp-tab.on small{background:rgba(31,63,36,.14)}

                .hcp-list{display:grid;gap:16px;margin-top:26px}
                .hcp-card{position:relative;overflow:hidden;display:grid;gap:18px;border-radius:28px;background:#fff;padding:24px 22px;box-shadow:0 22px 40px -26px rgba(0,0,0,.65)}
                @media (min-width:768px){.hcp-card{grid-template-columns:minmax(0,1fr) auto;align-items:center;padding:26px 30px}}
                .hcp-card::before{content:"";position:absolute;left:0;right:0;top:0;height:6px;background:linear-gradient(90deg,#2F5D34,#E4F03A,#2F5D34)}
                .hcp-card.is-off{opacity:.62}
                .hcp-name{margin:0;font-size:22px;font-weight:800;line-height:1.15;color:#1B2B1C;overflow-wrap:anywhere}
                .hcp-host{display:inline-flex;align-items:center;gap:6px;margin:6px 0 0;font-size:13px;font-weight:700;color:#2F5D34}
                .hcp-when{display:flex;flex-wrap:wrap;align-items:center;gap:6px 10px;margin:12px 0 0;font-size:14px;font-weight:700;color:rgba(27,43,28,.78)}
                .hcp-when svg{flex-shrink:0;color:#2F5D34}
                .hcp-when .sep{color:rgba(27,43,28,.25)}
                .hcp-incl{margin:10px 0 0;font-size:13.5px;line-height:1.6;color:rgba(27,43,28,.72)}
                .hcp-incl b{color:#1B2B1C}
                .hcp-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:14px}
                .hcp-tag{border-radius:9999px;background:#E5ECD2;padding:5px 12px;font-size:12px;font-weight:800;color:#1F3F24}
                .hcp-tag.court{background:#1F3F24;color:#E4F03A}

                .hcp-side{display:flex;flex-direction:column;gap:12px}
                @media (min-width:768px){.hcp-side{align-items:flex-end;min-width:240px;text-align:right}}
                .hcp-pill{display:inline-flex;align-items:center;gap:8px;align-self:flex-start;border-radius:9999px;background:#E5ECD2;padding:7px 14px;font-size:12.5px;font-weight:800;color:#1F3F24}
                @media (min-width:768px){.hcp-pill{align-self:flex-end}}
                .hcp-pill i{width:8px;height:8px;border-radius:9999px;background:#2F5D34;animation:hcp-pulse 2s ease-in-out infinite}
                .hcp-pill.live{background:#EEF6B0;color:#4D6B12}
                .hcp-pill.full{background:#FEE2E2;color:#B91C1C}
                .hcp-pill.full i{background:#EF4444;animation:none}
                .hcp-pill.off{background:#F1F1F1;color:#6B7280}
                .hcp-pill.off i{display:none}
                .hcp-price{margin:0;font-size:34px;font-weight:800;line-height:1;color:#2F5D34}
                .hcp-price small{margin-left:4px;font-size:14px;font-weight:700;color:rgba(27,43,28,.6)}
                .hcp-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:9999px;background:#1F3F24;padding:14px 26px;font-size:15px;font-weight:800;color:#E4F03A;text-decoration:none;transition:background .2s,transform .2s}
                .hcp-btn:hover{background:#2F5D34;transform:translateY(-2px)}
                .hcp-btn.off{background:#F1F1F1;color:#9CA3AF;cursor:not-allowed}
                .hcp-btn.off:hover{background:#F1F1F1;transform:none}
                @media (max-width:767px){.hcp-btn{width:100%}}

                .hcp-empty{margin-top:26px;border-radius:28px;background:#fff;padding:44px 24px;text-align:center;box-shadow:0 22px 40px -26px rgba(0,0,0,.65)}
                .hcp-empty p{margin:0}
                .hcp-empty .h{font-size:20px;font-weight:800;color:#1B2B1C}
                .hcp-empty .s{margin-top:8px;font-size:14px;line-height:1.6;color:rgba(27,43,28,.7)}
                .hcp-empty a{display:inline-flex;margin-top:18px;border-radius:9999px;background:#E4F03A;padding:13px 24px;font-size:14px;font-weight:800;color:#1B2B1C;text-decoration:none}
                .hcp-empty a:hover{background:#1F3F24;color:#E4F03A}

                @keyframes hcp-pulse{0%,100%{opacity:1}50%{opacity:.45}}
                @keyframes hcp-arc{0%{transform:translate(0,0) rotate(0)}50%{transform:translate(26px,-24px) rotate(140deg)}100%{transform:translate(52px,0) rotate(280deg)}}
                @media (prefers-reduced-motion:reduce){
                    .hcp-ball,.hcp-eyebrow i,.hcp-pill i{animation:none}
                }
            </style>

            <div class="hcp-deco" aria-hidden="true">
                <svg class="hcp-lines" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round">
                    <rect x="60" y="60" width="1080" height="480" rx="6"/>
                    <line x1="600" y1="60" x2="600" y2="540" stroke-dasharray="3 10"/>
                    <line x1="428" y1="60" x2="428" y2="540"/>
                    <line x1="772" y1="60" x2="772" y2="540"/>
                    <line x1="60" y1="300" x2="428" y2="300"/>
                    <line x1="772" y1="300" x2="1140" y2="300"/>
                </svg>
                <div class="hcp-ball a"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".85"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/><circle cx="50" cy="29" r="5"/><circle cx="50" cy="71" r="5"/></g></svg></div>
                <div class="hcp-ball b"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".35"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/></g></svg></div>
                <div class="hcp-ball c"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".35"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/></g></svg></div>
            </div>

            <div class="hcp-inner">

                {{-- Hero --}}
                <a href="{{ route('home') }}" class="hcp-back">&larr; Back to home</a>
                <div class="hcp-hero">
                    <span class="hcp-eyebrow"><i></i>Open play</span>
                    <h1 class="hc-display hcp-title">Join an <em>open play</em></h1>
                    <p class="hcp-sub">Pick a session, reserve your spot, and play. No group needed, you pay per player.</p>
                    <div class="hcp-chips">
                        <span>Pay per player</span>
                        <span>GCash &amp; Maya</span>
                        <span>Indoor courts</span>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="hcp-tabs">
                    <div class="hcp-tabs-in" role="tablist" aria-label="Sessions">
                        <button type="button" role="tab" class="hcp-tab" :class="{ 'on': tab === 'today' }" :aria-selected="tab === 'today'" @click="tab = 'today'">Today <small>{{ $today->count() }}</small></button>
                        <button type="button" role="tab" class="hcp-tab" :class="{ 'on': tab === 'upcoming' }" :aria-selected="tab === 'upcoming'" @click="tab = 'upcoming'">Upcoming <small>{{ $upcoming->count() }}</small></button>
                    </div>
                </div>

                {{-- Session lists --}}
                @foreach ($groups as $key => $list)
                    <div x-show="tab === '{{ $key }}'" x-cloak>
                        @if ($list->isEmpty())
                            <div class="hcp-empty">
                                <p class="h hc-display">{{ $key === 'today' ? 'No open play today' : 'No upcoming sessions yet' }}</p>
                                <p class="s">New sessions are added regularly. Check back soon, or book a court for your own group.</p>
                                <a href="{{ route('booking.create') }}">Book a court</a>
                            </div>
                        @else
                            <div class="hcp-list">
                                @foreach ($list as $s)
                                    @php $state = $s->state; @endphp
                                    <article class="hcp-card {{ $state === 'ended' ? 'is-off' : '' }}">
                                        <div>
                                            <h2 class="hc-display hcp-name">{{ $s->title }}</h2>
                                            <p class="hcp-host">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>
                                                Hosted by HomeCourt PickleHouse
                                            </p>

                                            <p class="hcp-when">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                                <span>{{ $s->startsAt()->format('D, M j') }}</span>
                                                <span class="sep">|</span>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span>{{ $s->startsAt()->format('g:i A') }} – {{ $s->endsAt()->format('g:i A') }}</span>
                                            </p>

                                            @if ($s->inclusions)
                                                <p class="hcp-incl"><b>Inclusions:</b> {{ $s->inclusions }}</p>
                                            @endif

                                            <div class="hcp-tags">
                                                <span class="hcp-tag">{{ $s->format }}</span>
                                                @if ($s->skill_level)
                                                    <span class="hcp-tag">{{ $s->skill_level }}</span>
                                                @endif
                                                @foreach ($s->courts as $c)
                                                    <span class="hcp-tag court">{{ $c->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="hcp-side">
                                            @if ($state === 'ended')
                                                <span class="hcp-pill off">Unavailable</span>
                                            @elseif ($state === 'full')
                                                <span class="hcp-pill full"><i></i>Full · {{ $s->capacity }} / {{ $s->capacity }} spots taken</span>
                                            @else
                                                <span class="hcp-pill {{ $state === 'live' ? 'live' : '' }}"><i></i>{{ $s->joined }} joined · {{ $s->left }} spots left · out of {{ $s->capacity }} slots</span>
                                            @endif

                                            <p class="hc-display hcp-price">₱{{ number_format((float) $s->price_per_player, 0) }}<small>/ player</small></p>
                                            <button type="button" @click="rosterId = {{ $s->id }}" style="border:0;background:none;padding:0;font-family:inherit;font-size:13px;font-weight:800;color:#2F5D34;text-decoration:underline;text-underline-offset:4px;cursor:pointer;">View roster</button>

                                            @if ($state === 'ended')
                                                <span class="hcp-btn off">Unavailable</span>
                                            @elseif ($state === 'full')
                                                <span class="hcp-btn off">Fully booked</span>
                                            @elseif (\Illuminate\Support\Facades\Route::has('open-play.join'))
                                                <a href="{{ route('open-play.join', $s) }}" class="hcp-btn">
                                                    {{ $state === 'live' ? 'Join now' : 'Join this session' }}
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                                </a>
                                            @else
                                                <span class="hcp-btn off">Joining opens soon</span>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        <style>
                .hcrm-overlay{position:fixed;inset:0;z-index:1000;display:flex;align-items:flex-end;justify-content:center;background:rgba(27,43,28,.6);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px)}
                @media (min-width:640px){.hcrm-overlay{align-items:center;padding:24px}}
                .hcrm-panel{position:relative;display:flex;flex-direction:column;width:100%;max-width:640px;max-height:90vh;max-height:90dvh;overflow:hidden;background:#fff;color:#1B2B1C;border-radius:28px 28px 0 0;box-shadow:0 30px 60px -20px rgba(0,0,0,.5);font-family:'Inter',system-ui,sans-serif}
                @media (min-width:640px){.hcrm-panel{border-radius:32px}}
                .hcrm-panel::before{content:"";position:absolute;left:0;right:0;top:0;height:6px;background:linear-gradient(90deg,#2F5D34,#E4F03A,#2F5D34)}
                .hcrm-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:26px 24px 14px}
                .hcrm-title{margin:0;font-size:22px;font-weight:800;line-height:1.15;color:#1B2B1C;overflow-wrap:anywhere}
                .hcrm-sub{margin:6px 0 0;font-size:13px;font-weight:700;line-height:1.5;color:rgba(27,43,28,.65)}
                .hcrm-x{flex:none;padding:8px;border:0;border-radius:9999px;background:transparent;color:rgba(27,43,28,.7);cursor:pointer}
                .hcrm-x:hover{background:#E5ECD2;color:#1B2B1C}
                .hcrm-body{overflow-y:auto;overscroll-behavior:contain;padding:0 24px 20px}
                .hcrm-avg{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;border-radius:20px;background:#F6F8EE;border:1px solid rgba(27,43,28,.08);padding:14px 18px}
                .hcrm-avg small{display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:rgba(27,43,28,.55)}
                .hcrm-avg b{display:block;margin-top:2px;font-size:22px;font-weight:800;color:#1B2B1C}
                .hcrm-chips{display:flex;flex-wrap:wrap;gap:6px}
                .hcrm-chips span{display:inline-flex;align-items:center;gap:6px;border-radius:9999px;background:#fff;border:1px solid rgba(27,43,28,.1);padding:5px 11px;font-size:12px;font-weight:800;color:#1B2B1C}
                .hcrm-chips i,.hcrm-lv i{display:inline-block;width:8px;height:8px;border-radius:9999px}
                .hcrm-list{display:grid;gap:10px;margin-top:14px;grid-template-columns:minmax(0,1fr)}
                @media (min-width:520px){.hcrm-list{grid-template-columns:repeat(2,minmax(0,1fr))}}
                .hcrm-pl{display:flex;align-items:center;gap:12px;border-radius:16px;background:#F6F8EE;border:1px solid rgba(27,43,28,.08);padding:10px 12px}
                .hcrm-av{display:flex;width:40px;height:40px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:#1F3F24;font-size:13px;font-weight:800;color:#E4F03A}
                .hcrm-pl b{display:block;font-size:14px;font-weight:800;color:#1B2B1C}
                .hcrm-lv{display:flex;align-items:center;gap:6px;margin-top:2px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:rgba(27,43,28,.6)}
                .hcrm-plus{margin-left:auto;border-radius:9999px;background:#E5ECD2;padding:3px 9px;font-size:11px;font-weight:800;color:#1F3F24}
                .hcrm-empty{border-radius:22px;border:2px dashed rgba(47,93,52,.25);background:#F6F8EE;padding:30px 20px;text-align:center}
                .hcrm-empty p{margin:0}
                .hcrm-empty .h{font-size:18px;font-weight:800;color:#1B2B1C}
                .hcrm-empty .s{margin-top:6px;font-size:13px;color:rgba(27,43,28,.65)}
                .hcrm-pend{margin:14px 0 0;font-size:13px;line-height:1.6;color:rgba(27,43,28,.65)}
                .hcrm-foot{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;padding:14px 24px;border-top:1px solid rgba(27,43,28,.1)}
                .hcrm-join{display:inline-flex;align-items:center;justify-content:center;border-radius:9999px;background:#1F3F24;padding:12px 24px;font-size:14px;font-weight:800;color:#E4F03A;text-decoration:none;transition:background .2s}
                .hcrm-join:hover{background:#2F5D34}
                .hcrm-join.off{background:#F1F1F1;color:#9CA3AF;cursor:not-allowed}
                .hcrm-close{border:0;border-radius:9999px;background:transparent;padding:12px 18px;font-family:inherit;font-size:14px;font-weight:800;color:rgba(27,43,28,.7);cursor:pointer}
                .hcrm-close:hover{color:#1B2B1C}

                .hcp-back{display:inline-flex;align-items:center;gap:6px;margin-bottom:22px;border-radius:9999px;background:rgba(246,248,238,.12);border:1px solid rgba(246,248,238,.2);padding:9px 18px;font-size:13px;font-weight:800;color:#F6F8EE;text-decoration:none;transition:background .2s,color .2s}
                .hcp-back:hover{background:#E4F03A;border-color:#E4F03A;color:#1F3F24}
            </style>

            <template x-teleport="body">
                <div class="hcrm-overlay" x-show="rosterId !== null" x-cloak style="display:none;" x-transition.opacity @click.self="rosterId = null">
                    @foreach ($today->concat($upcoming)->unique('id') as $s)
                        @php
                            $rEntries = $s->entries;
                            $rAvg     = \App\Support\SkillLevels::average($rEntries);
                            $rCounts  = \App\Support\SkillLevels::counts($rEntries);
                            $rPending = (int) ($s->pending_players ?? 0);
                            $rState   = $s->state;
                        @endphp

                        <div class="hcrm-panel" role="dialog" aria-modal="true" aria-label="Roster for {{ $s->title }}" x-show="rosterId === {{ $s->id }}" x-cloak style="display:none;" x-transition>
                            <div class="hcrm-head">
                                <div>
                                    <h2 class="hc-display hcrm-title">{{ $s->title }}</h2>
                                    <p class="hcrm-sub">
                                        {{ $s->startsAt()->format('D, M j') }} &middot; {{ $s->startsAt()->format('g:i A') }} – {{ $s->endsAt()->format('g:i A') }}
                                        <br>{{ $s->joined }} joined &middot; {{ $s->left }} spots left &middot; ₱{{ number_format((float) $s->price_per_player, 0) }} / player
                                    </p>
                                </div>
                                <button type="button" class="hcrm-x" @click="rosterId = null" aria-label="Close roster"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                            </div>

                            <div class="hcrm-body">
                                @if ($rEntries->isEmpty())
                                    <div class="hcrm-empty">
                                        <p class="h hc-display">No confirmed players yet</p>
                                        <p class="s">Players appear here once their payment is verified. Be the first!</p>
                                    </div>
                                @else
                                    <div class="hcrm-avg">
                                        <div>
                                            <small>Roster skill average</small>
                                            <b class="hc-display">{{ $rAvg ?? 'Not set' }}</b>
                                        </div>
                                        <div class="hcrm-chips">
                                            @foreach ($rCounts as $lvl => $n)
                                                <span><i style="background:{{ \App\Support\SkillLevels::color($lvl) }}"></i>{{ $lvl }} {{ $n }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="hcrm-list">
                                        @foreach ($rEntries as $e)
                                            @php
                                                $pn       = $e->public_name;
                                                $initials = collect(explode(' ', $pn))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
                                            @endphp
                                            <div class="hcrm-pl">
                                                <span class="hcrm-av">{{ $initials }}</span>
                                                <div>
                                                    <b>{{ $pn }}</b>
                                                    <div class="hcrm-lv"><i style="background:{{ \App\Support\SkillLevels::color($e->skill_level) }}"></i>{{ $e->skill_level ?? 'Not set' }}</div>
                                                </div>
                                                @if ($e->players > 1)
                                                    <span class="hcrm-plus">+{{ $e->players - 1 }} {{ $e->players - 1 === 1 ? 'guest' : 'guests' }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($rPending > 0)
                                    <p class="hcrm-pend">{{ $rPending }} more {{ $rPending === 1 ? 'player is' : 'players are' }} waiting for payment verification and will appear here once confirmed.</p>
                                @endif
                            </div>

                            <div class="hcrm-foot">
                                @if (in_array($rState, ['open', 'live'], true))
                                    <a href="{{ route('open-play.join', $s) }}" class="hcrm-join">{{ $rState === 'live' ? 'Join now' : 'Join this session' }}</a>
                                @else
                                    <span class="hcrm-join off">{{ $rState === 'full' ? 'Fully booked' : 'Unavailable' }}</span>
                                @endif
                                <button type="button" class="hcrm-close" @click="rosterId = null">Close</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </template>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="hcz">
        <style>
            .hcz{position:relative;overflow:hidden;border-radius:40px 40px 0 0;color:#CFDDAE;
                background-color:#1F3F24;
                background-image:radial-gradient(700px 380px at 100% 0%, rgba(228,240,58,.16), transparent 65%),radial-gradient(640px 380px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
            @media (min-width:768px){.hcz{border-radius:64px 64px 0 0}}
            .hcz-mark{position:absolute;left:50%;bottom:-.14em;z-index:1;transform:translateX(-50%);font-size:clamp(64px,15vw,210px);font-weight:800;line-height:.8;letter-spacing:-.02em;white-space:nowrap;color:rgba(246,248,238,.05);pointer-events:none;user-select:none}
            .hcz-inner{position:relative;z-index:2;margin:0 auto;max-width:1240px;padding:0 24px}
            .hcz-cta{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:22px;padding:52px 0 44px;border-bottom:1px solid rgba(246,248,238,.14)}
            .hcz-cta h2{margin:0;font-size:clamp(28px,4.4vw,46px);font-weight:800;line-height:1.05;color:#F6F8EE}
            .hcz-cta h2 em{font-style:normal;color:#E4F03A}
            .hcz-cta p{margin:12px 0 0;max-width:430px;line-height:1.6;color:#CFDDAE}
            .hcz-fb{display:inline-flex;align-items:center;gap:10px;border-radius:9999px;background:#E4F03A;padding:16px 28px;font-size:16px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s}
            .hcz-fb:hover{background:#fff}
            .hcz-bar{position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px 24px;padding:22px 0 30px;font-size:13px}
            .hcz-copy{margin:0;color:#CFDDAE}
            .hcz-copy b{color:#F6F8EE}
            .hcz-legal{display:flex;flex-wrap:wrap;align-items:center;gap:10px 22px}
            .hcz-legal a{font-size:13px;font-weight:600;color:#F6F8EE;text-decoration:none;transition:color .2s}
            .hcz-legal a:hover{color:#E4F03A;text-decoration:underline}
            .hcz-round{display:flex;width:42px;height:42px;align-items:center;justify-content:center;border-radius:9999px;border:2px solid rgba(246,248,238,.3);background:transparent;color:#F6F8EE;cursor:pointer;transition:background .2s,color .2s,border-color .2s}
            .hcz-round:hover{background:#E4F03A;border-color:#E4F03A;color:#1B2B1C}
        </style>

        <div class="hcz-mark hc-display" aria-hidden="true">HOMECOURT</div>

        <div class="hcz-inner">
            <div class="hcz-cta">
                <div>
                    <h2 class="hc-display">Questions about <em>open play?</em></h2>
                    <p>Message our Facebook page and we'll help you pick the right session.</p>
                </div>
                <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="hcz-fb">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Message us on Facebook
                </a>
            </div>
            @include('partials.footer-map')

            <div class="hcz-bar">
                <p class="hcz-copy">© {{ date('Y') }} <b>HomeCourt PickleHouse</b>. All rights reserved.</p>
                <div class="hcz-legal">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('booking.create') }}">Book a court</a>
                    <button type="button" class="hcz-round" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" aria-label="Back to top">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>