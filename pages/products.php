<?php
session_start();
include 'database.php'; // expects $conn to be a mysqli connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$products = array();
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY brand, id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SneakPeak — Shop</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:ital,wght@0,400;0,700;0,900;1,900&family=Barlow:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }


    :root {
      --red:   #E8192C;
      --black: #111111;
      --card:  #181818;
      --white: #F5F5F5;
      --grey:  #888;
      --border: rgba(255,255,255,.07);
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--black);
      color: var(--white);
      font-family: 'Barlow', sans-serif;
      min-height: 100vh;
    }

    /* ── Noise overlay ─── */
    body::before {
      content: '';
      position: fixed; inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
      opacity: .035;
      pointer-events: none;
      z-index: 999;
    }

    /* ── Navbar ─── */
    nav {
      position: sticky;
      top: 0;
      z-index: 50;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 22px 60px;
      background: rgba(17,17,17,.92);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border);
    }

    .nav-logo {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.8rem;
      letter-spacing: .06em;
      text-decoration: none;
    }
    .nav-logo .sneak { color: var(--red); }
    .nav-logo .peak  { color: var(--white); }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 36px;
    }

    .nav-links a {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .95rem;
      font-weight: 700;
      letter-spacing: .12em;
      color: var(--grey);
      text-decoration: none;
      text-transform: uppercase;
      transition: color .2s;
    }
    .nav-links a:hover,
    .nav-links a.active { color: var(--white); }

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

    .nav-cart {
      position: relative;
      cursor: pointer;
      color: var(--white);
      transition: color .2s;
    }
    .nav-cart:hover { color: var(--red); }

    .nav-cart svg {
      width: 22px; height: 22px;
      fill: none;
      stroke: currentColor;
      stroke-width: 1.8;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .cart-badge {
      position: absolute;
      top: -6px; right: -8px;
      background: var(--red);
      color: var(--white);
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .65rem;
      width: 16px; height: 16px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
    }

    /* ── Search bar (in nav) ─── */
    .nav-search {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(245,245,245,.06);
      border: 1px solid rgba(245,245,245,.15);
      border-radius: 50px;
      padding: 8px 16px;
      transition: border-color .2s, background .2s;
    }
    .nav-search:focus-within {
      border-color: var(--red);
      background: rgba(245,245,245,.1);
    }

    .nav-search input {
      background: transparent;
      border: none;
      outline: none;
      color: var(--white);
      font-family: 'Barlow', sans-serif;
      font-size: .9rem;
      width: 140px;
    }
    .nav-search input::placeholder { color: var(--grey); }

    .nav-search button {
      background: none;
      border: none;
      cursor: pointer;
      display: flex;
      padding: 0;
    }
    .nav-search button svg {
      width: 17px; height: 17px;
      stroke: var(--white);
      fill: none;
      stroke-width: 2;
      transition: stroke .2s;
    }
    .nav-search button:hover svg { stroke: var(--red); }

    @media (max-width: 700px) {
      .nav-search input { width: 90px; }
    }

    /* ── Page hero strip ─── */
    .page-hero {
      position: relative;
      padding: 56px 60px 40px;
      overflow: hidden;
    }

    .page-hero::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 1px;
      background: var(--border);
    }

    .hero-eyebrow {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .8rem;
      letter-spacing: .3em;
      color: var(--red);
      text-transform: uppercase;
      margin-bottom: 10px;
      opacity: 0;
      animation: fadeUp .5s .1s forwards;
    }

    .hero-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(48px, 7vw, 90px);
      line-height: .9;
      letter-spacing: .02em;
      opacity: 0;
      animation: fadeUp .6s .2s forwards;
    }

    .hero-title em {
      color: var(--red);
      font-style: normal;
    }

    .hero-sub {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem;
      color: var(--grey);
      letter-spacing: .08em;
      margin-top: 14px;
      text-transform: uppercase;
      opacity: 0;
      animation: fadeUp .5s .35s forwards;
    }

    /* red accent line top-right */
    .hero-accent {
      position: absolute;
      top: 0; right: 0;
      width: 260px; height: 3px;
      background: var(--red);
      opacity: .7;
    }

    /* ── Brand filter bar ─── */
    .brand-bar {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 28px 60px;
      overflow-x: auto;
      scrollbar-width: none;
      border-bottom: 1px solid var(--border);
    }
    .brand-bar::-webkit-scrollbar { display: none; }

    .brand-btn {
      flex-shrink: 0;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .85rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      color: var(--grey);
      background: transparent;
      border: 1px solid var(--border);
      padding: 9px 22px;
      border-radius: 50px;
      cursor: pointer;
      transition: color .2s, border-color .2s, background .2s;
      white-space: nowrap;
    }
    .brand-btn:hover {
      color: var(--white);
      border-color: rgba(255,255,255,.25);
    }
    .brand-btn.active {
      background: var(--red);
      color: var(--white);
      border-color: var(--red);
    }

    /* ── Section label ─── */
    .section-label {
      display: flex;
      align-items: center;
      gap: 18px;
      padding: 36px 60px 20px;
    }

    .section-label h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.5rem;
      letter-spacing: .08em;
      color: var(--white);
    }

    .section-label .label-line {
      flex: 1;
      height: 1px;
      background: var(--border);
    }

    .section-label .label-count {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .85rem;
      letter-spacing: .1em;
      color: var(--grey);
      text-transform: uppercase;
    }

    /* ── Products grid ─── */
    .products-wrap {
      padding: 0 60px 80px;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
      gap: 24px;
    }

    /* ── Product card ─── */
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      cursor: pointer;
      transition: transform .25s, border-color .25s, box-shadow .25s;
      position: relative;
      opacity: 0;
      animation: cardIn .45s forwards;
    }

    .card:hover {
      transform: translateY(-6px);
      border-color: rgba(232,25,44,.35);
      box-shadow: 0 20px 50px rgba(0,0,0,.5), 0 0 0 1px rgba(232,25,44,.15);
    }

    .card-img {
      position: relative;
      background: #1c1c1c;
      height: 210px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .card-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .4s;
    }
    .card:hover .card-img img { transform: scale(1.06); }

    /* coloured overlay shimmer on hover */
    .card-img::after {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 50% 50%, rgba(232,25,44,.12), transparent 70%);
      opacity: 0;
      transition: opacity .3s;
    }
    .card:hover .card-img::after { opacity: 1; }

    /* badge */
    .badge {
      position: absolute;
      top: 14px; left: 14px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .72rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 4px;
      z-index: 2;
    }
    .badge-hot   { background: var(--red); color: var(--white); }
    .badge-new   { background: #1a6fff; color: var(--white); }
    .badge-sale  { background: #e8a319; color: #111; }

    .card-body {
      padding: 18px 20px 20px;
    }

    .card-brand {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .75rem;
      letter-spacing: .2em;
      color: var(--red);
      text-transform: uppercase;
      margin-bottom: 5px;
    }

    .card-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 900;
      font-size: 1.15rem;
      letter-spacing: .03em;
      color: var(--white);
      line-height: 1.2;
      margin-bottom: 4px;
    }

    .card-colorway {
      font-size: .82rem;
      color: var(--grey);
      margin-bottom: 14px;
    }

    .card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .card-price {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.5rem;
      letter-spacing: .04em;
      color: var(--white);
    }

    .card-add {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: var(--red);
      border: none;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 4px 16px rgba(232,25,44,.4);
    }
    .card-add:hover {
      transform: scale(1.12);
      box-shadow: 0 6px 24px rgba(232,25,44,.6);
    }
    .card-add svg {
      width: 16px; height: 16px;
      fill: none;
      stroke: #fff;
      stroke-width: 2.2;
      stroke-linecap: round;
    }

    /* ── No results ─── */
    .no-results {
      grid-column: 1 / -1;
      text-align: center;
      padding: 80px 20px;
      display: none;
    }
    .no-results h3 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2rem;
      color: var(--grey);
      letter-spacing: .1em;
    }
    .no-results p {
      color: var(--grey);
      margin-top: 8px;
      font-size: .9rem;
    }

    /* ── Stripe accents ─── */
    .stripe-h {
      width: 100%; height: 1px;
      background: var(--red);
      opacity: .07;
    }

    /* ── Keyframes ─── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes cardIn {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Responsive ─── */
    @media (max-width: 768px) {
      nav, .page-hero, .brand-bar, .section-label, .products-wrap {
        padding-left: 20px;
        padding-right: 20px;
      }
      .products-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; }
      .card-img { height: 150px; }
    }
  </style>
</head>
<body>

<!-- ── Navbar ─── -->
<nav>
  <a href="index.php" class="nav-logo">
    <span class="sneak">Sneak</span><span class="peak">Peak</span>
  </a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="products.php" class="active">Shop</a>
    <a href="AboutUs.php">About</a>
    <a href="#">Contact</a>
    <form class="nav-search" action="search.php" method="GET">
      <input type="text" name="query" placeholder="Search sneakers..." required>
      <button type="submit" aria-label="Search">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
    </form>
    <a href="cart.php" class="nav-cart" title="View Cart">
      <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <span class="cart-badge">0</span>
    </a>
    <div class="user-dropdown">
      <span class="user-dropdown-toggle" id="userDropdownToggle"><?= strtoupper($_SESSION['email'][0]) ?></span>
      <div class="user-dropdown-menu" id="userDropdownMenu">
        <span class="user-dropdown-email"><?= htmlspecialchars($_SESSION['email']) ?></span>
        <a href="login.php">Logout</a>
      </div>
    </div>
  </div>
</nav>



<!-- ── Brand filter bar ─── -->
<div class="brand-bar">
  <button class="brand-btn active" data-brand="all">All</button>
  <button class="brand-btn" data-brand="nike">Nike</button>
  <button class="brand-btn" data-brand="adidas">Adidas</button>
  <button class="brand-btn" data-brand="new balance">New Balance</button>
  <button class="brand-btn" data-brand="under armour">Under Armour</button>
  <button class="brand-btn" data-brand="converse">Converse</button>
  <button class="brand-btn" data-brand="crocs">Crocs</button>
  <button class="brand-btn" data-brand="puma">Puma</button>
  <button class="brand-btn" data-brand="vans">Vans</button>
</div>

<!-- ── Section label ─── -->
<div class="section-label">
  <h2 id="section-title">All Drops</h2>
  <div class="label-line"></div>
  <span class="label-count" id="count-label">24 styles</span>
</div>

<!-- ── Products grid ─── -->
<div class="products-wrap">
  <div class="products-grid" id="products-grid">
    <!-- Cards injected by JS -->
  </div>
</div>

<script>
  /* -- Product data (from database) -- */
  const products = <?php echo json_encode($products); ?>;

  /* ── State ─── */
  let activeBrand = 'all';

  function formatPrice(value) {
    const number = Number(value);
    return isNaN(number) ? value : `Rs.${number.toLocaleString('en-IN')}`;
  }

  /* ── Render cards ─── */
  function renderCards(list) {                                         
    const grid = document.getElementById('products-grid');
    const countLabel = document.getElementById('count-label');
    const sectionTitle = document.getElementById('section-title');

    grid.innerHTML = '';

    if (list.length === 0) {
      grid.innerHTML = `
        <div class="no-results" style="display:block">
          <h3>No sneakers found</h3>
          <p>Try a different brand filter.</p>
        </div>`;
      countLabel.textContent = '0 styles';
      return;
    }

    countLabel.textContent = `${list.length} style${list.length !== 1 ? 's' : ''}`;
    sectionTitle.textContent = activeBrand === 'all'
      ? 'Trending Now'
      : activeBrand.replace(/\b\w/g, c => c.toUpperCase()) + ' Collection';

    list.forEach((p, i) => {
      const card = document.createElement('div');
      card.className = 'card';
      card.style.animationDelay = `${i * 55}ms`;

      const badgeHtml = p.badge
        ? `<span class="badge badge-${p.badge}">${p.badge === 'hot' ? '🔥 Hot' : p.badge === 'new' ? '✦ New' : '% Sale'}</span>`
        : '';

      card.innerHTML = `
        <div class="card-img" onclick="event.stopPropagation(); window.location.href='product-detail.php?id=${p.id}'">
          ${badgeHtml}
          <img src="${p.img}" alt="${p.name}" loading="lazy" />
        </div>
        <div class="card-body" onclick="event.stopPropagation(); window.location.href='product-detail.php?id=${p.id}'">
          <p class="card-brand">${p.brand}</p>
          <p class="card-name">${p.name}</p>
          <p class="card-colorway">${p.colorway}</p>
          <div class="card-footer">
            <span class="card-price">${formatPrice(p.price)}</span>
          </div>
        </div>`;

      grid.appendChild(card);
    });
  }

  /* ── Filter logic ─── */
  function filterProducts(brand) {
    activeBrand = brand;
    let filtered;

    if (brand === 'all') {
      /* Trending: first 2 from each brand */
      const seen = {};
      filtered = products.filter(p => {
        seen[p.brand] = (seen[p.brand] || 0) + 1;
        return seen[p.brand] <= 2;
      });
    } else {
      filtered = products.filter(p => p.brand === brand);
    }

    renderCards(filtered);
  }

  /* ── Brand button clicks ─── */
  document.querySelectorAll('.brand-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.brand-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filterProducts(btn.dataset.brand);
    });
  });

  /* ── Cart ─── */
  function addToCart(id) {
    cartCount++;
    document.querySelector('.cart-badge').textContent = cartCount;




    /* quick pulse feedback */
    const badge = document.querySelector('.cart-badge');
    badge.style.transform = 'scale(1.5)';
    badge.style.transition = 'transform .15s';
    setTimeout(() => { badge.style.transform = 'scale(1)'; }, 150);
  }

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

  /* ── Init ─── */
  filterProducts('all');
</script>

</body>
</html>