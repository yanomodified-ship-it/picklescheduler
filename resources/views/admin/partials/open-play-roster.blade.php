@once
    <style>
        .hco-panel .hco-roster{flex:0 0 100%;width:100%;margin-top:6px;border-top:1px solid rgba(148,163,184,.14);padding-top:12px}
        .hco-panel .hco-roster summary{display:flex;flex-wrap:wrap;align-items:center;gap:8px;cursor:pointer;list-style:none;font-size:13px;font-weight:800;color:#E2E8F0}
        .hco-panel .hco-roster summary::-webkit-details-marker{display:none}
        .hco-panel .hco-roster summary::before{content:"\25B8";color:#A3E635;transition:transform .2s}
        .hco-panel .hco-roster[open] summary::before{transform:rotate(90deg)}
        .hco-panel .hco-entry{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px 16px;margin-top:10px;border:1px solid rgba(148,163,184,.14);background:#0B1327;border-radius:14px;padding:12px 14px}
        .hco-panel .hco-entry.is-off{opacity:.55}
        .hco-panel .hco-entry .who{display:flex;flex-wrap:wrap;align-items:baseline;gap:4px 12px}
        .hco-panel .hco-entry .who b{font-size:15px;color:#fff}
        .hco-panel .hco-entry .who a{font-size:13px;color:#94A3B8;text-decoration:none}
        .hco-panel .hco-entry .meta{margin-top:4px;font-size:12px;line-height:1.6;color:#94A3B8}
        .hco-panel .hco-entry .side{display:flex;flex-wrap:wrap;align-items:center;gap:8px}
        .hco-panel .hco-entry .side form{margin:0}
        .hco-panel .hco-st{border-radius:9999px;padding:4px 11px;font-size:11px;font-weight:800;letter-spacing:.03em}
        .hco-panel .hco-st.pend{background:rgba(251,191,36,.16);color:#FCD34D}
        .hco-panel .hco-st.ok{background:rgba(163,230,53,.16);color:#BEF264}
        .hco-panel .hco-st.bad{background:rgba(248,113,113,.16);color:#FCA5A5}
        .hco-panel .hco-roster-empty{margin-top:10px;font-size:13px;color:#94A3B8}
    </style>
@endonce

@php
    $entries    = $s->entries;
    $inactive   = ['Rejected', 'Cancelled'];
    $pendingN   = $entries->where('entry_status', 'Pending Verification')->count();
    $confirmedN = $entries->where('entry_status', 'Confirmed')->count();
    $skillCounts = \App\Support\SkillLevels::counts($entries->whereNotIn('entry_status', $inactive));
@endphp

<details class="hco-roster" @if ($pendingN > 0) open @endif>
    <summary>
        <span>Players ({{ $entries->count() }})</span>
        @if ($pendingN > 0)
            <span class="hco-badge" style="background:rgba(251,191,36,.16);color:#FCD34D">{{ $pendingN }} to verify</span>
        @endif
        @if ($confirmedN > 0)
            <span class="hco-badge lime">{{ $confirmedN }} confirmed</span>
        @endif
        @foreach ($skillCounts as $lvl => $n)
            <span class="hco-badge"><span style="display:inline-block;width:8px;height:8px;border-radius:9999px;margin-right:6px;background:{{ \App\Support\SkillLevels::color($lvl) }}"></span>{{ $lvl }} {{ $n }}</span>
        @endforeach
    </summary>

    @forelse ($entries as $e)
        @php
            $off        = in_array($e->entry_status, $inactive, true);
            $badgeClass = $e->entry_status === 'Confirmed' ? 'ok' : ($off ? 'bad' : 'pend');
            $verifyMsg  = 'Verify ₱' . number_format((float) $e->total_price, 0) . ' from ' . $e->name . ' (' . $e->players . ' player(s))? Make sure the money is already in your ' . $e->payment_method . ' account.';
            $cancelMsg  = 'Cancel the entry of ' . $e->name . ' (' . $e->reference . ')? Their spot(s) will be free again.';
        @endphp

        <div class="hco-entry {{ $off ? 'is-off' : '' }}">
            <div>
                <div class="who">
                    <b>{{ $e->name }}</b>
                    <a href="tel:{{ $e->contact_number }}">{{ $e->contact_number }}</a>
                    @if ($e->skill_level)
                        <span class="hco-badge"><span style="display:inline-block;width:8px;height:8px;border-radius:9999px;margin-right:6px;background:{{ \App\Support\SkillLevels::color($e->skill_level) }}"></span>{{ $e->skill_level }}</span>
                    @endif
                    @if ($e->hide_name)
                        <span class="hco-badge">Name hidden on roster</span>
                    @endif
                </div>
                <div class="meta">
                    {{ $e->reference }} &middot; {{ $e->players }} {{ $e->players == 1 ? 'player' : 'players' }}
                    &middot; ₱{{ number_format((float) $e->total_price, 0) }} &middot; {{ $e->payment_method }}
                    &middot; {{ $e->created_at->format('M j, g:i A') }}
                </div>
                @if ($e->note)
                    <div class="meta">Note: {{ $e->note }}</div>
                @endif
                @if ($e->entry_status === 'Rejected' && $e->rejection_reason)
                    <div class="meta" style="color:#FCA5A5">Reason: {{ $e->rejection_reason }}</div>
                @endif
            </div>

            <div class="side">
                <span class="hco-st {{ $badgeClass }}">{{ $e->entry_status === 'Pending Verification' ? 'Pending' : $e->entry_status }}</span>

                @if ($e->entry_status === 'Pending Verification')
                    <form method="POST" action="{{ route('admin.open-play.entries.verify', $e) }}" onsubmit="return confirm({{ \Illuminate\Support\Js::from($verifyMsg) }})">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="hco-btn sm">Verify</button>
                    </form>

                    <form method="POST" action="{{ route('admin.open-play.entries.reject', $e) }}"
                          onsubmit="var r = prompt('Reason for rejecting (the customer will see this, optional):', ''); if (r === null) { return false; } this.elements['rejection_reason'].value = r.slice(0, 250); return true;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="rejection_reason" value="">
                        <button type="submit" class="hco-btn danger sm">Reject</button>
                    </form>
                @endif

                @if (! $off)
                    <form method="POST" action="{{ route('admin.open-play.entries.cancel', $e) }}" onsubmit="return confirm({{ \Illuminate\Support\Js::from($cancelMsg) }})">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="hco-btn ghost sm">Cancel</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="hco-roster-empty">No one has joined this session yet.</div>
    @endforelse
</details>