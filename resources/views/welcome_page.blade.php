<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>SCOPS — Smart Control of Paid Systems</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
<style>
  :root {
    --navy:        #050B16;
    --navy-2:      #08111F;
    --cyan:        #14E6F4;
    --orange:      #FF6B00;
    --white:       #F8FAFC;
    --gray:        #94A3B8;
    --card-line:   rgba(20, 230, 244, 0.16);
    --font-display: "Space Grotesk", sans-serif;
    --font-body:    "Manrope", sans-serif;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  html, body { height: 100%; }

  body {
    font-family: var(--font-body);
    background: var(--navy);
    color: var(--white);
    min-height: 100vh;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    position: relative;
  }

  /* ── Atmosphere ─────────────────────────────────────────── */
  .bg {
    position: fixed;
    inset: 0;
    z-index: 0;
    background:
      radial-gradient(120% 90% at 88% 18%, rgba(20,230,244,0.10), transparent 55%),
      radial-gradient(90% 80% at 12% 92%, rgba(255,107,0,0.07), transparent 50%),
      linear-gradient(160deg, var(--navy) 0%, var(--navy-2) 100%);
  }
  .bg::before {
    content: none;
  }
  .bg::after {
    /* grain */
    content: "";
    position: absolute;
    inset: 0;
    opacity: 0.5;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
  }

  .shell {
    position: relative;
    z-index: 2;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 28px clamp(20px, 5vw, 72px) 36px;
  }

  /* ── Top bar ────────────────────────────────────────────── */
  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
  }
  .brand {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .brand img.mark { height: 38px; width: auto; display: block;
    filter: drop-shadow(0 0 14px rgba(20,230,244,0.45)); }
  .brand img.wordmark { height: 40px; width: auto; display: block; }
  .brand .name {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: 22px;
    letter-spacing: 0.14em;
    color: var(--white);
  }
  .brand .name b { color: var(--cyan); font-weight: 700; }

  nav.top {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .nav-link {
    font-family: var(--font-display);
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--gray);
    text-decoration: none;
    padding: 11px 18px;
    border-radius: 10px;
    transition: color .2s ease, background .2s ease;
  }
  .nav-link:hover { color: var(--white); background: rgba(255,255,255,0.04); }
  .nav-cta {
    font-family: var(--font-display);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--navy);
    background: var(--cyan);
    text-decoration: none;
    padding: 11px 20px;
    border-radius: 10px;
    box-shadow: 0 0 0 1px rgba(20,230,244,0.4), 0 8px 28px rgba(20,230,244,0.28);
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .nav-cta:hover { transform: translateY(-2px); box-shadow: 0 0 0 1px rgba(20,230,244,0.6), 0 12px 34px rgba(20,230,244,0.4); }

  /* ── Hero ───────────────────────────────────────────────── */
  main {
    flex: 1;
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
    align-items: center;
    gap: clamp(32px, 5vw, 80px);
    padding-top: clamp(28px, 6vh, 64px);
  }

  /* Left card */
  .card {
    position: relative;
    background: linear-gradient(165deg, rgba(20,230,244,0.05), rgba(8,17,31,0.5));
    border: 1px solid var(--card-line);
    border-radius: 24px;
    padding: clamp(28px, 3.4vw, 48px);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    box-shadow: 0 30px 80px -40px rgba(0,0,0,0.9), inset 0 1px 0 rgba(255,255,255,0.04);
    max-width: 620px;
  }

  .eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--cyan);
    margin-bottom: 22px;
  }
  .eyebrow .dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--cyan);
    box-shadow: 0 0 12px 2px rgba(20,230,244,0.8);
    animation: pulse 2.4s ease-in-out infinite;
  }
  @keyframes pulse { 0%,100%{opacity:.45;transform:scale(.8);} 50%{opacity:1;transform:scale(1.15);} }

  h1 {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: clamp(34px, 4vw, 56px);
    line-height: 1.04;
    letter-spacing: -0.02em;
    margin-bottom: 18px;
    text-wrap: balance;
  }
  h1 .glow { color: var(--cyan); text-shadow: 0 0 28px rgba(20,230,244,0.5); }

  .lede {
    font-size: clamp(15px, 1.15vw, 17px);
    line-height: 1.6;
    color: var(--gray);
    max-width: 48ch;
    margin-bottom: 30px;
    text-wrap: pretty;
  }

  /* feature list */
  .features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 22px;
    margin-bottom: 34px;
  }
  .feature {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14.5px;
    font-weight: 500;
    color: #cdd7e4;
  }
  .feature .ico {
    flex: none;
    width: 34px; height: 34px;
    display: grid; place-items: center;
    border-radius: 9px;
    background: rgba(20,230,244,0.08);
    border: 1px solid var(--card-line);
    color: var(--cyan);
  }
  .feature .ico svg { width: 17px; height: 17px; display: block; }

  /* actions */
  .actions { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
  .btn-primary {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.02em;
    color: #fff;
    background: var(--orange);
    border: none;
    border-radius: 12px;
    padding: 15px 28px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    box-shadow: 0 10px 30px -6px rgba(255,107,0,0.5), inset 0 1px 0 rgba(255,255,255,0.25);
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 40px -6px rgba(255,107,0,0.6), inset 0 1px 0 rgba(255,255,255,0.25); }
  .btn-primary svg { width: 17px; height: 17px; transition: transform .2s ease; }
  .btn-primary:hover svg { transform: translateX(4px); }

  .btn-ghost {
    font-family: var(--font-display);
    font-weight: 500;
    font-size: 15px;
    color: var(--white);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 15px 8px;
    transition: color .2s ease;
  }
  .btn-ghost:hover { color: var(--cyan); }
  .btn-ghost .play {
    width: 30px; height: 30px; border-radius: 50%;
    border: 1px solid var(--card-line);
    display: grid; place-items: center;
    color: var(--cyan);
  }
  .btn-ghost .play svg { width: 12px; height: 12px; }

  /* ── Right visual ───────────────────────────────────────── */
  .visual {
    position: relative;
    height: clamp(420px, 60vh, 620px);
    display: grid;
    place-items: center;
  }
  .visual .halo {
    position: absolute;
    width: 78%;
    aspect-ratio: 1;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(20,230,244,0.28), transparent 62%);
    filter: blur(10px);
    animation: breathe 7s ease-in-out infinite;
  }
  @keyframes breathe { 0%,100%{transform:scale(1);opacity:.85;} 50%{transform:scale(1.08);opacity:1;} }
  .ring {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(20,230,244,0.16);
  }
  .ring.r1 { width: 56%; aspect-ratio:1; }
  .ring.r2 { width: 76%; aspect-ratio:1; border-color: rgba(20,230,244,0.10); }
  .ring.r3 { width: 96%; aspect-ratio:1; border-color: rgba(20,230,244,0.06); }
  .ring.spin { animation: spin 26s linear infinite; }
  .ring.spin::after {
    content:""; position:absolute; top:-3px; left:50%;
    width:6px; height:6px; border-radius:50%; background:var(--cyan);
    box-shadow:0 0 12px 2px rgba(20,230,244,0.9); transform:translateX(-50%);
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  .owl {
    position: relative;
    width: clamp(150px, 20vw, 230px);
    z-index: 3;
    filter: drop-shadow(0 0 40px rgba(20,230,244,0.55));
    animation: float 6s ease-in-out infinite;
  }
  @keyframes float { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-14px);} }

  /* floating subscription chips */
  .chip {
    position: absolute;
    z-index: 4;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 14px;
    background: rgba(8,17,31,0.72);
    border: 1px solid var(--card-line);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 18px 44px -22px rgba(0,0,0,0.9);
    animation: float 6.5s ease-in-out infinite;
  }
  .chip .av {
    width: 30px; height: 30px; border-radius: 8px;
    display: grid; place-items: center;
    font-family: var(--font-display); font-weight: 700; font-size: 14px;
  }
  .chip .meta { line-height: 1.25; }
  .chip .meta b { display:block; font-size: 13px; font-weight: 600; color: var(--white); }
  .chip .meta span { font-size: 11px; color: var(--gray); }
  .chip .amt { font-family: var(--font-display); font-weight: 600; font-size: 13px; margin-left: 6px; }

  .chip.c1 { top: 10%;  left: -4%;  animation-delay: -1s; }
  .chip.c2 { top: 38%;  right: -6%; animation-delay: -3s; }
  .chip.c3 { bottom: 12%; left: 4%;  animation-delay: -2s; }

  .chip.c1 .av { background: rgba(20,230,244,0.14); color: var(--cyan); }
  .chip.c1 .amt { color: var(--cyan); }
  .chip.c2 .av { background: rgba(255,107,0,0.16); color: var(--orange); }
  .chip.c2 .amt { color: var(--orange); }
  .chip.c2 .renew { font-size: 11px; color: var(--orange); font-weight:600; }
  .chip.c3 .av { background: rgba(20,230,244,0.14); color: var(--cyan); }
  .chip.c3 .amt { color: var(--white); }

  /* ── Footer strip ───────────────────────────────────────── */
  footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 24px;
    padding-top: 22px;
    border-top: 1px solid rgba(148,163,184,0.1);
    font-size: 12.5px;
    color: var(--gray);
    letter-spacing: 0.02em;
  }
  footer .stats { display:flex; gap: clamp(20px,3vw,44px); flex-wrap: wrap; }
  footer .stats b { color: var(--white); font-family: var(--font-display); font-weight:600; }
  footer .stats .lbl { display:block; font-size:11px; text-transform:uppercase; letter-spacing:0.12em; margin-top:2px; }

  /* reveal */
  .reveal { opacity: 0; transform: translateY(18px); animation: rise .8s cubic-bezier(.2,.7,.3,1) forwards; }
  @keyframes rise { to { opacity:1; transform:none; } }
  .d1{animation-delay:.05s}.d2{animation-delay:.15s}.d3{animation-delay:.25s}.d4{animation-delay:.35s}.d5{animation-delay:.45s}.d6{animation-delay:.55s}

  /* ── Responsive ─────────────────────────────────────────── */
  @media (max-width: 940px) {
    main { grid-template-columns: 1fr; padding-top: 24px; }
    .visual { height: 360px; order: -1; }
    .card { max-width: none; }
    .chip.c1 { left: 2%; } .chip.c2 { right: 2%; } .chip.c3 { left: 8%; }
    footer { flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 560px) {
    nav.top .nav-link { display: none; }
    .features { grid-template-columns: 1fr; }
    .brand .name { font-size: 18px; }
    h1 { font-size: 32px; }
  }
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; }
    .reveal { opacity:1; transform:none; }
  }
</style>
</head>
<body>
  <div class="bg"></div>

  <div class="shell" data-screen-label="SCOPS Start Page">
    <!-- Top bar -->
    <header class="reveal d1">
      <div class="brand">
        <img class="mark" src="{{asset('asset/owl_cyan.png')}}" alt="SCOPS owl logo" />
        <img class="wordmark" src="{{asset('asset/scops_wordmark.png')}}" alt="SCOPS" />
      </div>
      <nav class="top">
        <a class="nav-cta" href="#start">Get the app</a>
      </nav>
    </header>

    <!-- Hero -->
    <main>
      <section class="card reveal d2">
        <span class="eyebrow"><span class="dot"></span>Smart Control of Paid Systems</span>
        <h1>Every subscription,<br /><span class="glow">under control.</span></h1>
        <p class="lede">SCOPS brings all your recurring payments into one place — track spending, get renewal reminders before you're charged, and manage every linked card with confidence.</p>

        <div class="features" id="features">
          <div class="feature">
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg></span>
            Track recurring payments
          </div>
          <div class="feature">
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
            Timely renewal reminders
          </div>
          <div class="feature">
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>
            Manage linked cards
          </div>
          <div class="feature">
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21H4a1 1 0 0 1-1-1V3"/><rect x="7" y="11" width="3" height="6"/><rect x="13" y="7" width="3" height="10"/></svg></span>
            Analyze spending patterns
          </div>
        </div>

        <div class="actions">
          <a class="btn-primary" href="#start" id="start">
            Get started
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
          </a>
          <a class="btn-ghost" href="#how" id="how">
            <span class="play"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
            See how it works
          </a>
        </div>
      </section>

      <!-- Right visual -->
      <section class="visual reveal d3" aria-hidden="true">
        <div class="halo"></div>
        <div class="ring r3"></div>
        <div class="ring r2"></div>
        <div class="ring r1 spin"></div>
        <img class="owl" src="{{asset('asset/owl_cyan.png')}}" alt="" />

        <div class="chip c1">
          <span class="av"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
          <span class="meta"><b>Active users</b><span>Worldwide</span></span>
          <span class="amt">{{ $users }}</span>
        </div>
        <div class="chip c2">
          <span class="av"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
          <span class="meta"><b>Active subscriptions</b><span class="renew">Tracked & managed</span></span>
          <span class="amt">{{ $subscription}}</span>
        </div>
        <div class="chip c3">
          <span class="av"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></span>
          <span class="meta"><b>Payment success rate</b><span>Across all cards</span></span>
          <span class="amt">{{ $payment }}</span>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="reveal d5">
      <div>© 2026 SCOPS · Smart Control of Paid System</div>
    </footer>
  </div>
</body>
</html>
