<?php session_start(); ?>
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
      min-height: 100%;
      background: var(--black);
      color: var(--white);
      font-family: 'Barlow', sans-serif;
      overflow-x: hidden;
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

    /* ── User Dropdown ─── */
    .user-dropdown { position: relative; display: inline-block; }
    .user-dropdown-toggle {
      cursor: pointer;
      display:flex;
      align-items:center;
      justify-content:center;
      width:36px;
      height:36px;
      border-radius:50%;
      background:var(--red);
      color:var(--white);
      font-size:.85rem;
      font-weight:700;
      font-family: 'Barlow Condensed', sans-serif;
      text-decoration: none;
      transition: background .2s;
    }
    .user-dropdown-toggle:hover { background:rgba(232,25,44,.8); }
    .user-dropdown-menu {
      position: absolute;
      top: calc(100% + 8px);
      right: 0;
      background: #1a1a1a;
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 8px;
      padding: 6px 0;
      min-width: 170px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(4px);
      transition: opacity .2s, visibility .2s, transform .2s;
      z-index: 1000;
      box-shadow: 0 8px 24px rgba(0,0,0,.5);
    }
    .user-dropdown-menu.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .user-dropdown-email {
      display: block;
      padding: 8px 16px;
      font-size: .82rem;
      color: var(--grey);
      border-bottom: 1px solid rgba(255,255,255,.08);
      white-space: nowrap;
    }
    .user-dropdown-menu a {
      display: block;
      padding: 8px 16px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .9rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--white);
      text-decoration: none;
      transition: color .2s, background .2s;
    }
    .user-dropdown-menu a:hover {
      color: var(--red);
      background: rgba(232,25,44,.08);
    }

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

    /* ── Footer ──────────────────────────────────────────── */
    footer {
      position: relative;
      z-index: 10;
      background: #0b0b0b;
      border-top: 1px solid rgba(232,25,44,.25);
      padding: 56px 60px 28px;
    }

    .footer-inner {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 40px;
    }

    .footer-brand {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2rem;
      letter-spacing: .04em;
    }
    .footer-brand .sneak { color: var(--red); }
    .footer-brand .peak  { color: var(--white); }

    .footer-tagline {
      margin-top: 6px;
      font-family: 'Barlow Condensed', sans-serif;
      font-style: italic;
      font-weight: 700;
      font-size: .8rem;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--grey);
    }

    .footer-email {
      margin-top: 18px;
    }

    .footer-email a {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .95rem;
      letter-spacing: .06em;
      color: var(--white);
      text-decoration: none;
      border-bottom: 1px solid rgba(245,245,245,.25);
      padding-bottom: 2px;
      transition: color .2s, border-color .2s;
    }
    .footer-email a:hover {
      color: var(--red);
      border-color: var(--red);
    }

    .footer-social {
      display: flex;
      flex-direction: column;
      gap: 14px;
      align-items: flex-start;
    }

    .footer-social-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .8rem;
      letter-spacing: .25em;
      text-transform: uppercase;
      color: var(--grey);
    }

    .footer-social-icons {
      display: flex;
      gap: 16px;
    }

    .footer-social-icons a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px; height: 40px;
      border-radius: 50%;
      border: 1px solid rgba(245,245,245,.2);
      color: var(--white);
      transition: border-color .2s, color .2s, transform .2s, background .2s;
    }
    .footer-social-icons a:hover {
      border-color: var(--red);
      background: rgba(232,25,44,.12);
      color: var(--red);
      transform: translateY(-3px);
    }
    .footer-social-icons svg {
      width: 18px; height: 18px;
      fill: currentColor;
    }

    .footer-bottom {
      max-width: 1200px;
      margin: 40px auto 0;
      padding-top: 20px;
      border-top: 1px solid rgba(245,245,245,.08);
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .8rem;
      letter-spacing: .06em;
      color: var(--grey);
    }

    .footer-bottom a {
      color: var(--grey);
      text-decoration: none;
      transition: color .2s;
    }
    .footer-bottom a:hover { color: var(--red); }

    @media (max-width: 640px) {
      footer { padding: 44px 24px 24px; }
      .footer-inner { gap: 32px; }
      .footer-social { align-items: flex-start; }
    }

    /* ── About / Feature Section ──────────────────────────── */
    .about {
      position: relative;
      background: var(--black);
      padding: 140px 60px;
      overflow: hidden;
    }

    .about::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; height: 1px;
      background: linear-gradient(90deg, transparent, rgba(232,25,44,.4), transparent);
    }

    .about-inner {
      max-width: 1240px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 140px;
    }

    .about-row {
      display: flex;
      align-items: center;
      gap: 80px;
    }

    .about-row.reverse {
      flex-direction: row-reverse;
    }

    .about-text {
      flex: 1 1 0;
      min-width: 0;
    }

    .about-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .8rem;
      letter-spacing: .3em;
      text-transform: uppercase;
      color: var(--red);
      margin-bottom: 18px;
    }
    .about-eyebrow::before {
      content: '';
      width: 28px; height: 2px;
      background: var(--red);
    }

    .about-text h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(34px, 4vw, 52px);
      line-height: 1.05;
      letter-spacing: .01em;
      color: var(--white);
      margin-bottom: 24px;
    }

    .about-text p {
      font-family: 'Barlow', sans-serif;
      font-size: 1.05rem;
      line-height: 1.75;
      color: #c7c7c7;
      max-width: 46ch;
    }

    .about-text strong {
      color: var(--white);
      font-weight: 600;
    }
    .about-text strong.brand-sneak { color: var(--red); }

    .about-media {
      flex: 1 1 0;
      min-width: 0;
      position: relative;
      aspect-ratio: 4 / 3;
      border-radius: 18px;
      overflow: hidden;
      background:
        radial-gradient(circle at 30% 20%, rgba(232,25,44,.25), transparent 55%),
        linear-gradient(155deg, #1c1c1c 0%, #0d0d0d 70%);
      border: 1px solid rgba(245,245,245,.08);
      box-shadow: 0 30px 70px rgba(0,0,0,.5);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .about-media img {
      width: 100%; height: 100%;
      object-fit: cover;
      display: block;
    }

    .about-media .media-mark {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      color: rgba(245,245,245,.35);
      text-align: center;
      padding: 20px;
    }

    .about-media .media-mark svg {
      width: 44px; height: 44px;
      stroke: var(--red);
      opacity: .8;
    }

    .about-media .media-mark span {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .78rem;
      letter-spacing: .16em;
      text-transform: uppercase;
    }

    .about-media::after {
      content: '';
      position: absolute;
      width: 90px; height: 90px;
      border-top: 3px solid var(--red);
      border-left: 3px solid var(--red);
      top: -1px; left: -1px;
      border-radius: 18px 0 0 0;
      opacity: .5;
    }

    @media (max-width: 900px) {
      .about { padding: 100px 24px; }
      .about-inner { gap: 90px; }
      .about-row, .about-row.reverse {
        flex-direction: column;
        gap: 36px;
      }
      .about-text p { max-width: 60ch; }
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
      <a href="products.php">Shop</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-dropdown">
          <span class="user-dropdown-toggle" id="userDropdownToggle"><?= strtoupper($_SESSION['email'][0]) ?></span>
          <div class="user-dropdown-menu" id="userDropdownMenu">
            <span class="user-dropdown-email"><?= htmlspecialchars($_SESSION['email']) ?></span>
            <a href="logout.php">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
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
    <a href="products.php" class="cta">Shop Now</a>
</div>
  </div>

</section>

<section class="about">
  <div class="about-inner">

    <div class="about-row">
      <div class="about-text">
        <span class="about-eyebrow">Who We Are</span>
        <h2>All The Kicks You Love, In One Place</h2>
        <p>
          At <strong class="brand-sneak">Sneak</strong><strong>Peak</strong>, we bring you a curated collection of the latest and most comfortable footwear from trusted brands — all in one place. Our mission is to make quality sneakers accessible to everyone across Nepal, whether you're after everyday comfort, statement styles, or something for a special drop.
        </p>
      </div>
      <div class="about-media">
         <img src="https://cdn.pixabay.com/photo/2019/11/27/16/47/jordan-4657349_960_720.jpg" alt="Featured sneaker"> 
        
      </div>
    </div>

    <div class="about-row reverse">
      <div class="about-text">
        <span class="about-eyebrow">Beyond Footwear</span>
        <h2>Style That Speaks You</h2>
        <p>
          At <strong class="brand-sneak">Sneak</strong><strong>Peak</strong>, we're passionate about authentic footwear. We bring you a carefully curated collection of genuine sneakers and shoes from trusted brands, ensuring quality, comfort, and style with every pair. Our mission is simple: to provide 100% authentic footwear at the best prices, so you can step out with confidence without overpaying.
        </p>
      </div>
      <div class="about-media">
       <img src="https://static.nike.com/a/images/f_auto,cs_srgb/w_1920,c_limit/77e79006-1593-4174-8aa5-bdce318eb28b/air-jordan-1-2022-lost-and-found-chicago-the-inspiration-behind-the-design.jpg" alt="Apparel collection"> 

      </div>
    </div>

  </div>
</section>

<footer>
  <div class="footer-inner">
    <div>
      <div class="footer-brand"><span class="sneak">Sneak</span><span class="peak">Peak</span></div>
      <div class="footer-tagline">Unleash The Beast</div>
      <div class="footer-email">
        <a href="mailto:shop@sneakpeak.np">shop@sneakpeak.np</a>
      </div>
    </div>

    <div class="footer-social">
      <span class="footer-social-label">Follow Us</span>
      <div class="footer-social-icons">
        <a href="#" rel="noopener" aria-label="Instagram">
          <svg viewBox="0 0 24 24"><path d="M12 2c2.72 0 3.06.01 4.12.06 1.07.05 1.79.22 2.43.46.66.26 1.22.6 1.77 1.15.55.55.9 1.11 1.15 1.77.24.64.41 1.36.46 2.43.05 1.06.06 1.4.06 4.12s-.01 3.06-.06 4.12c-.05 1.07-.22 1.79-.46 2.43-.26.66-.6 1.22-1.15 1.77-.55.55-1.11.9-1.77 1.15-.64.24-1.36.41-2.43.46-1.06.05-1.4.06-4.12.06s-3.06-.01-4.12-.06c-1.07-.05-1.79-.22-2.43-.46-.66-.26-1.22-.6-1.77-1.15-.55-.55-.9-1.11-1.15-1.77-.24-.64-.41-1.36-.46-2.43C2.01 15.06 2 14.72 2 12s.01-3.06.06-4.12c.05-1.07.22-1.79.46-2.43.26-.66.6-1.22 1.15-1.77.55-.55 1.11-.9 1.77-1.15.64-.24 1.36-.41 2.43-.46C8.94 2.01 9.28 2 12 2zm0 1.8c-2.67 0-2.99.01-4.04.06-.87.04-1.34.18-1.66.3-.42.16-.71.36-1.03.68-.32.32-.52.61-.68 1.03-.12.32-.26.79-.3 1.66-.05 1.05-.06 1.37-.06 4.04s.01 2.99.06 4.04c.04.87.18 1.34.3 1.66.16.42.36.71.68 1.03.32.32.61.52 1.03.68.32.12.79.26 1.66.3 1.05.05 1.37.06 4.04.06s2.99-.01 4.04-.06c.87-.04 1.34-.18 1.66-.3.42-.16.71-.36 1.03-.68.32-.32.52-.61.68-1.03.12-.32.26-.79.3-1.66.05-1.05.06-1.37.06-4.04s-.01-2.99-.06-4.04c-.04-.87-.18-1.34-.3-1.66-.16-.42-.36-.71-.68-1.03-.32-.32-.61-.52-1.03-.68-.32-.12-.79-.26-1.66-.3C14.99 3.81 14.67 3.8 12 3.8zm0 3.05a5.15 5.15 0 1 1 0 10.3 5.15 5.15 0 0 1 0-10.3zm0 1.8a3.35 3.35 0 1 0 0 6.7 3.35 3.35 0 0 0 0-6.7zm5.36-1.98a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0z"/></svg>
        </a>
        <a href="#" rel="noopener" aria-label="X (Twitter)">
          <svg viewBox="0 0 24 24"><path d="M18.9 2H22l-7.6 8.7L23.3 22h-6.9l-5.4-6.9L4.8 22H1.7l8.1-9.3L1 2h7.1l4.9 6.3L18.9 2zm-1.2 18h1.9L7.4 4H5.4l12.3 16z"/></svg>
        </a>
        <a href="#" rel="noopener" aria-label="TikTok">
          <svg viewBox="0 0 24 24"><path d="M16.6 2h-3.2v13.4a3.1 3.1 0 1 1-2.2-2.97V9.1a6.3 6.3 0 1 0 5.4 6.24V8.3a8.1 8.1 0 0 0 4.6 1.44V6.5a4.9 4.9 0 0 1-4.6-4.5z"/></svg>
        </a>
        <a href="#" rel="noopener" aria-label="Facebook">
          <svg viewBox="0 0 24 24"><path d="M13.5 21v-8.1h2.7l.4-3.2h-3.1V7.7c0-.9.25-1.55 1.58-1.55h1.68V3.3C15.9 3.2 14.9 3.1 13.7 3.1c-2.5 0-4.2 1.53-4.2 4.3v2.3H6.8v3.2h2.7V21h4z"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <span>&copy; <span id="year"></span> SneakPeak. All rights reserved.</span>
    <span>
      <a href="login.php">Login</a> &nbsp;·&nbsp; <a href="register.php">Register</a>
    </span>
  </div>
</footer>

<script>
  document.getElementById('year').textContent = new Date().getFullYear();

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

  /* ── User dropdown ─── */
  document.addEventListener('click', function(e){
    var toggle = document.getElementById('userDropdownToggle');
    var menu = document.getElementById('userDropdownMenu');
    if (!toggle || !menu) return;
    if (toggle.contains(e.target)) {
      menu.classList.toggle('show');
    } else if (!menu.contains(e.target)) {
      menu.classList.remove('show');
    }
  });
</script>
</body>
</html>