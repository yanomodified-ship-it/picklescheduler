<footer class="hcz">
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
                <button type="button" class="l" onclick="window.dispatchEvent(new CustomEvent('open-house-rules'))">House Rules</button>
                <button type="button" class="l" onclick="window.dispatchEvent(new CustomEvent('open-privacy'))">Privacy Policy</button>
                <button type="button" class="hcz-round" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" aria-label="Back to top">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                </button>
            </div>
        </div>
    </div>
</footer>