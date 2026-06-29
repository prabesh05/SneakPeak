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
    <a href="#" class="active">Shop</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <a href="cart.php" class="nav-cart" title="View Cart">
      <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <span class="cart-badge">0</span>
    </a>
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
  /* ── Product data ─── */
  const products = [
    /* NIKE */
    {
      id: 1, brand: 'nike',
      name: 'Air Jordan 1 Retro High OG',
      colorway: 'Chicago / Red & White',
      price: 180,
      badge: 'hot',
      img: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80'
    },
    {
      id: 2, brand: 'nike',
      name: 'Nike Dunk Low',
      colorway: 'Panda / Black & White',
      price: 110,
      badge: null,
      img: 'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=600&q=80'
    },
    {
      id: 3, brand: 'nike',
      name: 'Air Force 1 \'07',
      colorway: 'Triple White',
      price: 90,
      badge: null,
      img: 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&q=80'
    },
    {
      id: 4, brand: 'nike',
      name: 'Nike Air Max 90',
      colorway: 'Infrared / White & Red',
      price: 130,
      badge: 'new',
      img: 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=600&q=80'
    },

    /* ADIDAS */
    {
      id: 5, brand: 'adidas',
      name: 'Adidas Ultraboost 23',
      colorway: 'Core Black / Cloud White',
      price: 190,
      badge: 'hot',
      img: 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&q=80'
    },
    {
      id: 6, brand: 'adidas',
      name: 'Adidas Stan Smith',
      colorway: 'White / Green',
      price: 100,
      badge: null,
      img: 'https://images.stockx.com/images/adidas-Stan-Smith-Footwear-White-Core-Black.jpg?fit=fill&bg=FFFFFF&w=480&h=320&q=60&dpr=1&trim=color&updated_at=1664882949'
    },
    {
      id: 7, brand: 'adidas',
      name: 'Adidas Samba OG',
      colorway: 'Core Black / Gum',
      price: 100,
      badge: 'hot',
      img: 'https://images.unsplash.com/photo-1584735175315-9d5df23be4be?w=600&q=80'
    },
    {
      id: 8, brand: 'adidas',
      name: 'Adidas Forum Low',
      colorway: 'Cloud White / Collegiate Navy',
      price: 90,
      badge: 'new',
      img: 'https://images.unsplash.com/photo-1620155176061-52b66f842a39?w=600&q=80'
    },

    /* NEW BALANCE */
    {
      id: 9, brand: 'new balance',
      name: 'New Balance 550',
      colorway: 'White / Green',
      price: 110,
      badge: 'hot',
      img: 'https://images.unsplash.com/photo-1608231387042-66d1773d3028?w=600&q=80'
    },
    {
      id: 10, brand: 'new balance',
      name: 'New Balance 990v6',
      colorway: 'Grey / Navy',
      price: 185,
      badge: null,
      img: 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&q=80'
    },
    {
      id: 11, brand: 'new balance',
      name: 'New Balance 2002R',
      colorway: 'Sea Salt / Beige',
      price: 150,
      badge: 'new',
      img: 'https://images.unsplash.com/photo-1562183241-b937e95585b6?w=600&q=80'
    },

    /* UNDER ARMOUR */
    {
      id: 12, brand: 'under armour',
      name: 'UA Curry 11',
      colorway: 'Performance Blue',
      price: 160,
      badge: 'new',
      img: 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&q=80'
    },
    {
      id: 13, brand: 'under armour',
      name: 'UA HOVR Phantom 3',
      colorway: 'Black / Metallic Silver',
      price: 140,
      badge: null,
      img: 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=600&q=80'
    },
    {
      id: 14, brand: 'under armour',
      name: 'UA SlipSpeed Mega',
      colorway: 'Pitch Gray / White',
      price: 120,
      badge: 'sale',
      img: 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=600&q=80'
    },

    /* CONVERSE */
    {
      id: 15, brand: 'converse',
      name: 'Chuck Taylor All Star Hi',
      colorway: 'Classic Black',
      price: 65,
      badge: null,
      img: 'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=600&q=80'
    },
    {
      id: 16, brand: 'converse',
      name: 'Converse Run Star Hike',
      colorway: 'White / Black / Gum',
      price: 100,
      badge: 'hot',
      img: 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=600&q=80'
    },
    {
      id: 17, brand: 'converse',
      name: 'Chuck 70 High Top',
      colorway: 'Vintage Canvas / White',
      price: 85,
      badge: 'new',
      img: 'https://images.unsplash.com/photo-1445632283550-de097b87ad43?w=600&q=80'
    },

    /* CROCS */
    {
      id: 18, brand: 'crocs',
      name: 'Classic Clog',
      colorway: 'Neon Yellow',
      price: 55,
      badge: 'hot',
      img: 'https://media.crocs.com/images/f_auto%2Cq_auto%2Cw_900%2Ch_900%2Cc_pad%2Cb_transparent/products/10001_3YF_ALT100/crocs.jpg'
    },
    
    {
      id: 20, brand: 'crocs',
      name: 'Classic Mega Crush Clog',
      colorway: 'Black',
      price: 80,
      badge: 'sale',
      img: 'https://media.crocs.com/images/f_auto%2Cq_auto%2Cw_900%2Ch_900%2Cc_pad%2Cb_transparent/products/10001_5EP_ALT100/crocs.jpg'
    },
    {
      id: 20, brand: 'crocs',
      name: 'Echo Clog',
      colorway: 'Black',
      price: 80,
      badge: null,
      img: 'https://media.crocs.com/images/f_auto%2Cq_auto%2Cw_900%2Ch_900%2Cc_pad%2Cb_transparent/products/207937_001_ALT100/crocs.jpg'
    },
    

    /* PUMA */
    {
      id: 21, brand: 'puma',
      name: 'Puma Suede Classic XXI',
      colorway: 'Black / Puma Team Gold',
      price: 75,
      badge: null,
      img: 'https://images.unsplash.com/photo-1600181957967-7ff2f3fb4f19?w=600&q=80'
    },
    {
      id: 22, brand: 'puma',
      name: 'Puma RS-X',
      colorway: 'White / Team Royal / Red',
      price: 110,
      badge: 'hot',
      img: 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?w=600&q=80'
    },

    /* VANS */
    {
      id: 23, brand: 'vans',
      name: 'Vans Old Skool',
      colorway: 'Black / White',
      price: 65,
      badge: null,
      img: 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&q=80'
    },
    
    {
      id: 24, brand: 'vans',
      name: 'Vans Sk8-Hi',
      colorway: 'Classic White',
      price: 80,
      badge: 'new',
      img: 'https://images.unsplash.com/photo-1609259510516-5aea81aeeedc?w=600&q=80'
    }
  ];

  /* ── State ─── */
  let activeBrand = 'all';
  let cartCount = 0;

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
        <div class="card-img">
          ${badgeHtml}
          <img src="${p.img}" alt="${p.name}" loading="lazy" />
        </div>
        <div class="card-body">
          <p class="card-brand">${p.brand}</p>
          <p class="card-name">${p.name}</p>
          <p class="card-colorway">${p.colorway}</p>
          <div class="card-footer">
            <span class="card-price">$${p.price}</span>
            <button class="card-add" onclick="addToCart(${p.id})" title="Add to cart">
              <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
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

  /* ── Init ─── */
  filterProducts('all');
</script>
</body>
</html>
