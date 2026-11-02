<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join {{ $session->title }} | HomeCourt PickleHouse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @include('partials.op-styles')

    <style>
        .hcj-top{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px}
        .hcj-cap{margin:0;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:rgba(27,43,28,.6)}
        .hcj-price{margin:4px 0 0;font-size:40px;font-weight:800;line-height:1;color:#2F5D34}
        .hcj-price small{margin-left:4px;font-size:15px;font-weight:700;color:rgba(27,43,28,.6)}
        .hcj-pill{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:#E5ECD2;padding:8px 16px;font-size:13px;font-weight:800;color:#1F3F24}
        .hcj-pill i{width:8px;height:8px;border-radius:9999px;background:#2F5D34;animation:hcn-pulse 2s ease-in-out infinite}
        .hcj-pill.live{background:#EEF6B0;color:#4D6B12}
        .hcj-pill.full{background:#FEE2E2;color:#B91C1C}
        .hcj-pill.full i,.hcj-pill.off i{animation:none}
        .hcj-pill.off{background:#F1F1F1;color:#6B7280}
        .hcj-when{display:flex;flex-wrap:wrap;gap:6px 14px;margin:18px 0 0;font-size:15px;font-weight:700;color:rgba(27,43,28,.8)}
        .hcj-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:14px}
        .hcj-tag{border-radius:9999px;background:#E5ECD2;padding:5px 12px;font-size:12px;font-weight:800;color:#1F3F24}
        .hcj-tag.court{background:#1F3F24;color:#E4F03A}
        .hcj-incl{margin:14px 0 0;font-size:14px;line-height:1.6;color:rgba(27,43,28,.75)}
        .hcj-incl b{color:#1B2B1C}

        .hcj-step{display:inline-flex;align-items:center;gap:14px}
        .hcj-step button{display:flex;width:46px;height:46px;align-items:center;justify-content:center;border-radius:9999px;border:2px solid rgba(47,93,52,.3);background:#fff;color:#2F5D34;font-size:24px;font-weight:700;line-height:1;cursor:pointer;transition:background .2s,color .2s}
        .hcj-step button:hover{background:#2F5D34;color:#fff}
        .hcj-step b{min-width:48px;text-align:center;font-size:26px;font-weight:800;color:#1B2B1C}
        .hcj-hint{margin:8px 0 0;font-size:12.5px;color:rgba(27,43,28,.6)}

        .hcj-pm{display:grid;gap:12px;grid-template-columns:repeat(2,minmax(0,1fr))}
        .hcj-pm button{position:relative;display:flex;flex-direction:column;align-items:flex-start;gap:12px;border-radius:22px;border:2px solid rgba(27,43,28,.12);background:#fff;padding:16px;text-align:left;cursor:pointer;transition:transform .2s,border-color .2s,background .2s,box-shadow .2s}
        .hcj-pm button:hover{transform:translateY(-3px);border-color:#2F5D34}
        .hcj-pm .logo{display:flex;width:44px;height:44px;align-items:center;justify-content:center;border-radius:14px;font-size:20px;font-weight:800;color:#fff}
        .hcj-pm .gcash .logo{background:#0B63F6}
        .hcj-pm .maya .logo{background:#00A859}
        .hcj-pm b{display:block;font-size:16px;font-weight:800;color:#1B2B1C}
        .hcj-pm small{display:block;margin-top:2px;font-size:12px;font-weight:600;line-height:1.4;color:rgba(27,43,28,.6)}
        .hcj-pm .tick{position:absolute;right:12px;top:12px;display:none;width:24px;height:24px;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24}
        .hcj-pm button.is-on{border-color:#1F3F24;background:#F3F8E4;box-shadow:0 14px 24px -18px rgba(31,63,36,.8)}
        .hcj-pm button.is-on .tick{display:flex}

        .hcj-rows{margin-top:6px;font-size:14px}
        .hcj-row{display:flex;justify-content:space-between;gap:16px;padding:11px 0;border-bottom:1px solid rgba(27,43,28,.08)}
        .hcj-row span{color:rgba(27,43,28,.6)}
        .hcj-row b{text-align:right;font-weight:800;color:#1B2B1C}
        .hcj-total{margin-top:18px;border-radius:22px;background:#1F3F24;padding:16px 18px;background-image:radial-gradient(260px 140px at 100% 0%, rgba(228,240,58,.22), transparent 65%)}
        .hcj-total .t{font-size:13px;font-weight:700;color:#CFDDAE}
        .hcj-total .v{margin-top:2px;font-size:38px;font-weight:800;line-height:1.1;color:#E4F03A}
        .hcj-agree{display:flex;cursor:pointer;align-items:flex-start;gap:12px;margin-top:16px;border-radius:20px;border:1px solid #FECACA;background:#FEF2F2;padding:14px;font-size:13px;line-height:1.6;color:rgba(27,43,28,.82)}
        .hcj-agree strong{color:#B91C1C}
        .hcj-submit{display:flex;width:100%;align-items:center;justify-content:center;gap:8px;margin-top:16px;border:0;border-radius:9999px;background:#1F3F24;padding:16px;font-family:inherit;font-size:16px;font-weight:800;color:#E4F03A;cursor:pointer;transition:background .2s,transform .2s}
        .hcj-submit:hover:not(:disabled){background:#2F5D34;transform:translateY(-2px)}
        .hcj-submit:disabled{opacity:.4;cursor:not-allowed}
        .hcj-note{margin:14px 0 0;text-align:center;font-size:12px;line-height:1.6;color:rgba(27,43,28,.65)}
        .hcj-note button{border:0;background:none;padding:0;font-family:inherit;font-size:12px;font-weight:700;color:#2F5D34;text-decoration:underline;text-underline-offset:2px;cursor:pointer}
        .hcj-blocked{text-align:center}
                .hcj-who{display:grid;gap:12px;grid-template-columns:repeat(2,minmax(0,1fr))}
        .hcj-who button{display:flex;flex-direction:column;align-items:flex-start;gap:2px;border-radius:18px;border:2px solid rgba(27,43,28,.12);background:#fff;padding:14px 16px;text-align:left;font-family:inherit;cursor:pointer;transition:border-color .2s,background .2s}
        .hcj-who button:hover:not(:disabled){border-color:#2F5D34}
        .hcj-who button:disabled{opacity:.45;cursor:not-allowed}
        .hcj-who b{font-size:15px;font-weight:800;color:#1B2B1C}
        .hcj-who small{font-size:12px;font-weight:600;color:rgba(27,43,28,.6)}
        .hcj-who button.is-on{border-color:#1F3F24;background:#F3F8E4}
        .hcj-skill{display:grid;gap:10px;grid-template-columns:minmax(0,1fr)}
        @media (min-width:640px){.hcj-skill{grid-template-columns:repeat(2,minmax(0,1fr))}}
        .hcj-skill button{display:flex;align-items:flex-start;gap:12px;border-radius:18px;border:2px solid rgba(27,43,28,.12);background:#fff;padding:12px 14px;text-align:left;font-family:inherit;cursor:pointer;transition:border-color .2s,background .2s,transform .2s}
        .hcj-skill button:hover{transform:translateY(-2px);border-color:#2F5D34}
        .hcj-skill button i{flex:none;width:12px;height:12px;margin-top:4px;border-radius:9999px}
        .hcj-skill b{display:block;font-size:14px;font-weight:800;color:#1B2B1C}
        .hcj-skill small{display:block;margin-top:2px;font-size:12px;font-weight:600;line-height:1.4;color:rgba(27,43,28,.6)}
        .hcj-skill button.is-on{border-color:#1F3F24;background:#1F3F24}
        .hcj-skill button.is-on b{color:#fff}
        .hcj-skill button.is-on small{color:#CFDDAE}
        .hcj-blocked p{margin:0}
    </style>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=1">
</head>
<body>

    @include('partials.op-header')

    <main>
        @php
            $state        = $session->state;
            $price        = (float) $session->price_per_player;
            $pm           = in_array(old('payment_method'), ['GCash', 'Maya'], true) ? old('payment_method') : 'GCash';
            $startPlayers = max(1, min((int) old('players', 1), $maxPlayers));
            $skillOld     = in_array(old('skill_level'), \App\Support\SkillLevels::names(), true) ? old('skill_level') : '';
        @endphp

        <section class="hcn"
                 x-data="{
                    players: {{ $startPlayers }},
                     mode: '{{ $startPlayers > 1 ? 'group' : 'solo' }}',
                     skill: '{{ $skillOld }}',
                     max: {{ $maxPlayers }},
                     price: {{ $price }},
                     paymentMethod: '{{ $pm }}',
                     agreed: false,
                     submitting: false,
                     get total() { return this.players * this.price },
                     get fmt() { return '₱' + this.total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }
                 }">

            <div class="hcn-deco" aria-hidden="true">
                <svg class="hcn-lines" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#F6F8EE" stroke-width="3" stroke-linecap="round">
                    <rect x="60" y="60" width="1080" height="480" rx="6"/>
                    <line x1="600" y1="60" x2="600" y2="540" stroke-dasharray="3 10"/>
                    <line x1="428" y1="60" x2="428" y2="540"/>
                    <line x1="772" y1="60" x2="772" y2="540"/>
                    <line x1="60" y1="300" x2="428" y2="300"/>
                    <line x1="772" y1="300" x2="1140" y2="300"/>
                </svg>
                <div class="hcn-ball a"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".85"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/><circle cx="50" cy="29" r="5"/><circle cx="50" cy="71" r="5"/></g></svg></div>
                <div class="hcn-ball b"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".35"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/></g></svg></div>
                <div class="hcn-ball c"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".35"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="60" r="5"/><circle cx="33" cy="60" r="5"/><circle cx="33" cy="40" r="5"/><circle cx="67" cy="40" r="5"/></g></svg></div>
            </div>

            <div class="hcn-inner">

                {{-- Hero --}}
                <a href="{{ route('open-play.index') }}" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:22px;border-radius:9999px;background:rgba(246,248,238,.12);border:1px solid rgba(246,248,238,.2);padding:9px 18px;font-size:13px;font-weight:800;color:#F6F8EE;text-decoration:none;">&larr; All sessions</a>
                <div class="hcn-hero">
                    <span class="hcn-eyebrow"><i></i>Join open play</span>
                    <h1 class="hc-display hcn-title">{{ $session->title }}</h1>
                    <p class="hcn-sub">Reserve your spot below. You pay per player, and your spot is confirmed once we verify your payment.</p>
                </div>

                {{-- Session details --}}
                <div class="hcn-card">
                    <div class="hcj-top">
                        <div>
                            <p class="hcj-cap">Price</p>
                            <p class="hc-display hcj-price">₱{{ number_format($price, 0) }}<small>/ player</small></p>
                        </div>

                        @if ($state === 'full')
                            <span class="hcj-pill full"><i></i>Full · {{ $session->capacity }} / {{ $session->capacity }} spots taken</span>
                        @elseif ($state === 'ended' || $state === 'cancelled')
                            <span class="hcj-pill off"><i></i>{{ $state === 'cancelled' ? 'Cancelled' : 'Unavailable' }}</span>
                        @else
                            <span class="hcj-pill {{ $state === 'live' ? 'live' : '' }}"><i></i>{{ $session->joined }} joined · {{ $session->left }} spots left · out of {{ $session->capacity }} slots</span>
                        @endif
                    </div>

                    <p class="hcj-when">
                        <span>{{ $session->startsAt()->format('D, M j, Y') }}</span>
                        <span>{{ $session->startsAt()->format('g:i A') }} – {{ $session->endsAt()->format('g:i A') }}</span>
                    </p>

                    <div class="hcj-tags">
                        <span class="hcj-tag">{{ $session->format }}</span>
                        @if ($session->skill_level)
                            <span class="hcj-tag">{{ $session->skill_level }}</span>
                        @endif
                        @foreach ($session->courts as $c)
                            <span class="hcj-tag court">{{ $c->name }}</span>
                        @endforeach
                    </div>

                    @if ($session->inclusions)
                        <p class="hcj-incl"><b>Inclusions:</b> {{ $session->inclusions }}</p>
                    @endif
                </div>

                @if (! $canJoin)
                    <div class="hcn-card hcj-blocked">
                        <p class="hc-display" style="font-size:22px;font-weight:800;color:#1B2B1C;">{{ $blocked }}</p>
                        <div class="hcn-btns" style="margin-top:20px;">
                            <a href="{{ route('open-play.index') }}" class="hcn-btn main">See other sessions</a>
                            <a href="{{ route('booking.create') }}" class="hcn-btn main" style="background:#1F3F24;color:#E4F03A;">Book a court</a>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('open-play.store', $session) }}" @submit="submitting = true" novalidate>
                        @csrf
                        <input type="hidden" name="payment_method" :value="paymentMethod" value="{{ $pm }}">
                        <input type="hidden" name="players" :value="players" value="{{ $startPlayers }}">
                        <input type="hidden" name="skill_level" :value="skill" value="{{ $skillOld }}">

                        @if ($errors->any())
                            <div class="hcn-msg" role="alert">
                                <strong>Please fix the following:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Your details --}}
                        <div class="hcn-card">
                            <div class="hcn-head">
                                <span class="hcn-num">1</span>
                                <h2 class="hc-display">Your details</h2>
                            </div>

                            <div class="hcn-grid two">
                                <div>
                                    <label class="hcn-l" for="name">Facebook Name</label>
                                    <input class="hcn-in" id="name" name="name" type="text" maxlength="100" autocomplete="name" required placeholder="Juan Dela Cruz" value="{{ old('name') }}">
                                </div>
                                <div>
                                    <label class="hcn-l" for="contact_number">Contact Number</label>
                                    <input class="hcn-in" id="contact_number" name="contact_number" type="tel" inputmode="numeric" maxlength="20" autocomplete="tel" required placeholder="09XXXXXXXXX" value="{{ old('contact_number') }}"
                                           oninput="this.value = this.value.replace(/[^0-9+\-\s]/g, '')">
                                </div>
                            </div>

                                                        <div style="margin-top:22px;">
                                <span class="hcn-l">Booking for</span>
                                <div class="hcj-who">
                                    <button type="button" :class="{ 'is-on': mode === 'solo' }" @click="mode = 'solo'; players = 1">
                                        <b>Just me</b><small>1 player</small>
                                    </button>
                                    <button type="button" :class="{ 'is-on': mode === 'group' }" :disabled="max < 2" @click="mode = 'group'; if (players < 2) players = 2">
                                        <b>Me + others</b><small>Add friends, pay together</small>
                                    </button>
                                </div>

                                <div x-show="mode === 'group'" x-cloak style="margin-top:16px;">
                                    <span class="hcn-l">Total players (including you)</span>
                                    <div class="hcj-step">
                                        <button type="button" @click="players = Math.max(2, players - 1)" aria-label="Fewer players">−</button>
                                        <b x-text="players">{{ $startPlayers }}</b>
                                        <button type="button" @click="players = Math.min(max, players + 1)" aria-label="More players">+</button>
                                    </div>
                                    <p class="hcj-hint">Up to {{ $maxPlayers }} players for this entry. Everyone is counted at the skill level you pick below.</p>
                                </div>
                            </div>

                            <div style="margin-top:22px;">
                                <span class="hcn-l">Your skill level</span>
                                <div class="hcj-skill">
                                    @foreach (\App\Support\SkillLevels::LEVELS as $levelName => $levelMeta)
                                        <button type="button" :class="{ 'is-on': skill === '{{ $levelName }}' }" @click="skill = '{{ $levelName }}'">
                                            <i style="background:{{ $levelMeta['color'] }}"></i>
                                            <span><b>{{ $levelName }}</b><small>{{ $levelMeta['hint'] }}</small></span>
                                        </button>
                                    @endforeach
                                </div>
                                <p class="hcj-hint" x-show="!skill">Please pick the level that fits you best.</p>
                            </div>

                            <div style="margin-top:22px;">
                                <label class="hcn-l" for="note">Note for the organizer (optional)</label>
                                <input class="hcn-in" id="note" name="note" type="text" maxlength="255" value="{{ old('note') }}">

                                <label style="display:flex;align-items:flex-start;gap:10px;margin-top:14px;cursor:pointer;font-size:13px;line-height:1.5;color:rgba(27,43,28,.8);">
                                    <input type="checkbox" name="hide_name" value="1" style="width:1rem;height:1rem;flex:none;margin-top:2px;accent-color:#2F5D34" @checked(old('hide_name'))>
                                    <span>Hide my name on the public roster (it will show as "Player").</span>
                                </label>
                            </div>
                        </div>

                        {{-- Payment method --}}
                        <div class="hcn-card">
                            <div class="hcn-head">
                                <span class="hcn-num">2</span>
                                <h2 class="hc-display">Payment method</h2>
                            </div>

                            <div class="hcj-pm" role="radiogroup" aria-label="Mode of payment">
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

                            <p class="hcj-hint" style="margin-top:14px;">You pay after submitting. The account numbers and QR codes appear on the next screen.</p>
                        </div>

                        {{-- Review --}}
                        <div class="hcn-card">
                            <div class="hcn-head">
                                <span class="hcn-num">3</span>
                                <h2 class="hc-display">Review &amp; join</h2>
                            </div>

                            <div class="hcj-rows">
                                <div class="hcj-row"><span>Session</span><b>{{ $session->title }}</b></div>
                                <div class="hcj-row"><span>Date</span><b>{{ $session->startsAt()->format('M j, Y') }}</b></div>
                                <div class="hcj-row"><span>Time</span><b>{{ $session->startsAt()->format('g:i A') }} – {{ $session->endsAt()->format('g:i A') }}</b></div>
                                <div class="hcj-row"><span>Players</span><b><span x-text="players"></span> × ₱{{ number_format($price, 0) }}</b></div>
                                <div class="hcj-row"><span>Skill level</span><b x-text="skill || '—'"></b></div>
                                <div class="hcj-row" style="border-bottom:0"><span>Payment</span><b x-text="paymentMethod"></b></div>
                            </div>

                            <div class="hcj-total">
                                <div class="t">Total Price</div>
                                <div class="v hc-display" x-text="fmt">₱{{ number_format($price, 2) }}</div>
                            </div>

                            <label class="hcj-agree">
                                <input type="checkbox" name="agree_terms" value="1" x-model="agreed" style="width:1rem;height:1rem;flex:none;margin-top:3px;accent-color:#2F5D34">
                                <span>I understand that joining is <strong>non-refundable and non-cancellable</strong> once submitted, and that my spot is confirmed only after my payment is verified.</span>
                            </label>

                            <button type="submit" class="hcj-submit" :disabled="submitting || !agreed || !skill">
                                <span x-show="!submitting">Join this session</span>
                                <span x-show="submitting" x-cloak>Submitting...</span>
                            </button>

                            <p class="hcj-note">
                                Please read our
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-house-rules'))">House Rules</button>
                                and
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-privacy'))">Privacy Policy</button>.
                                We use your name and contact number only to manage your spot.
                            </p>
                        </div>
                    </form>
                @endif

            </div>
        </section>
    </main>

    @include('partials.op-footer')
    @include('partials.op-legal')

</body>
</html>