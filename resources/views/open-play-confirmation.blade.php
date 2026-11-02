<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Open Play Request | HomeCourt PickleHouse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @include('partials.op-styles')

    <style>
        .hcq-check{position:relative;display:inline-flex;width:76px;height:76px;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24;animation:hcq-pop .6s cubic-bezier(.2,.9,.3,1.4) both}
        .hcq-check::before,.hcq-check::after{content:"";position:absolute;inset:0;border-radius:9999px;border:2px solid #E4F03A;animation:hcq-ring 2.6s ease-out infinite}
        .hcq-check::before{animation-delay:1.3s}
        .hcq-check path{stroke-dasharray:40;stroke-dashoffset:40;animation:hcq-draw .6s .35s ease forwards}
        .hcq-check.bad{background:#F87171;color:#fff}
        .hcq-check.bad::before,.hcq-check.bad::after{border-color:#F87171}
        .hcq-title.bad em{color:#FCA5A5}

        .hcq-ticket{position:relative;margin:28px auto 0;max-width:560px;border-radius:28px;background:#fff;padding:26px 24px 22px;text-align:center;box-shadow:0 22px 40px -26px rgba(0,0,0,.65)}
        .hcq-cap{margin:0;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:rgba(27,43,28,.6)}
        .hcq-ref{margin:8px 0 0;word-break:break-all;font-size:clamp(34px,7vw,52px);font-weight:800;letter-spacing:.04em;line-height:1.05;color:#2F5D34}
        .hcq-copy{margin-top:14px;display:inline-flex;align-items:center;justify-content:center;border-radius:9999px;border:2px solid rgba(47,93,52,.35);background:#fff;padding:9px 18px;font-family:inherit;font-size:13px;font-weight:800;color:#2F5D34;cursor:pointer;transition:background .2s,color .2s,border-color .2s}
        .hcq-copy:hover{background:#2F5D34;border-color:#2F5D34;color:#fff}
        .hcq-copy.sm{margin-top:10px;padding:6px 14px;font-size:12px}
        .hcq-perf{position:relative;margin:22px -24px 18px;border-top:2px dashed rgba(27,43,28,.18)}
        .hcq-perf::before,.hcq-perf::after{content:"";position:absolute;top:-13px;width:24px;height:24px;border-radius:9999px;background:#1F3F24}
        .hcq-perf::before{left:-12px}
        .hcq-perf::after{right:-12px}
        .hcq-stats{display:flex;flex-wrap:wrap;justify-content:center;gap:12px 36px}
        .hcq-stat span{display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:rgba(27,43,28,.55)}
        .hcq-stat b{display:inline-flex;align-items:center;gap:8px;margin-top:4px;font-size:20px;font-weight:800;color:#1B2B1C}
        .hcq-stat b i{display:inline-block;width:9px;height:9px;border-radius:9999px}
        .hcq-stat b.pend{font-size:15px;color:#B45309}
        .hcq-stat b.pend i{background:#F59E0B;animation:hcn-pulse 2s ease-in-out infinite}
        .hcq-stat b.ok{font-size:15px;color:#15803D}
        .hcq-stat b.ok i{background:#22C55E}
        .hcq-stat b.bad{font-size:15px;color:#B91C1C}
        .hcq-stat b.bad i{background:#EF4444}
        .hcq-hint{margin:16px 0 0;font-size:13px;color:rgba(27,43,28,.65)}
        .hcq-refresh{margin-left:8px;border:0;background:none;padding:0;font-family:inherit;font-size:13px;font-weight:800;color:#2F5D34;text-decoration:underline;text-underline-offset:4px;cursor:pointer}

        .hcq-dl{display:grid;gap:20px 32px;grid-template-columns:minmax(0,1fr)}
        @media (min-width:640px){.hcq-dl{grid-template-columns:repeat(2,minmax(0,1fr))}}
        .hcq-dl dt{font-size:13px;font-weight:700;color:rgba(27,43,28,.6)}
        .hcq-dl dd{margin:4px 0 0;font-weight:800;word-break:break-word;color:#1B2B1C}
        .hcq-slotchip{display:inline-flex;align-items:center;border-radius:9999px;background:#E5ECD2;padding:7px 14px;font-size:12px;font-weight:800;color:#1F3F24}

        .hcq-amount{display:flex;align-items:center;justify-content:space-between;gap:12px;border-radius:22px;background:#1F3F24;padding:16px 20px;background-image:radial-gradient(280px 140px at 100% 0%, rgba(228,240,58,.22), transparent 65%)}
        .hcq-amount span{font-size:13px;font-weight:700;color:#CFDDAE}
        .hcq-amount b{font-size:32px;font-weight:800;line-height:1.1;color:#E4F03A}
        .hcq-tabs{display:inline-flex;gap:6px;margin-top:20px;border-radius:9999px;background:#E5ECD2;padding:5px}
        .hcq-tab{border:0;border-radius:9999px;background:transparent;padding:10px 22px;font-family:inherit;font-size:14px;font-weight:800;color:rgba(27,43,28,.7);cursor:pointer;transition:background .2s,color .2s}
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
        .hcq-accnum{margin:4px 0 0;font-size:28px;font-weight:800;letter-spacing:.02em;color:#1F3F24}
        .hcq-accname{margin:4px 0 0;font-weight:800;color:#1B2B1C}
        .hcq-acc .hcq-cap{margin-top:12px}
        .hcq-note{margin:16px 0 0;font-size:12.5px;line-height:1.6;color:rgba(27,43,28,.68)}
        .hcq-fb{display:flex;width:100%;align-items:center;justify-content:center;gap:10px;margin-top:18px;border-radius:9999px;background:#1F3F24;padding:16px 22px;font-size:15px;font-weight:800;color:#E4F03A;text-decoration:none;transition:background .2s,transform .2s}
        .hcq-fb:hover{background:#2F5D34;transform:translateY(-2px)}
        .hcq-go{display:flex;width:100%;align-items:center;justify-content:center;gap:10px;margin-top:18px;border-radius:9999px;background:#E4F03A;padding:16px 22px;font-size:15px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s}
        .hcq-go:hover{background:#1F3F24;color:#E4F03A}
        .hcq-reason{margin:14px 0 0;border-radius:18px;background:#FEF2F2;border:1px solid #FECACA;padding:12px 16px;font-size:14px;line-height:1.6;color:#7F1D1D}

        @keyframes hcq-pop{from{transform:scale(0)}to{transform:scale(1)}}
        @keyframes hcq-ring{0%{transform:scale(1);opacity:.8}100%{transform:scale(1.9);opacity:0}}
        @keyframes hcq-draw{to{stroke-dashoffset:0}}
        @media (prefers-reduced-motion:reduce){
            .hcq-check,.hcq-check::before,.hcq-check::after,.hcq-stat b.pend i{animation:none}
            .hcq-check path{animation:none;stroke-dashoffset:0}
        }
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
            $session = $entry->session;
            $method  = $entry->payment_method === 'Maya' ? 'maya' : 'gcash';

            $state = ($session->status === 'Cancelled' || $entry->entry_status === 'Cancelled') ? 'cancelled'
                : (($entry->payment_status === 'Rejected' || $entry->entry_status === 'Rejected') ? 'rejected'
                : ($entry->payment_status === 'Verified' ? 'confirmed' : 'pending'));
            $isBad = in_array($state, ['rejected', 'cancelled'], true);

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

        <section class="hcn">
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
                <div class="hcn-ball c"><svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="48" fill="currentColor"/><g fill="#1F3F24" opacity=".35"><circle cx="50" cy="50" r="5"/><circle cx="67" cy="40" r="5"/><circle cx="33" cy="60" r="5"/></g></svg></div>
            </div>

            <div class="hcn-inner" style="max-width:960px;">

                {{-- Hero --}}
                <div class="hcn-hero">
                    <span class="hcq-check {{ $isBad ? 'bad' : '' }}">
                        @if ($isBad)
                            <svg width="34" height="34" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 6l12 12M18 6L6 18"></path></svg>
                        @else
                            <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </span>

                    <h1 class="hc-display hcn-title hcq-title {{ $isBad ? 'bad' : '' }}" style="margin-top:22px;">
                        @if ($state === 'confirmed')
                            You're <em>in!</em>
                        @elseif ($state === 'rejected')
                            Entry <em>Not Approved</em>
                        @elseif ($state === 'cancelled')
                            Entry <em>Cancelled</em>
                        @else
                            Spot Request <em>Submitted!</em>
                        @endif
                    </h1>

                    <p class="hcn-sub">
                        @if ($state === 'confirmed')
                            Your payment is verified and your spot is reserved. See you on the court!
                        @elseif ($state === 'rejected')
                            We couldn't approve this entry. Please message us on Facebook and we'll help you sort it out.
                        @elseif ($state === 'cancelled')
                            This entry was cancelled. If you think this is a mistake, please message us on Facebook.
                        @else
                            Your spot is held for now. Send your payment receipt and reference to confirm it.
                        @endif
                    </p>
                </div>

                {{-- Reference ticket --}}
                <div class="hcq-ticket">
                    <p class="hcq-cap">Your entry reference</p>
                    <p class="hc-display hcq-ref">{{ $entry->reference }}</p>
                    <button type="button" class="hcq-copy" data-copy="{{ $entry->reference }}" data-label="Copy reference" onclick="hcCopy(this)">Copy reference</button>

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
                        <div class="hcq-stat"><span>Total</span><b class="hc-display">₱{{ number_format((float) $entry->total_price, 2) }}</b></div>
                    </div>

                    <p class="hcq-hint">Screenshot this reference for your records.
                        @if ($state === 'pending')
                            <button type="button" class="hcq-refresh" onclick="location.reload()">Refresh status</button>
                        @endif
                    </p>
                </div>

                {{-- Entry details --}}
                <div class="hcn-card" style="max-width:none;">
                    <div class="hcn-head">
                        <span class="hcn-num"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg></span>
                        <h2 class="hc-display">Entry details</h2>
                    </div>

                    <dl class="hcq-dl">
                        <div><dt>Name</dt><dd>{{ $entry->name }}</dd></div>
                        <div><dt>Contact number</dt><dd>{{ $entry->contact_number }}</dd></div>
                        <div><dt>Session</dt><dd>{{ $session->title }}</dd></div>
                        <div><dt>Players</dt><dd>{{ $entry->players }}</dd></div>
                        <div><dt>Skill level</dt><dd>{{ $entry->skill_level ?? 'Not set' }}</dd></div>
                        <div><dt>Date</dt><dd>{{ $session->startsAt()->format('M d, Y') }}</dd></div>
                        <div><dt>Time</dt><dd>{{ $session->startsAt()->format('g:i A') }} – {{ $session->endsAt()->format('g:i A') }}</dd></div>
                        <div style="grid-column:1 / -1;">
                            <dt>Court{{ $session->courts->count() > 1 ? 's' : '' }}</dt>
                            <dd style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
                                @foreach ($session->courts as $c)
                                    <span class="hcq-slotchip">{{ $c->name }}</span>
                                @endforeach
                            </dd>
                        </div>
                    </dl>
                </div>

                @if ($state === 'pending')

                    {{-- Payment --}}
                    <div class="hcn-card" x-data="{ tab: '{{ $method }}' }">
                        <div class="hcn-head">
                            <span class="hcn-num">2</span>
                            <h2 class="hc-display">Send your payment</h2>
                        </div>

                        <div class="hcq-amount">
                            <span>Please send exactly</span>
                            <b class="hc-display">₱{{ number_format((float) $entry->total_price, 2) }}</b>
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
                    <div class="hcn-card">
                        <div class="hcn-head">
                            <span class="hcn-num">3</span>
                            <h2 class="hc-display">Send your receipt</h2>
                        </div>

                        <p style="margin:0;line-height:1.65;color:rgba(27,43,28,.8);">
                            To verify your spot, kindly message our Facebook page with your entry reference and payment receipt.
                        </p>

                        <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="hcq-fb">
                            <svg width="20" height="20" style="flex:none" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <span>Open our Facebook page</span>
                        </a>
                    </div>

                @else

                    <div class="hcn-card">
                        <div class="hcn-head">
                            <span class="hcn-num">
                                @if ($isBad)
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                @else
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                                @endif
                            </span>
                            <h2 class="hc-display">{{ $state === 'confirmed' ? "You're all set" : 'What happens now' }}</h2>
                        </div>

                        @if ($state === 'confirmed')
                            <p style="margin:0;line-height:1.65;color:rgba(27,43,28,.8);">
                                Please arrive before the session starts and show this reference to our staff. Your payment has been received, so there is nothing more to send.
                            </p>
                            <a href="https://www.google.com/maps/dir/?api=1&amp;destination=7.3144437,125.6915676" target="_blank" rel="noopener noreferrer" class="hcq-go">
                                Get directions
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                            </a>
                        @else
                            @if ($state === 'rejected' && $entry->rejection_reason)
                                <p class="hcq-reason"><strong>Reason:</strong> {{ $entry->rejection_reason }}</p>
                            @endif
                            <p style="margin:14px 0 0;line-height:1.65;color:rgba(27,43,28,.8);">
                                Please do not send any payment for this entry. If you already paid, message our Facebook page right away with your reference and your receipt so we can sort it out.
                            </p>
                            <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer" class="hcq-fb">
                                <svg width="20" height="20" style="flex:none" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                <span>Message us on Facebook</span>
                            </a>
                        @endif
                    </div>

                @endif

                <div class="hcn-btns">
                    <a href="{{ route('open-play.index') }}" class="hcn-btn main">All sessions</a>
                    <a href="{{ route('home') }}" class="hcn-btn ghost">Back to home</a>
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

    @include('partials.op-footer')
    @include('partials.op-legal')

</body>
</html>