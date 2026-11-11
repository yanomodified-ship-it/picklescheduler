@once
    <style>
        .hcz-find{display:grid;gap:26px;padding:44px 0;border-bottom:1px solid rgba(246,248,238,.14)}
        @media (min-width:900px){.hcz-find{grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:44px;align-items:center}}
        .hcz-find-eyebrow{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:rgba(228,240,58,.14);padding:6px 14px;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#E4F03A}
        .hcz-find h3{margin:14px 0 0;font-size:clamp(26px,3.6vw,38px);font-weight:800;line-height:1.1;color:#F6F8EE}
        .hcz-find h3 em{font-style:normal;color:#E4F03A}
        .hcz-find-row{display:flex;align-items:flex-start;gap:12px;margin-top:18px;font-size:15px;line-height:1.5;color:#F6F8EE}
        .hcz-find-row .ic{display:flex;width:38px;height:38px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:rgba(228,240,58,.15);color:#E4F03A}
        .hcz-find-row small{display:block;margin-bottom:2px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#CFDDAE}
        .hcz-find-go{display:inline-flex;align-items:center;gap:8px;margin-top:24px;border-radius:9999px;background:#E4F03A;padding:14px 26px;font-size:15px;font-weight:800;color:#1B2B1C;text-decoration:none;transition:background .2s}
        .hcz-find-go:hover{background:#fff}
        .hcz-find-go svg{transition:transform .25s}
        .hcz-find-go:hover svg{transform:translate(3px,-3px)}
        .hcz-find-map{position:relative;overflow:hidden;border-radius:28px;border:5px solid #F6F8EE;background:#fff;aspect-ratio:4/3;box-shadow:0 26px 44px -30px rgba(0,0,0,.7)}
        @media (min-width:900px){.hcz-find-map{aspect-ratio:auto;min-height:340px}}
        .hcz-find-map iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
        .hcz-find-chip{position:absolute;left:12px;top:12px;z-index:2;display:flex;align-items:center;gap:10px;border-radius:16px;background:#fff;padding:8px 14px 8px 8px;box-shadow:0 14px 28px -14px rgba(27,43,28,.5);pointer-events:none}
        .hcz-find-chip .dot{display:flex;width:32px;height:32px;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24}
        .hcz-find-chip b{display:block;font-size:13px;font-weight:800;line-height:1.2;color:#1B2B1C}
        .hcz-find-chip .s{display:block;font-size:11px;font-weight:600;color:rgba(27,43,28,.65)}
    </style>
@endonce

@php
    // Edit the location here once and every footer follows.
    $mapLat     = 7.3144437;
    $mapLng     = 125.6915676;
    $mapAddress = 'Purok 1, Salvacion Panabo City, Davao Del Norte, Philippines';
    $mapHours   = \App\Support\Pricing::data()['open_label'] . ' – ' . \App\Support\Pricing::data()['close_label'];
@endphp

<div class="hcz-find">
    <div>
        <span class="hcz-find-eyebrow">Visit us</span>
        <h3 class="hc-display">Find <em>HomeCourt</em></h3>

        <div class="hcz-find-row">
            <span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>
            <div><small>Address</small>{{ $mapAddress }}</div>
        </div>

        <div class="hcz-find-row">
            <span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
            <div><small>Courts open</small>{{ $mapHours }}</div>
        </div>

        <a href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $mapLat }},{{ $mapLng }}" target="_blank" rel="noopener noreferrer" class="hcz-find-go">
            Get directions
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
        </a>
    </div>

    <div class="hcz-find-map">
        <iframe
            title="Map showing HomeCourt PickleHouse"
            src="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}&amp;z=18&amp;output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>

        <div class="hcz-find-chip">
            <span class="dot">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            </span>
            <div>
                <b>HomeCourt PickleHouse</b>
                <span class="s">Panabo City</span>
            </div>
        </div>
    </div>
</div>