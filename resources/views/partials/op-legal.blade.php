<div x-data="{ open: null, lastFocus: null }"
     x-init="$watch('open', value => {
         document.body.style.overflow = value ? 'hidden' : '';
         if (value) { $nextTick(() => { const btn = value === 'rules' ? $refs.closeRules : $refs.closePrivacy; if (btn) btn.focus(); }); }
         else if (lastFocus) { lastFocus.focus(); }
     })"
     @open-privacy.window="lastFocus = document.activeElement; open = 'privacy'"
     @open-house-rules.window="lastFocus = document.activeElement; open = 'rules'"
     @keydown.escape.window="open = null">

    {{-- House Rules popup --}}
    <div class="hcm-overlay" x-show="open === 'rules'" x-cloak x-transition.opacity @click.self="open = null">
        <div class="hcm-panel" role="dialog" aria-modal="true" aria-labelledby="rules-title">
            <div class="hcm-head">
                <div>
                    <h2 id="rules-title" class="hcm-title hc-display">House Rules</h2>
                    <p class="hcm-sub">Last updated: October 2026</p>
                </div>
                <button type="button" x-ref="closeRules" @click="open = null" aria-label="Close house rules" class="hcm-x"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            </div>

            <div class="hcm-body hcm-rules">
                <p class="hcm-intro">To keep our courts safe and in great condition for everyone, all players and guests of HomeCourt PickleHouse agree to the following house rules:</p>

                <section><h3 class="hc-display">Play within your booked time</h3><p>Please arrive on time and leave the court promptly when your session ends, so the next players can start on time.</p></section>
                <section><h3 class="hc-display">Keep the courts clean</h3><p>Throw trash, cups, bottles and other waste in the bins. Leave the court clean and ready for the next players.</p></section>
                <section><h3 class="hc-display">Food and drinks</h3><p>Please keep food away from the playing surface. Drinks in closed bottles are fine; clean up any spills right away.</p></section>
                <section><h3 class="hc-display">Clean your shoes before playing</h3><p>Wipe your shoes before stepping onto the court. This keeps dirt and debris off the surface and keeps everyone's footing safe.</p></section>
                <section><h3 class="hc-display">Protect the court surface</h3><p>Please do not bring in furniture, sharp objects or anything else that could damage the playing surface.</p></section>
                <section><h3 class="hc-display">No smoking or vaping</h3><p>Smoking and vaping are not allowed on the courts or in the playing areas.</p></section>
                <section><h3 class="hc-display">Play safely and respectfully</h3><p>No aggressive behavior, abusive language, or conduct that could endanger or disturb other players.</p></section>
                <section><h3 class="hc-display">Keep your belongings secure</h3><p>Keep bags, phones, paddles and other items with you or in a safe place. HomeCourt PickleHouse is not responsible for lost or unattended belongings.</p></section>
                <section><h3 class="hc-display">Damages and misuse</h3><p>Intentional or careless damage to the courts, equipment or property may be charged for repair or replacement.</p></section>
            </div>

            <div class="hcm-foot"><button type="button" @click="open = null" class="hcm-close">Close</button></div>
        </div>
    </div>

    {{-- Privacy Policy popup --}}
    <div class="hcm-overlay" x-show="open === 'privacy'" x-cloak x-transition.opacity @click.self="open = null">
        <div class="hcm-panel" role="dialog" aria-modal="true" aria-labelledby="privacy-title">
            <div class="hcm-head">
                <div>
                    <h2 id="privacy-title" class="hcm-title hc-display">Privacy Policy</h2>
                    <p class="hcm-sub">Last updated: October 2026</p>
                </div>
                <button type="button" x-ref="closePrivacy" @click="open = null" aria-label="Close privacy policy" class="hcm-x"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            </div>

            <div class="hcm-body">
                <section><h3 class="hc-display">Who we are</h3><p>HomeCourt PickleHouse ("we", "us") runs this website so you can book pickleball courts and join open play sessions at Purok 7, San Vicente Panabo City, Panabo, Philippines, 8105.</p></section>
                <section>
                    <h3 class="hc-display">What we collect</h3>
                    <p>When you book a court or join an open play session we collect only what we need to run it:</p>
                    <ul>
                        <li>The Facebook name and contact number you type into the booking or join form</li>
                        <li>The number of players, and the court, date and time slots or session you choose</li>
                        <li>The payment method you pick (GCash or Maya)</li>
                        <li>The reference and status we assign to your booking or entry</li>
                    </ul>
                    <p>We never ask for your e-wallet PIN, card details or social media password. Payment receipts you send through Facebook Messenger stay on Facebook; they are not uploaded to this website.</p>
                </section>
                <section><h3 class="hc-display">Why we use it</h3><p>We use your details to reserve your court or spot, verify your payment, contact you about it, issue your invoice, prevent double bookings, and keep our business records.</p></section>
                <section><h3 class="hc-display">Who can see it</h3><p>Only our staff who manage bookings, and the website hosting and database providers that store the data for us. We do not sell your information or use it for advertising. We may share it if the law requires us to.</p></section>
                <section><h3 class="hc-display">Third-party content</h3><p>This website loads Google Fonts, and the map in the footer of our pages is provided by Google Maps. Google may receive your IP address and browser details when these load.</p></section>
                <section><h3 class="hc-display">Cookies</h3><p>We use only essential cookies that keep the site working and protect our forms from misuse. We do not use advertising cookies.</p></section>
                <section><h3 class="hc-display">How long we keep it</h3><p>We keep records only as long as we need them for bookkeeping, customer support and resolving disputes, then we delete or anonymise them.</p></section>
                <section><h3 class="hc-display">Your rights</h3><p>Under the Data Privacy Act of 2012 (Republic Act No. 10173) you may ask to see, correct or delete your personal data, object to how we use it, and file a complaint with the National Privacy Commission at <a href="https://privacy.gov.ph" target="_blank" rel="noopener noreferrer">privacy.gov.ph</a>.</p></section>
                <section><h3 class="hc-display">Contact us</h3><p>To make a request or ask a question about your data, message our <a href="https://www.facebook.com/HomeCourtPickleHouse" target="_blank" rel="noopener noreferrer">Facebook page</a>.</p></section>
                <section><h3 class="hc-display">Changes to this policy</h3><p>We may update this policy from time to time. The date at the top shows when it was last changed.</p></section>
            </div>

            <div class="hcm-foot"><button type="button" @click="open = null" class="hcm-close">Close</button></div>
        </div>
    </div>
</div>