<?php
include 'database.php';
include 'cartHelper.php';

$product = null;
$relatedProducts = array();

$eu_sizes = [38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48];
$us_sizes = [6, 7, 8, 9, 10, 11, 12, 13, 14];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        $brand = mysqli_real_escape_string($conn, $product['brand']);
        $relatedResult = mysqli_query($conn, "SELECT * FROM products WHERE brand = '$brand' AND id != $id ORDER BY RAND() LIMIT 4");
        if ($relatedResult) {
            while ($row = mysqli_fetch_assoc($relatedResult)) {
                $relatedProducts[] = $row;
            }
        }
    }
}

if (!$product) {
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($product['name']) ?> — SneakPeak</title>
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

    /* ── Breadcrumb ─── */
    .breadcrumb {
      padding: 20px 60px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .85rem;
      letter-spacing: .1em;
      color: var(--grey);
      border-bottom: 1px solid var(--border);
      opacity: 0;
      animation: fadeUp .4s .1s forwards;
    }

    .breadcrumb a {
      color: var(--grey);
      text-decoration: none;
      transition: color .2s;
    }
    .breadcrumb a:hover { color: var(--red); }

    .breadcrumb span { color: var(--white); margin: 0 8px; }

    /* ── Product detail section ─── */
    .detail-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      padding: 60px;
      max-width: 1400px;
      margin: 0 auto;
      opacity: 0;
      animation: fadeUp .5s .2s forwards;
    }

    /* ── Image panel ─── */
    .detail-img-panel {
      position: relative;
      background: #f2f0f2;
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      aspect-ratio: 4/3;
      max-width: 460px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .detail-img-panel img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      transition: transform .5s;
      position: relative;
      z-index: 1;
      filter: drop-shadow(0 12px 18px rgba(0,0,0,.18));
    }

    .detail-img-panel:hover img {
      transform: scale(1.04);
    }

    .detail-badge {
      position: absolute;
      top: 20px; left: 20px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .8rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      padding: 6px 14px;
      border-radius: 6px;
      z-index: 2;
    }
    .detail-badge-hot   { background: var(--red); color: var(--white); }
    .detail-badge-new   { background: #1a6fff; color: var(--white); }
    .detail-badge-sale  { background: #e8a319; color: #111; }

    /* ── Info panel ─── */
    .detail-info {
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 24px;
    }

    .detail-brand {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .9rem;
      letter-spacing: .25em;
      color: var(--red);
      text-transform: uppercase;
      opacity: 0;
      animation: fadeUp .4s .3s forwards;
    }

    .detail-name {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(36px, 5vw, 56px);
      line-height: .95;
      letter-spacing: .02em;
      color: var(--white);
      opacity: 0;
      animation: fadeUp .4s .35s forwards;
    }

    .detail-colorway {
      font-size: 1rem;
      color: var(--grey);
      opacity: 0;
      animation: fadeUp .4s .4s forwards;
    }

    .detail-price {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2.2rem;
      letter-spacing: .04em;
      color: var(--white);
      opacity: 0;
      animation: fadeUp .4s .45s forwards;
    }

    .detail-divider {
      width: 60px;
      height: 2px;
      background: var(--red);
      opacity: 0;
      animation: fadeUp .4s .5s forwards;
    }

    /* ── Size selector ─── */
    .size-select {
      opacity: 0;
      animation: fadeUp .4s .52s forwards;
    }

    .size-group {
      margin-bottom: 14px;
    }
    .size-group:last-child {
      margin-bottom: 0;
    }
    .size-group-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .7rem;
      letter-spacing: .2em;
      color: var(--grey);
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .size-select-label {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .8rem;
      letter-spacing: .2em;
      color: var(--grey);
      text-transform: uppercase;
      margin-bottom: 12px;
    }

    .size-select-label .size-warning {
      color: var(--red);
      font-size: .75rem;
      letter-spacing: .05em;
      text-transform: none;
      opacity: 0;
      transition: opacity .2s;
    }
    .size-select-label .size-warning.show { opacity: 1; }

    .size-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .size-option {
      min-width: 56px;
      padding: 10px 14px;
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--white);
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .9rem;
      text-align: center;
      cursor: pointer;
      transition: border-color .2s, background .2s, color .2s;
    }

    .size-option:hover {
      border-color: rgba(255,255,255,.3);
    }

    .size-option.selected {
      background: var(--red);
      border-color: var(--red);
      color: var(--white);
    }

    .detail-specs {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      opacity: 0;
      animation: fadeUp .4s .55s forwards;
    }

    .spec-item {
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 16px;
    }

    .spec-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .7rem;
      letter-spacing: .2em;
      color: var(--grey);
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .spec-value {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      color: var(--white);
      text-transform: capitalize;
    }

    /* ── Actions ─── */
    /* ── Size Guide ─── */
    .size-guide-toggle {
      background: none;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px 20px;
      color: var(--grey);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .85rem;
      font-weight: 700;
      letter-spacing: .15em;
      text-transform: uppercase;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      transition: color .2s, border-color .2s;
      opacity: 0;
      animation: fadeUp .4s .58s forwards;
    }
    .size-guide-toggle:hover {
      color: var(--white);
      border-color: rgba(255,255,255,.2);
    }
    .size-guide-toggle svg {
      width: 18px; height: 18px;
      fill: none;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      transition: transform .3s;
    }
    .size-guide-toggle.open svg {
      transform: rotate(180deg);
    }

    .size-guide-panel {
      max-height: 0;
      overflow: hidden;
      transition: max-height .4s ease, opacity .3s ease, margin .3s ease;
      opacity: 0;
      margin-top: 0;
    }
    .size-guide-panel.open {
      max-height: 500px;
      opacity: 1;
      margin-top: 12px;
    }

    .size-guide-table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
      border-radius: 10px;
      overflow: hidden;
    }
    .size-guide-table th {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .7rem;
      letter-spacing: .15em;
      color: var(--grey);
      text-transform: uppercase;
      padding: 10px 12px;
      background: rgba(255,255,255,.04);
      border-bottom: 1px solid var(--border);
      text-align: left;
    }
    .size-guide-table td {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .9rem;
      color: var(--white);
      padding: 8px 12px;
      border-bottom: 1px solid var(--border);
    }
    .size-guide-table tr:last-child td {
      border-bottom: none;
    }
    .size-guide-table td.select-size {
      cursor: pointer;
      transition: background .15s, color .15s;
    }
    .size-guide-table td.select-size:hover {
      background: rgba(232,25,44,.2);
      color: var(--red);
    }

    .size-guide-tabs {
      display: flex;
      gap: 4px;
      margin-bottom: 12px;
    }
    .size-guide-tab {
      padding: 6px 16px;
      border-radius: 6px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .75rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      cursor: pointer;
      border: 1px solid var(--border);
      background: transparent;
      color: var(--grey);
      transition: all .2s;
    }
    .size-guide-tab.active {
      background: var(--red);
      color: var(--white);
      border-color: var(--red);
    }
    .size-guide-tab:hover:not(.active) {
      color: var(--white);
      border-color: rgba(255,255,255,.2);
    }

    .detail-actions {
      display: flex;
      gap: 16px;
      margin-top: 8px;
      opacity: 0;
      animation: fadeUp .4s .6s forwards;
    }

    .btn-add-cart {
      flex: 1;
      padding: 16px 32px;
      background: var(--red);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 6px 24px rgba(232,25,44,.4);
    }

    .btn-add-cart:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 32px rgba(232,25,44,.55);
    }

    .btn-add-cart svg {
      width: 20px; height: 20px;
      fill: none;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .btn-back {
      padding: 16px 28px;
      background: transparent;
      color: var(--grey);
      border: 1px solid var(--border);
      border-radius: 12px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      cursor: pointer;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: color .2s, border-color .2s;
    }

    .btn-back:hover {
      color: var(--white);
      border-color: rgba(255,255,255,.25);
    }

    .btn-back svg {
      width: 18px; height: 18px;
      fill: none;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    /* ── Related products ─── */
    .related-section {
      padding: 0 60px 80px;
      max-width: 1400px;
      margin: 0 auto;
      opacity: 0;
      animation: fadeUp .5s .7s forwards;
    }

    .related-header {
      display: flex;
      align-items: center;
      gap: 18px;
      margin-bottom: 32px;
    }

    .related-header h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.5rem;
      letter-spacing: .08em;
      color: var(--white);
    }

    .related-header .label-line {
      flex: 1;
      height: 1px;
      background: var(--border);
    }

    .related-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }

    .related-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      cursor: pointer;
      transition: transform .25s, border-color .25s, box-shadow .25s;
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .related-card:hover {
      transform: translateY(-4px);
      border-color: rgba(232,25,44,.3);
      box-shadow: 0 16px 40px rgba(0,0,0,.4);
    }

    .related-card-img {
      position: relative;
      background: #f2f0f2;
      height: 180px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      padding: 10px;
    }

    .related-card-img img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      transition: transform .4s;
    }

    .related-card:hover .related-card-img img { transform: scale(1.05); }

    .related-card-body {
      padding: 14px 16px 16px;
    }

    .related-card-brand {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .7rem;
      letter-spacing: .2em;
      color: var(--red);
      text-transform: uppercase;
      margin-bottom: 4px;
    }

    .related-card-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 900;
      font-size: 1rem;
      color: var(--white);
      line-height: 1.2;
      margin-bottom: 4px;
    }

    .related-card-price {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.2rem;
      letter-spacing: .04em;
      color: var(--white);
    }

    /* ── Flash message ─── */
    .flash {
      position: fixed;
      bottom: 30px;
      right: 30px;
      padding: 16px 28px;
      border-radius: 10px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .95rem;
      letter-spacing: .1em;
      z-index: 1000;
      transform: translateY(100px);
      opacity: 0;
      transition: transform .35s, opacity .35s;
    }
    .flash.show {
      transform: translateY(0);
      opacity: 1;
    }
    .flash.success {
      background: #1a6fff;
      color: var(--white);
    }

    /* ── Keyframes ─── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Responsive ─── */
    @media (max-width: 900px) {
      nav, .breadcrumb, .detail-section, .related-section {
        padding-left: 24px;
        padding-right: 24px;
      }
      .detail-section {
        grid-template-columns: 1fr;
        gap: 40px;
        padding-top: 30px;
      }
      .detail-img-panel { aspect-ratio: 4/3; max-width: 100%; }
      .detail-specs { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
      .detail-actions { flex-direction: column; }
      .related-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; }
      .related-card-img { height: 140px; }
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
    <a href="products.php">Shop</a>
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
      <span class="cart-badge"><?= cart_count() ?></span>
    </a>
  </div>
</nav>

<!-- ── Breadcrumb ─── -->
<div class="breadcrumb">
  <a href="products.php">Shop</a>
  <span>/</span>
  <?= htmlspecialchars($product['brand']) ?>
  <span>/</span>
  <?= htmlspecialchars($product['name']) ?>
</div>

<!-- ── Product detail ─── -->
<div class="detail-section">
  <div class="detail-img-panel">
    <?php if ($product['badge']): ?>
      <span class="detail-badge detail-badge-<?= htmlspecialchars($product['badge']) ?>">
        <?= $product['badge'] === 'hot' ? '🔥 Hot' : ($product['badge'] === 'new' ? '✦ New' : '% Sale') ?>
      </span>
    <?php endif; ?>
    <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
  </div>

  <div class="detail-info">
    <p class="detail-brand"><?= htmlspecialchars($product['brand']) ?></p>
    <h1 class="detail-name"><?= htmlspecialchars($product['name']) ?></h1>
    <p class="detail-colorway"><?= htmlspecialchars($product['colorway']) ?></p>

    <div class="detail-divider"></div>

    <p class="detail-price">Rs.<?= number_format($product['price'], 0) ?></p>

    <div class="size-select">
      <div class="size-select-label">
        <span>Select Size</span>
        <span class="size-warning" id="size-warning">Please select a size</span>
      </div>
      <div class="size-group">
        <div class="size-group-label">EU</div>
        <div class="size-grid" id="size-grid-eu">
          <?php foreach ($eu_sizes as $s): ?>
            <div class="size-option" data-size="EU <?= $s ?>"><?= $s ?></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="size-group">
        <div class="size-group-label">US</div>
        <div class="size-grid" id="size-grid-us">
          <?php foreach ($us_sizes as $s): ?>
            <div class="size-option" data-size="US <?= $s ?>"><?= $s ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="detail-specs">
      <div class="spec-item">
        <p class="spec-label">Brand</p>
        <p class="spec-value"><?= htmlspecialchars($product['brand']) ?></p>
      </div>
      <div class="spec-item">
        <p class="spec-label">Colorway</p>
        <p class="spec-value"><?= htmlspecialchars($product['colorway']) ?></p>
      </div>
      <div class="spec-item">
        <p class="spec-label">Price</p>
        <p class="spec-value">Rs.<?= number_format($product['price'], 0) ?></p>
      </div>
      <div class="spec-item">
        <p class="spec-label">Status</p>
        <p class="spec-value"><?= $product['badge'] ? ucfirst(htmlspecialchars($product['badge'])) : 'Available' ?></p>
      </div>
    </div>

    <!-- ── Size Guide ─── -->
    <button class="size-guide-toggle" onclick="toggleSizeGuide()">
      <span>Size Guide (EU / US)</span>
      <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div class="size-guide-panel" id="size-guide-panel">
      <div class="size-guide-tabs">
        <button class="size-guide-tab active" data-gender="men" onclick="switchSizeTab('men', this)">Men</button>
        <button class="size-guide-tab" data-gender="women" onclick="switchSizeTab('women', this)">Women</button>
      </div>
      <table class="size-guide-table" id="size-table-men">
        <thead>
          <tr><th>EU</th><th>US</th><th>CM</th></tr>
        </thead>
        <tbody>
          <tr><td class="select-size" data-size="EU 38">38</td><td class="select-size" data-size="US 6">6</td><td>24</td></tr>
          <tr><td class="select-size" data-size="EU 38.5">38.5</td><td class="select-size" data-size="US 6.5">6.5</td><td>24.5</td></tr>
          <tr><td class="select-size" data-size="EU 39">39</td><td class="select-size" data-size="US 7">7</td><td>25</td></tr>
          <tr><td class="select-size" data-size="EU 40">40</td><td class="select-size" data-size="US 7.5">7.5</td><td>25.5</td></tr>
          <tr><td class="select-size" data-size="EU 40.5">40.5</td><td class="select-size" data-size="US 8">8</td><td>26</td></tr>
          <tr><td class="select-size" data-size="EU 41">41</td><td class="select-size" data-size="US 8.5">8.5</td><td>26.5</td></tr>
          <tr><td class="select-size" data-size="EU 42">42</td><td class="select-size" data-size="US 9">9</td><td>27</td></tr>
          <tr><td class="select-size" data-size="EU 42.5">42.5</td><td class="select-size" data-size="US 9.5">9.5</td><td>27.5</td></tr>
          <tr><td class="select-size" data-size="EU 43">43</td><td class="select-size" data-size="US 10">10</td><td>28</td></tr>
          <tr><td class="select-size" data-size="EU 44">44</td><td class="select-size" data-size="US 10.5">10.5</td><td>28.5</td></tr>
          <tr><td class="select-size" data-size="EU 44.5">44.5</td><td class="select-size" data-size="US 11">11</td><td>29</td></tr>
          <tr><td class="select-size" data-size="EU 45">45</td><td class="select-size" data-size="US 11.5">11.5</td><td>29.5</td></tr>
          <tr><td class="select-size" data-size="EU 46">46</td><td class="select-size" data-size="US 12">12</td><td>30</td></tr>
          <tr><td class="select-size" data-size="EU 47">47</td><td class="select-size" data-size="US 13">13</td><td>31</td></tr>
          <tr><td class="select-size" data-size="EU 48">48</td><td class="select-size" data-size="US 14">14</td><td>32</td></tr>
        </tbody>
      </table>
      <table class="size-guide-table" id="size-table-women" style="display:none">
        <thead>
          <tr><th>EU</th><th>US</th><th>CM</th></tr>
        </thead>
        <tbody>
          <tr><td class="select-size" data-size="EU 35">35</td><td class="select-size" data-size="US 5">5</td><td>22</td></tr>
          <tr><td class="select-size" data-size="EU 35.5">35.5</td><td class="select-size" data-size="US 5.5">5.5</td><td>22.5</td></tr>
          <tr><td class="select-size" data-size="EU 36">36</td><td class="select-size" data-size="US 6">6</td><td>23</td></tr>
          <tr><td class="select-size" data-size="EU 37">37</td><td class="select-size" data-size="US 6.5">6.5</td><td>23.5</td></tr>
          <tr><td class="select-size" data-size="EU 37.5">37.5</td><td class="select-size" data-size="US 7">7</td><td>24</td></tr>
          <tr><td class="select-size" data-size="EU 38">38</td><td class="select-size" data-size="US 7.5">7.5</td><td>24.5</td></tr>
          <tr><td class="select-size" data-size="EU 38.5">38.5</td><td class="select-size" data-size="US 8">8</td><td>25</td></tr>
          <tr><td class="select-size" data-size="EU 39">39</td><td class="select-size" data-size="US 8.5">8.5</td><td>25.5</td></tr>
          <tr><td class="select-size" data-size="EU 40">40</td><td class="select-size" data-size="US 9">9</td><td>26</td></tr>
          <tr><td class="select-size" data-size="EU 40.5">40.5</td><td class="select-size" data-size="US 9.5">9.5</td><td>26.5</td></tr>
          <tr><td class="select-size" data-size="EU 41">41</td><td class="select-size" data-size="US 10">10</td><td>27</td></tr>
          <tr><td class="select-size" data-size="EU 42">42</td><td class="select-size" data-size="US 11">11</td><td>28</td></tr>
        </tbody>
      </table>
    </div>

    <div class="detail-actions">
      <button class="btn-add-cart" id="add-to-cart-btn" data-id="<?= $product['id'] ?>">
        <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        Add to Cart
      </button>
      <a href="products.php" class="btn-back">
        <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back
      </a>
    </div>
  </div>
</div>

<!-- ── Related products ─── -->
<?php if (!empty($relatedProducts)): ?>
<div class="related-section">
  <div class="related-header">
    <h2>You May Also Like</h2>
    <div class="label-line"></div>
  </div>
  <div class="related-grid">
    <?php foreach ($relatedProducts as $rp): ?>
      <a href="product-detail.php?id=<?= $rp['id'] ?>" class="related-card">
        <div class="related-card-img">
          <img src="<?= htmlspecialchars($rp['img']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" loading="lazy" />
        </div>
        <div class="related-card-body">
          <p class="related-card-brand"><?= htmlspecialchars($rp['brand']) ?></p>
          <p class="related-card-name"><?= htmlspecialchars($rp['name']) ?></p>
          <p class="related-card-price">Rs.<?= number_format($rp['price'], 0) ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ── Flash message ─── -->
<div class="flash success" id="flash-msg">Added to cart!</div>

<script>
  let selectedSize = null;

  document.querySelectorAll('.size-option').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.size-option').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      selectedSize = opt.dataset.size;
      document.getElementById('size-warning').classList.remove('show');
    });
  });

  document.getElementById('add-to-cart-btn').addEventListener('click', () => {
    if (!selectedSize) {
      document.getElementById('size-warning').classList.add('show');
      document.querySelector('.size-group').scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    const id = document.getElementById('add-to-cart-btn').dataset.id;
    const formData = new URLSearchParams();
    formData.set('id', id);
    formData.set('size', selectedSize);
    formData.set('qty', 1);

    fetch('addToCart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert(data.message || 'Could not add to cart.');
          return;
        }

        const badge = document.querySelector('.cart-badge');
        badge.textContent = data.cartCount;
        badge.style.transform = 'scale(1.5)';
        badge.style.transition = 'transform .15s';
        setTimeout(() => { badge.style.transform = 'scale(1)'; }, 150);

        const flash = document.getElementById('flash-msg');
        flash.classList.add('show');
        setTimeout(() => { flash.classList.remove('show'); }, 2500);
      })
      .catch(() => alert('Something went wrong adding this to your cart.'));
  });

  function toggleSizeGuide() {
    const panel = document.getElementById('size-guide-panel');
    const toggle = document.querySelector('.size-guide-toggle');
    panel.classList.toggle('open');
    toggle.classList.toggle('open');
  }

  function switchSizeTab(gender, btn) {
    document.querySelectorAll('.size-guide-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('size-table-men').style.display = gender === 'men' ? '' : 'none';
    document.getElementById('size-table-women').style.display = gender === 'women' ? '' : 'none';
  }

  document.querySelectorAll('.size-guide-table td.select-size').forEach(cell => {
    cell.addEventListener('click', () => {
      const size = cell.dataset.size;
      const option = document.querySelector(`.size-option[data-size="${size}"]`);
      if (!option) {
        alert(`Size ${size} is not available for this product.`);
        return;
      }
      option.click();
      document.getElementById('add-to-cart-btn').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });
</script>

</body>
</html>