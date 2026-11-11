@php
    $hcNow  = now()->hour + now()->minute / 60;
       $hcOpen = \App\Support\Pricing::isOpenNow();
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

    <div class="hch-top" :class="{ 'off': scrolled }">
        <div class="hch-top-in">
            @if ($hcOpen)
                <span class="hch-status"><i></i>Open now · until {{ \App\Support\Pricing::data()['close_label'] }}</span>
            @else
                <span class="hch-status closed"><i></i>Closed now · opens at {{ \App\Support\Pricing::data()['open_label'] }}</span>
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
                <a href="{{ route('open-play.index') }}" class="hch-link">All sessions</a>
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