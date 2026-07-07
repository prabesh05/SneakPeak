<?php
session_start();
include 'database.php'; // expects $conn to be a mysqli connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$results = [];

if ($query !== '') {
    // Matches the `products` table columns used in shop.php: id, brand, name, colorway, price, img, badge
    $stmt = $conn->prepare("SELECT id, brand, name, colorway, price, img FROM products WHERE name LIKE ? OR brand LIKE ? ORDER BY brand, name ASC");
    $like = "%" . $query . "%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Results — SneakPeak</title>
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
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 28px 60px;
      border-bottom: 1px solid rgba(245,245,245,.08);
    }

    .brand {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.6rem;
      letter-spacing: .05em;
      text-decoration: none;
    }
    .brand .sneak { color: var(--red); }
    .brand .peak  { color: var(--white); }

    .nav-links a {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .95rem;
      font-weight: 700;
      letter-spacing: .12em;
      color: var(--white);
      text-decoration: none;
      text-transform: uppercase;
      margin-left: 32px;
      transition: color .2s;
    }
    .nav-links a:hover { color: var(--red); }

    /* ── User Dropdown ─── */
    .user-dropdown { position: relative; display: inline-block; margin-left: 32px; }
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

    main {
      max-width: 1000px;
      margin: 0 auto;
      padding: 60px 24px 100px;
    }

    .search-heading {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 900;
      font-size: clamp(1.6rem, 3vw, 2.4rem);
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    .search-heading span { color: var(--red); }

    .search-sub {
      color: var(--grey);
      margin-bottom: 40px;
      font-size: .95rem;
    }

    .results-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 24px;
    }

    .result-card {
      display: block;
      background: linear-gradient(155deg, #1c1c1c 0%, #0d0d0d 70%);
      border: 1px solid rgba(245,245,245,.08);
      border-radius: 14px;
      overflow: hidden;
      transition: transform .2s, box-shadow .2s;
      text-decoration: none;
      color: inherit;
    }
    .result-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(0,0,0,.4);
    }

    .result-card img {
      width: 100%;
      aspect-ratio: 4/3;
      object-fit: cover;
      display: block;
    }

    .result-card-body {
      padding: 16px 18px;
    }

    .result-card-body h3 {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 1.05rem;
      margin-bottom: 6px;
    }

    .result-card-body .brand-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .72rem;
      letter-spacing: .15em;
      color: var(--red);
      text-transform: uppercase;
      margin-bottom: 4px;
    }

    .result-card-body .colorway-label {
      font-size: .8rem;
      color: var(--grey);
      margin-bottom: 10px;
    }

    .result-card-body .price {
      color: var(--red);
      font-weight: 700;
    }

    .no-results {
      color: var(--grey);
      font-size: 1.05rem;
      padding: 40px 0;
    }

    .back-link {
      display: inline-block;
      margin-top: 40px;
      color: var(--white);
      text-decoration: none;
      font-family: 'Barlow Condensed', sans-serif;
      letter-spacing: .1em;
      text-transform: uppercase;
      border-bottom: 1px solid var(--red);
      padding-bottom: 2px;
    }
  </style>
</head>
<body>

<nav>
  <a href="products.php" class="brand"><span class="sneak">Sneak</span><span class="peak">Peak</span></a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="products.php">Shop</a>
    <a href="#">About</a>
    <a href="#">Contact</a>
    <div class="user-dropdown">
      <span class="user-dropdown-toggle" id="userDropdownToggle"><?= strtoupper($_SESSION['email'][0]) ?></span>
      <div class="user-dropdown-menu" id="userDropdownMenu">
        <span class="user-dropdown-email"><?= htmlspecialchars($_SESSION['email']) ?></span>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</nav>

<main>
  <?php if ($query === ''): ?>
    <h1 class="search-heading">Type Something to <span>Search</span></h1>
    <p class="search-sub">Use the search bar in the navigation to find sneakers and apparel.</p>
  <?php else: ?>
    <h1 class="search-heading">Results for "<span><?= htmlspecialchars($query) ?></span>"</h1>
    <p class="search-sub"><?= count($results) ?> item(s) found</p>

    <?php if (count($results) > 0): ?>
      <div class="results-grid">
        <?php foreach ($results as $item): ?>
          <a href="product-detail.php?id=<?= $item['id'] ?>" class="result-card">
            <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <div class="result-card-body">
              <p class="brand-label"><?= htmlspecialchars($item['brand']) ?></p>
              <h3><?= htmlspecialchars($item['name']) ?></h3>
              <p class="colorway-label"><?= htmlspecialchars($item['colorway']) ?></p>
              <span class="price">Rs.<?= number_format((float)$item['price']) ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="no-results">No sneakers or apparel matched your search. Try a different keyword.</p>
    <?php endif; ?>
  <?php endif; ?>

  <a href="products.php" class="back-link">← Go Back </a>
</main>

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