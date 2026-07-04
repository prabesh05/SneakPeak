<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SneakPeak — Unleash The Beast</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:ital,wght@0,400;0,700;0,900;1,900&family=Barlow:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --red:    #E8192C;
      --black:  #111111;
      --white:  #F5F5F5;
      --grey:   #888;
    }

    html, body {
      height: 100%;
      background: var(--black);
      color: var(--white);
      font-family: 'Barlow', sans-serif;
      overflow: hidden;
    }

    /* ── Noise overlay ─────────────────────────────────── */
    body::before {
      content: '';
      position: fixed; inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
      opacity: .035;
      pointer-events: none;
      z-index: 100;
    }

    /* ── Layout ─────────────────────────────────────────── */
    .hero {
      position: relative;
      width: 100vw; height: 100vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    /* ── Nav ─────────────────────────────────────────────── */
    nav {
      position: relative;
      z-index: 10;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 40px;
      padding: 28px 60px;
    }

    .nav-tagline {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.05rem;
      letter-spacing: .35em;
      color: var(--white);
      opacity: 0;
      animation: fadeDown .6s .2s forwards;
    }

    .nav-tagline span {
      color: var(--red);
    }

    .nav-links {
      position: absolute;
      right: 60px;
      display: flex;
      gap: 32px;
      opacity: 0;
      animation: fadeDown .6s .4s forwards;
    }

    .nav-links a {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .95rem;
      font-weight: 700;
      letter-spacing: .12em;
      color: var(--white);
      text-decoration: none;
      text-transform: uppercase;
      transition: color .2s;
    }
    .nav-links a:hover { color: var(--red); }

    /* ── Stage ───────────────────────────────────────────── */
    .stage {
      position: relative;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Red vignette glow behind shoe */
    .glow {
      position: absolute;
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(232,25,44,.22) 0%, transparent 70%);
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }

    /* ── Decorative arrows ───────────────────────────────── */
    .arrows {
      position: absolute;
      inset: 0;
      pointer-events: none;
      z-index: 4;
    }

    .arrow {
      position: absolute;
      width: 0; height: 0;
      opacity: 0;
    }

    /* top-right cluster */
    .arr-tr1 {
      border-left: 18px solid var(--red);
      border-top: 11px solid transparent;
      border-bottom: 11px solid transparent;
      top: 12%; right: 24%;
      animation: arrowPop .5s 1s forwards;
    }
    .arr-tr2 {
      border-left: 14px solid var(--red);
      border-top: 8px solid transparent;
      border-bottom: 8px solid transparent;
      top: 18%; right: 27%;
      animation: arrowPop .5s 1.1s forwards;
    }
    .arr-tr3 {
      border-left: 10px solid var(--red);
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      top: 24%; right: 25%;
      animation: arrowPop .5s 1.2s forwards;
    }

    /* bottom-left cluster */
    .arr-bl1 {
      border-right: 18px solid var(--red);
      border-top: 11px solid transparent;
      border-bottom: 11px solid transparent;
      bottom: 22%; left: 18%;
      animation: arrowPop .5s 1.1s forwards;
    }
    .arr-bl2 {
      border-right: 14px solid var(--red);
      border-top: 8px solid transparent;
      border-bottom: 8px solid transparent;
      bottom: 30%; left: 21%;
      animation: arrowPop .5s 1.2s forwards;
    }
    .arr-bl3 {
      border-right: 10px solid var(--red);
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      bottom: 28%; left: 25%;
      animation: arrowPop .5s 1.3s forwards;
    }

    /* ── Combined Brand Type ─────────────────────────────── */
    .word-brand {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -40%);
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(80px, 11vw, 160px);
      line-height: .88;
      letter-spacing: .02em;
      z-index: 6;
      opacity: 0;
      animation: brandIn .7s .3s cubic-bezier(.16,1,.3,1) forwards;
      white-space: nowrap;
      text-shadow: 0 0 80px rgba(232,25,44,.4);
    }
    .word-brand .sneak { color: var(--red); }
    .word-brand .peak  { color: var(--white); }

    .word-tagline {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      top: calc(50% + clamp(50px,7vw,100px));
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 900;
      font-style: italic;
      font-size: clamp(13px, 1.4vw, 20px);
      letter-spacing: .25em;
      color: var(--white);
      text-transform: uppercase;
      white-space: nowrap;
      z-index: 6;
      opacity: 0;
      animation: fadeIn .6s 1.2s forwards;
    }

    /* ── CTA Button ─────────────────────────────────────── */
    .cta-wrap {
      position: absolute;
      bottom: 10%;
      left: 50%;
      transform: translateX(-50%);
      z-index: 10;
      opacity: 0;
      animation: fadeUp .6s 1.3s forwards;
    }

    .cta {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--red);
      color: var(--white);
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 900;
      font-size: 1rem;
      letter-spacing: .2em;
      text-transform: uppercase;
      text-decoration: none;
      padding: 16px 44px;
      border-radius: 50px;
      border: none;
      cursor: pointer;
      transition: transform .2s, box-shadow .2s, background .2s;
      box-shadow: 0 8px 40px rgba(232,25,44,.45);
      position: relative;
      overflow: hidden;
    }

    .cta::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.18) 50%, transparent 100%);
      transform: translateX(-100%);
      transition: transform .5s;
    }
    .cta:hover::before { transform: translateX(100%); }
    .cta:hover {
      transform: translateY(-3px) scale(1.04);
      box-shadow: 0 16px 50px rgba(232,25,44,.6);
    }
    .cta:active { transform: scale(.97); }

    /* ── Stripe accent lines ─────────────────────────────── */
    .stripe {
      position: absolute;
      background: var(--red);
      opacity: .07;
      pointer-events: none;
    }
    .stripe-h {
      width: 100%; height: 1px;
    }
    .stripe-h1 { top: 15%; }
    .stripe-h2 { bottom: 15%; }

    /* ── Keyframes ───────────────────────────────────────── */
    @keyframes fadeDown {
      from { opacity: 0; transform: translateY(-16px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translate(-50%, 20px); }
      to   { opacity: 1; transform: translate(-50%, 0); }
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }
    @keyframes brandIn {
      from { opacity: 0; transform: translate(-50%, -30%) scale(.85); }
      to   { opacity: 1; transform: translate(-50%, -40%) scale(1); }
    }
    @keyframes arrowPop {
      from { opacity: 0; transform: scale(0); }
      to   { opacity: 1; transform: scale(1); }
    }
    @keyframes float {
      0%,100% { transform: translate(-50%, -40%) translateY(0); }
      50%      { transform: translate(-50%, -40%) translateY(-14px); }
    }

    /* floating after intro */
    .word-brand.floats {
      animation: float 3.8s ease-in-out infinite;
    }

    /* ── Particle dots ───────────────────────────────────── */
    .particles {
      position: absolute;
      inset: 0;
      pointer-events: none;
      overflow: hidden;
    }
    .dot {
      position: absolute;
      border-radius: 50%;
      background: var(--red);
    }
  </style>
</head>
<body>

<section class="hero">

  <!-- Stripe accents -->
  <div class="stripe stripe-h stripe-h1"></div>
  <div class="stripe stripe-h stripe-h2"></div>

  <!-- Nav -->
  <nav>
    <span class="nav-tagline">Unleash <span>The Beast</span></span>
    <div class="nav-links">
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    </div>
  </nav>

  <!-- Stage -->
  <div class="stage">
    <div class="glow"></div>

    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <!-- Decorative arrows -->
    <div class="arrows">
      <div class="arrow arr-tr1"></div>
      <div class="arrow arr-tr2"></div>
      <div class="arrow arr-tr3"></div>
      <div class="arrow arr-bl1"></div>
      <div class="arrow arr-bl2"></div>
      <div class="arrow arr-bl3"></div>
    </div>

    <!-- Combined Brand Headline -->
    <div class="word-brand" id="brand"><span class="sneak">Sneak</span><span class="peak">Peak</span></div>
    <div class="word-tagline">New Definition</div>

<div class="cta-wrap">
    <a href="login.php" class="cta">Shop Now</a>
</div>
  </div>

</section>

<script>
  /* ── Floating brand after intro ─── */
  const brand = document.getElementById('brand');


  /* ── Subtle parallax on mousemove ─ */
  document.addEventListener('mousemove', (e) => {
    const cx = window.innerWidth / 2;
    const cy = window.innerHeight / 2;
    const dx = (e.clientX - cx) / cx;
    const dy = (e.clientY - cy) / cy;

    brand.style.marginLeft = `${dx * -18}px`;
    brand.style.marginTop  = `${dy * -12}px`;

    document.querySelector('.glow').style.transform = `translate(calc(-50% + ${dx*30}px), calc(-50% + ${dy*20}px))`;
  });

  /* ── Particle dots ─────────────── */
  const container = document.getElementById('particles');
  for (let i = 0; i < 22; i++) {
    const d = document.createElement('div');
    d.className = 'dot';
    const size = Math.random() * 4 + 1;
    d.style.cssText = `
      width:${size}px; height:${size}px;
      left:${Math.random()*100}%;
      top:${Math.random()*100}%;
      opacity:${Math.random()*.3+.05};
      animation: floatDot ${4+Math.random()*5}s ${Math.random()*4}s ease-in-out infinite alternate;
    `
    container.appendChild(d);
  }

  /* inject floatDot keyframe */
  const style = document.createElement('style');
  style.textContent = `
    @keyframes floatDot {
      from { transform: translateY(0) translateX(0); }
      to   { transform: translateY(${-20 - Math.random()*30}px) translateX(${(Math.random()-.5)*20}px); }
    }
  `;
  document.head.appendChild(style);
</script>
</body>
</html>