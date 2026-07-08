<?php session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us — SneakPeak</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg: #141414;
    --bg-alt: #1b1b1a;
    --fg: #f2efe9;
    --muted: #928d85;
    --line: #2d2b28;
    --accent: #ff4d1c;
    --accent-dim: #7a2a12;
  }

  *{ box-sizing:border-box; margin:0; padding:0; }

  html{ scroll-behavior:smooth; }

  body{
    background:var(--bg);
    color:var(--fg);
    font-family:'Inter', sans-serif;
    line-height:1.55;
    -webkit-font-smoothing:antialiased;
    overflow-x:hidden;
  }

  a{ color:inherit; }

  .wrap{
    max-width:1180px;
    margin:0 auto;
    padding:0 32px;
  }

  @media (max-width:640px){
    .wrap{ padding:0 20px; }
  }

  /* ---------- NAV ---------- */
  header{
    position:sticky;
    top:0;
    z-index:50;
    background:rgba(20,20,20,0.9);
    backdrop-filter:blur(8px);
    border-bottom:1px solid var(--line);
  }
  nav{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:20px 0;
  }
  .logo{
    font-family:'Archivo Black', sans-serif;
    font-size:20px;
    letter-spacing:0.5px;
    text-decoration:none;
  }
  .logo span{ color:var(--accent); }
  .nav-links{
    display:flex;
    gap:32px;
    list-style:none;
    font-size:14px;
    font-weight:500;
    color:var(--muted);
  }
  .nav-links a{ text-decoration:none; transition:color .2s ease; }
  .nav-links a:hover, .nav-links a:focus-visible{ color:var(--fg); }
  .nav-cta{
    font-size:13px;
    font-weight:700;
    letter-spacing:0.06em;
    text-transform:uppercase;
    padding:10px 18px;
    border:1px solid var(--fg);
    border-radius:2px;
    text-decoration:none;
    white-space:nowrap;
  }
  .nav-cta:hover{ background:var(--fg); color:var(--bg); }

  @media (max-width:720px){
    .nav-links{ display:none; }
  }

  /* ---------- HERO ---------- */
  .hero{
    padding:120px 0 100px;
    position:relative;
    border-bottom:1px solid var(--line);
  }
  .eyebrow{
    font-family:'Space Mono', monospace;
    font-size:12px;
    letter-spacing:0.18em;
    text-transform:uppercase;
    color:var(--accent);
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:28px;
  }
  .eyebrow::before{
    content:"";
    width:8px; height:8px;
    background:var(--accent);
    border-radius:50%;
    display:inline-block;
  }
  h1{
    font-family:'Archivo Black', sans-serif;
    font-size:clamp(40px, 7vw, 86px);
    line-height:0.98;
    letter-spacing:-0.01em;
    text-transform:uppercase;
    max-width:920px;
  }
  h1 em{
    font-style:normal;
    color:var(--accent);
  }
  .hero-sub{
    margin-top:28px;
    max-width:520px;
    color:var(--muted);
    font-size:17px;
  }

  /* ---------- MARQUEE STRIP ---------- */
  .strip{
    border-bottom:1px solid var(--line);
    background:var(--bg-alt);
    overflow:hidden;
    white-space:nowrap;
    padding:16px 0;
  }
  .strip-track{
    display:inline-block;
    animation:scroll 22s linear infinite;
    font-family:'Space Mono', monospace;
    font-size:13px;
    letter-spacing:0.08em;
    color:var(--muted);
    text-transform:uppercase;
  }
  .strip-track span{ margin:0 28px; }
  .strip-track span::after{ content:"—"; margin-left:28px; color:var(--line); }
  @keyframes scroll{
    from{ transform:translateX(0); }
    to{ transform:translateX(-50%); }
  }
  @media (prefers-reduced-motion: reduce){
    .strip-track{ animation:none; }
  }

  /* ---------- STORY ---------- */
  .story{
    padding:100px 0;
    border-bottom:1px solid var(--line);
  }
  .story-inner{
    display:grid;
    grid-template-columns:0.9fr 1.1fr;
    gap:80px;
  }
  .story-inner > div:first-child{
    padding-left:48px;
  }
  @media (max-width:860px){
    .story-inner{ grid-template-columns:1fr; gap:40px; }
    .story-inner > div:first-child{ padding-left:0; }
    .story{ padding:70px 0; }
  }
  .story h2{
    font-family:'Archivo Black', sans-serif;
    font-size:clamp(28px, 4vw, 40px);
    text-transform:uppercase;
    line-height:1.05;
  }
  .story-num{
    font-family:'Space Mono', monospace;
    color:var(--accent);
    font-size:13px;
    letter-spacing:0.1em;
    display:block;
    margin-bottom:18px;
  }
  .story p{
    color:var(--muted);
    max-width:520px;
    margin-top:18px;
    font-size:16px;
  }
  .story p + p{ margin-top:16px; }
  .story strong{ color:var(--fg); font-weight:600; }

  /* ---------- VALUES ---------- */
  .values{
    padding:100px 0;
    border-bottom:1px solid var(--line);
  }
  .section-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:24px;
    margin-bottom:56px;
    flex-wrap:wrap;
  }
  .section-head h2{
    font-family:'Archivo Black', sans-serif;
    font-size:clamp(28px, 4vw, 40px);
    text-transform:uppercase;
  }
  .section-head p{
    color:var(--muted);
    max-width:360px;
    font-size:15px;
  }
  .value-grid{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    border-top:1px solid var(--line);
    border-left:1px solid var(--line);
  }
  @media (max-width:820px){
    .value-grid{ grid-template-columns:1fr; }
  }
  .value-card{
    border-right:1px solid var(--line);
    border-bottom:1px solid var(--line);
    padding:36px 32px;
    transition:background .25s ease;
  }
  .value-card:hover{ background:var(--bg-alt); }
  .value-tag{
    font-family:'Space Mono', monospace;
    font-size:12px;
    color:var(--accent);
    letter-spacing:0.1em;
  }
  .value-card h3{
    font-family:'Archivo Black', sans-serif;
    font-size:19px;
    text-transform:uppercase;
    margin:16px 0 12px;
    letter-spacing:0.01em;
  }
  .value-card p{
    color:var(--muted);
    font-size:14.5px;
  }

  /* ---------- CTA ---------- */
  .cta{
    padding:110px 0;
    text-align:center;
  }
  .cta h2{
    font-family:'Archivo Black', sans-serif;
    font-size:clamp(32px, 6vw, 60px);
    text-transform:uppercase;
    line-height:1.02;
  }
  .cta h2 em{ font-style:normal; color:var(--accent); }
  .cta p{
    color:var(--muted);
    margin:20px auto 36px;
    max-width:420px;
  }
  .btn{
    display:inline-block;
    background:var(--accent);
    color:#141414;
    font-weight:700;
    font-size:14px;
    letter-spacing:0.06em;
    text-transform:uppercase;
    text-decoration:none;
    padding:16px 34px;
    border-radius:2px;
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .btn:hover, .btn:focus-visible{
    transform:translateY(-2px);
    box-shadow:0 10px 24px rgba(255,77,28,0.25);
  }

  /* ---------- FOOTER ---------- */
  footer{
    border-top:1px solid var(--line);
    padding:32px 0;
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:var(--muted);
    font-size:13px;
    flex-wrap:wrap;
    gap:12px;
  }

  :focus-visible{
    outline:2px solid var(--accent);
    outline-offset:3px;
  }

  /* ── User Dropdown (About theme) ─── */
  .user-dropdown-about { position: relative; display: inline-block; }
  .user-dropdown-toggle-about {
    cursor: pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    width:36px;
    height:36px;
    border-radius:50%;
    background:var(--accent);
    color:var(--bg);
    font-size:.85rem;
    font-weight:700;
    text-decoration:none;
    transition:background .2s;
  }
  .user-dropdown-toggle-about:hover { background:rgba(255,77,28,.8); }
  .user-dropdown-menu-about {
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
  .user-dropdown-menu-about.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }
  .user-dropdown-email-about {
    display: block;
    padding: 8px 16px;
    font-size: .82rem;
    color: var(--muted);
    border-bottom: 1px solid rgba(255,255,255,.08);
    white-space: nowrap;
  }
  .user-dropdown-menu-about a {
    display: block;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--fg);
    text-decoration: none;
    transition: color .2s, background .2s;
  }
  .user-dropdown-menu-about a:hover {
    color: var(--accent);
    background: rgba(255,77,28,.08);
  }
</style>
</head>
<body>

<header>
  <div class="wrap">
    <nav>
      <a class="logo" href="#">SNEAK<span>PEAK</span></a>
      <ul class="nav-links">
        <li><a href="#story">Our story</a></li>
        <li><a href="#values">What we check</a></li>
        <li><a href="#join">Shop</a></li>
      </ul>
      <div class="user-dropdown-about">
        <span class="user-dropdown-toggle-about" id="userDropdownToggle"><?= strtoupper($_SESSION['email'][0]) ?></span>
        <div class="user-dropdown-menu-about" id="userDropdownMenu">
          <span class="user-dropdown-email-about"><?= htmlspecialchars($_SESSION['email']) ?></span>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </nav>
  </div>
</header>

<section class="hero">
  <div class="wrap">
    <p class="eyebrow">About SneakPeak</p>
    <h1>No fakes.<br>No filler.<br><em>Just heat.</em></h1>
    <p class="hero-sub">SneakPeak is a marketplace for premium, 100% authentic shoes — every pair checked by hand before it ships to you.</p>
  </div>
</section>

<div class="strip" aria-hidden="true">
  <div class="strip-track">
    <span>Hand-checked</span>
    <span>Premium only</span>
    <span>Zero fakes</span>
    <span>Verified sellers</span>
    <span>Hand-checked</span>
    <span>Premium only</span>
    <span>Zero fakes</span>
    <span>Verified sellers</span>
  </div>
</div>

<section class="story" id="story">
  <div class="wrap story-inner">
    <div>
      <span class="story-num">01 / Why we exist</span>
      <h2>The resale market has a trust problem.</h2>
    </div>
    <div>
      <p>Shoe collecting should feel exciting, not risky. Too many buyers have opened a box expecting grails and found glue smudges, wrong stitching, or a size that doesn't match the label.</p>
      <p><strong>SneakPeak started as a fix for that.</strong> We built a marketplace where every single pair is inspected against the brand's original specs before it's listed — stitching, materials, box, tags, all of it.</p>
      <p>What's left is a shop where "premium" and "authentic" aren't marketing words. They're a promise we check on every pair, every time.</p>
    </div>
  </div>
</section>

<section class="values" id="values">
  <div class="wrap">
    <div class="section-head">
      <h2>What we actually check</h2>
      <p>Four checkpoints every pair passes before it reaches your door.</p>
    </div>
    <div class="value-grid">
      <div class="value-card">
        <span class="value-tag">CHECK 01</span>
        <h3>Authenticity</h3>
        <p>Stitching, materials, and manufacturing marks are compared against verified originals from the brand.</p>
      </div>
      <div class="value-card">
        <span class="value-tag">CHECK 02</span>
        <h3>Condition</h3>
        <p>Every pair is graded for wear, creasing, and sole condition, so the listing matches what actually arrives.</p>
      </div>
      <div class="value-card">
        <span class="value-tag">CHECK 03</span>
        <h3>Sourcing</h3>
        <p>We only work with sellers who can show a clear chain of ownership back to a legitimate retailer.</p>
      </div>
      <div class="value-card">
        <span class="value-tag">CHECK 04</span>
        <h3>Packaging</h3>
        <p>Original boxes, tags, and dust bags are inspected alongside the shoes — because the details matter too.</p>
      </div>
      <div class="value-card">
        <span class="value-tag">CHECK 05</span>
        <h3>Pricing</h3>
        <p>We track the resale market daily, so prices reflect real demand, not inflated hype.</p>
      </div>
      <div class="value-card">
        <span class="value-tag">CHECK 06</span>
        <h3>Delivery</h3>
        <p>Every order ships insured and tracked, sealed the moment it clears inspection.</p>
      </div>
    </div>
  </div>
</section>

<section class="cta" id="join">
  <div class="wrap">
    <h2>Your next pair,<br><em>guaranteed real.</em></h2>
    <p>Browse the current drop, or tell us the pair you're hunting for.</p>
    <a class="btn" href="products.php">Shop the collection</a>
  </div>
</section>

<footer>
  <div class="wrap" style="display:flex; justify-content:space-between; width:100%; flex-wrap:wrap; gap:12px;">
    <span>&copy; 2026 SneakPeak. Every pair checked, every time.</span>
    <span>Premium &amp; authentic shoes only.</span>
  </div>
</footer>

<script>
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