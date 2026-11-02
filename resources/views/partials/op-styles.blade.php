<style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; background: #F6F8EE; color: #1B2B1C; font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; }
    .hc-display { font-family: 'Bricolage Grotesque', 'Inter', system-ui, sans-serif; letter-spacing: -0.025em; }
    [x-cloak] { display: none !important; }
    a:focus-visible, button:focus-visible { outline: 2px solid #2F5D34; outline-offset: 3px; }

    /* ---------- header ---------- */
    .hch{position:sticky;top:0;z-index:50}
    .hch-top{overflow:hidden;max-height:44px;background:#1F3F24;color:#CFDDAE;transition:max-height .35s ease,opacity .25s ease}
    .hch-top.off{max-height:0;opacity:0}
    .hch-top-in{display:flex;align-items:center;justify-content:center;gap:12px;max-width:1232px;margin:0 auto;padding:8px 24px;font-size:12px;font-weight:700}
    @media (min-width:640px){.hch-top-in{justify-content:space-between}}
    .hch-status{display:inline-flex;align-items:center;gap:8px;color:#E4F03A}
    .hch-status i{width:8px;height:8px;border-radius:9999px;background:#E4F03A;animation:hcn-pulse 2s ease-in-out infinite}
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

    /* ---------- dark band + cards ---------- */
    .hcn{position:relative;margin:12px 12px 24px;border-radius:36px;padding:44px 14px 52px;
        background-color:#1F3F24;
        background-image:radial-gradient(620px 360px at 92% 0%, rgba(228,240,58,.20), transparent 65%),radial-gradient(700px 420px at 0% 100%, rgba(95,150,80,.35), transparent 65%)}
    @media (min-width:768px){.hcn{margin:12px 24px 28px;border-radius:56px;padding:60px 40px 64px}}
    .hcn-deco{position:absolute;inset:0;overflow:hidden;border-radius:inherit;pointer-events:none}
    .hcn-lines{position:absolute;inset:0;width:100%;height:100%;opacity:.06}
    .hcn-ball{position:absolute;color:#E4F03A;animation:hcn-arc 7s ease-in-out infinite alternate}
    .hcn-ball svg{display:block;width:100%;height:100%}
    .hcn-ball.a{left:4%;top:24px;width:34px;height:34px}
    .hcn-ball.b{right:6%;top:34px;width:46px;height:46px;color:#F6F8EE;opacity:.8;animation-delay:-3s}
    .hcn-ball.c{left:8%;bottom:30px;width:40px;height:40px;color:#F6F8EE;opacity:.8;animation-delay:-5s}
    @media (max-width:767px){.hcn-ball.b,.hcn-ball.c{display:none}}
    .hcn-inner{position:relative;z-index:2;margin:0 auto;max-width:760px}
    .hcn-hero{text-align:center}
    .hcn-eyebrow{display:inline-flex;align-items:center;gap:8px;border-radius:9999px;background:rgba(228,240,58,.14);padding:6px 14px;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#E4F03A}
    .hcn-eyebrow i{width:7px;height:7px;border-radius:9999px;background:#E4F03A;animation:hcn-pulse 2s ease-in-out infinite}
    .hcn-title{margin:16px 0 0;font-size:clamp(30px,5.2vw,50px);font-weight:800;line-height:1.05;color:#F6F8EE}
    .hcn-title em{font-style:normal;color:#E4F03A}
    .hcn-sub{margin:12px auto 0;max-width:520px;line-height:1.6;color:#CFDDAE}

    .hcn-card{position:relative;overflow:hidden;margin-top:22px;border-radius:30px;background:#fff;padding:28px 22px;box-shadow:0 22px 40px -26px rgba(0,0,0,.65)}
    @media (min-width:640px){.hcn-card{padding:34px}}
    .hcn-card::before{content:"";position:absolute;left:0;right:0;top:0;height:6px;background:linear-gradient(90deg,#2F5D34,#E4F03A,#2F5D34)}
    .hcn-head{display:flex;align-items:center;gap:12px;margin-bottom:22px}
    .hcn-head h2{margin:0;font-size:20px;font-weight:700;color:#1B2B1C}
    .hcn-num{display:flex;width:38px;height:38px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:#E4F03A;color:#1F3F24;font-size:16px;font-weight:800}
    .hcn-grid{display:grid;gap:16px;grid-template-columns:minmax(0,1fr)}
    @media (min-width:640px){.hcn-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}}
    .hcn-l{display:block;margin-bottom:8px;font-size:14px;font-weight:600;color:#1B2B1C}
    .hcn-in{width:100%;border:1px solid rgba(27,43,28,.2);background:#F6F8EE;color:#1B2B1C;border-radius:12px;padding:12px 16px;font-family:inherit;font-size:16px;outline:none;transition:border-color .2s,background .2s,box-shadow .2s}
    .hcn-in:focus{border-color:#2F5D34;background:#fff;box-shadow:0 0 0 3px rgba(47,93,52,.2)}
    .hcn-in::placeholder{color:rgba(27,43,28,.4)}
    .hcn-msg{margin-top:22px;border-radius:18px;border:1px solid #FECACA;background:#FEF2F2;padding:14px 18px;font-size:14px;line-height:1.6;color:#7F1D1D}
    .hcn-msg ul{margin:6px 0 0;padding-left:18px;list-style:disc}
    .hcn-btns{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-top:28px}
    .hcn-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:9999px;padding:15px 28px;font-size:15px;font-weight:800;text-decoration:none;transition:background .2s,color .2s,border-color .2s}
    .hcn-btn.main{background:#E4F03A;color:#1B2B1C}
    .hcn-btn.main:hover{background:#fff}
    .hcn-btn.ghost{border:2px solid rgba(246,248,238,.35);color:#F6F8EE}
    .hcn-btn.ghost:hover{background:#F6F8EE;color:#1F3F24;border-color:#F6F8EE}

    /* ---------- footer ---------- */
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
    .hcz-legal .l{background:none;border:0;padding:0;font-family:inherit;font-size:13px;font-weight:600;color:#F6F8EE;cursor:pointer;text-decoration:none;text-underline-offset:4px;transition:color .2s}
    .hcz-legal .l:hover{color:#E4F03A;text-decoration:underline}
    .hcz-round{display:flex;width:42px;height:42px;align-items:center;justify-content:center;border-radius:9999px;border:2px solid rgba(246,248,238,.3);background:transparent;color:#F6F8EE;cursor:pointer;transition:background .2s,color .2s,border-color .2s}
    .hcz-round:hover{background:#E4F03A;border-color:#E4F03A;color:#1B2B1C}

    /* ---------- House Rules / Privacy popups ---------- */
    .hcm-overlay{position:fixed;inset:0;z-index:1000;display:flex;align-items:flex-end;justify-content:center;background:rgba(27,43,28,.6);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px)}
    @media (min-width:640px){.hcm-overlay{align-items:center;padding:24px}}
    .hcm-panel{display:flex;flex-direction:column;width:100%;max-width:672px;max-height:90vh;max-height:90dvh;overflow:hidden;background:#fff;color:#1B2B1C;border-radius:28px 28px 0 0;box-shadow:0 30px 60px -20px rgba(0,0,0,.5)}
    @media (min-width:640px){.hcm-panel{border-radius:32px}}
    .hcm-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:20px 24px;border-bottom:1px solid rgba(27,43,28,.1)}
    .hcm-title{margin:0;font-size:24px;font-weight:800;line-height:1.2}
    .hcm-sub{margin:4px 0 0;font-size:14px;color:rgba(27,43,28,.7)}
    .hcm-x{flex:none;padding:8px;border:0;border-radius:9999px;background:transparent;color:rgba(27,43,28,.7);cursor:pointer}
    .hcm-x:hover{background:#E5ECD2;color:#1B2B1C}
    .hcm-body{overflow-y:auto;overscroll-behavior:contain;padding:24px;font-size:14px;line-height:1.65;color:rgba(27,43,28,.85)}
    .hcm-body section + section{margin-top:24px}
    .hcm-body h3{margin:0;font-size:17px;font-weight:700;color:#1B2B1C}
    .hcm-body p{margin:8px 0 0}
    .hcm-body ul{margin:8px 0 0;padding-left:20px;list-style:disc}
    .hcm-body li + li{margin-top:4px}
    .hcm-body a{color:#2F5D34;font-weight:600;text-decoration:underline;text-underline-offset:2px}
    .hcm-intro{margin:0 0 24px !important}
    .hcm-rules{counter-reset:rule}
    .hcm-rules h3::before{counter-increment:rule;content:counter(rule) ". "}
    .hcm-foot{padding:16px 24px;border-top:1px solid rgba(27,43,28,.1);text-align:right}
    .hcm-close{border:0;border-radius:9999px;background:#2F5D34;color:#fff;font-family:inherit;font-size:14px;font-weight:700;padding:10px 28px;cursor:pointer}
    .hcm-close:hover{background:#1F3F24}

    @keyframes hcn-pulse{0%,100%{opacity:1}50%{opacity:.45}}
    @keyframes hcn-arc{0%{transform:translate(0,0) rotate(0)}50%{transform:translate(26px,-24px) rotate(140deg)}100%{transform:translate(52px,0) rotate(280deg)}}
    @media (prefers-reduced-motion:reduce){
        .hcn-ball,.hcn-eyebrow i,.hch-status i{animation:none}
        .hch-top,.hch-bar,.hch-logo img{transition:none}
    }
</style>