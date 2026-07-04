<?php
include 'database.php'; // expects $conn to be a mysqli connection

// ── Handle form actions (add / update price / delete) ───
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add') {
            $brand    = mysqli_real_escape_string($conn, $_POST['brand']);
            $name     = mysqli_real_escape_string($conn, $_POST['name']);
            $colorway = mysqli_real_escape_string($conn, $_POST['colorway']);
            $price    = mysqli_real_escape_string($conn, $_POST['price']);

            $badgeInput = $_POST['badge'];
            if ($badgeInput == '') {
                $badge = 'NULL';
            } else {
                $badge = "'" . mysqli_real_escape_string($conn, $badgeInput) . "'";
            }

            // ── Handle uploaded image file ───
            $imgPath = '';
            $allowedExt = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            $maxSizeBytes = 5 * 1024 * 1024; // 5MB

            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] == 0) {
                $uploadDir = __DIR__ . '/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $originalName = $_FILES['img_file']['name'];
                $fileSize     = $_FILES['img_file']['size'];
                $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (in_array($ext, $allowedExt) && $fileSize <= $maxSizeBytes) {
                    $newFileName = uniqid('shoe_') . '.' . $ext;
                    $destination = $uploadDir . $newFileName;

                    if (move_uploaded_file($_FILES['img_file']['tmp_name'], $destination)) {
                        $imgPath = 'uploads/products/' . $newFileName;
                    }
                }
            }

            if ($imgPath == '') {
                header("Location: adminProduct.php?msg=uploaderror");
                exit;
            }

            $img = mysqli_real_escape_string($conn, $imgPath);

            $sql = "INSERT INTO products (brand, name, colorway, price, badge, img)
                    VALUES ('$brand', '$name', '$colorway', '$price', $badge, '$img')";

            if (mysqli_query($conn, $sql)) {
                header("Location: adminProduct.php?msg=added");
            } else {
                header("Location: adminProduct.php?msg=error");
            }
            exit;
        }

        else if ($action == 'update') {
            $id    = mysqli_real_escape_string($conn, $_POST['id']);
            $price = mysqli_real_escape_string($conn, $_POST['price']);

            $sql = "UPDATE products SET price = '$price' WHERE id = '$id'";

            if (mysqli_query($conn, $sql)) {
                header("Location: adminProduct.php?msg=updated");
            } else {
                header("Location: adminProduct.php?msg=error");
            }
            exit;
        }

        else if ($action == 'delete') {
            $id = mysqli_real_escape_string($conn, $_POST['id']);

            $sql = "DELETE FROM products WHERE id = '$id'";

            if (mysqli_query($conn, $sql)) {
                header("Location: adminProduct.php?msg=deleted");
            } else {
                header("Location: adminProduct.php?msg=error");
            }
            exit;
        }
    }
}

// ── Fetch all products for display ───
$products = array();
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY brand, id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

$flash = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SneakPeak — Admin</title>
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
      --green: #2ecc71;
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

    .nav-links { display: flex; align-items: center; gap: 36px; }

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
    .nav-links a:hover, .nav-links a.active { color: var(--white); }

    /* ── Page hero strip ─── */
    .page-hero { position: relative; padding: 56px 60px 40px; overflow: hidden; }
    .page-hero::after {
      content: '';
      position: absolute; bottom: 0; left: 0; right: 0;
      height: 1px; background: var(--border);
    }
    .hero-eyebrow {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .8rem; letter-spacing: .3em;
      color: var(--red); text-transform: uppercase; margin-bottom: 10px;
      opacity: 0; animation: fadeUp .5s .1s forwards;
    }
    .hero-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(48px, 7vw, 90px);
      line-height: .9; letter-spacing: .02em;
      opacity: 0; animation: fadeUp .6s .2s forwards;
    }
    .hero-title em { color: var(--red); font-style: normal; }
    .hero-sub {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem; color: var(--grey); letter-spacing: .08em;
      margin-top: 14px; text-transform: uppercase;
      opacity: 0; animation: fadeUp .5s .35s forwards;
    }
    .hero-accent {
      position: absolute; top: 0; right: 0;
      width: 260px; height: 3px; background: var(--red); opacity: .7;
    }

    /* ── Flash message ─── */
    .flash {
      margin: 0 60px;
      padding: 14px 22px;
      border-radius: 8px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      letter-spacing: .05em;
      text-transform: uppercase;
      font-size: .9rem;
      display: none;
    }
    .flash.show { display: block; }
    .flash.success { background: rgba(46,204,113,.12); border: 1px solid rgba(46,204,113,.35); color: var(--green); }
    .flash.error   { background: rgba(232,25,44,.12); border: 1px solid rgba(232,25,44,.35); color: var(--red); }

    /* ── Toolbar ─── */
    .toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 28px 60px 0;
    }
    .btn-primary {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .9rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--white);
      background: var(--red);
      border: none;
      padding: 12px 26px;
      border-radius: 50px;
      cursor: pointer;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 4px 16px rgba(232,25,44,.35);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(232,25,44,.5); }

    /* ── Add panel ─── */
    .add-panel {
      max-height: 0;
      overflow: hidden;
      margin: 0 60px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      transition: max-height .35s ease, margin .35s ease, padding .35s ease;
    }
    .add-panel.open {
      max-height: 700px;
      margin: 22px 60px 0;
      padding: 28px;
    }
    .add-panel h3 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.4rem;
      letter-spacing: .06em;
      margin-bottom: 20px;
    }
    .add-form { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 24px; }
    .form-row { display: flex; flex-direction: column; gap: 6px; }
    .form-row.full { grid-column: 1 / -1; }
    .form-row label {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--grey);
    }
    .form-row input, .form-row select {
      background: #101010;
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 11px 14px;
      color: var(--white);
      font-family: 'Barlow', sans-serif;
      font-size: .95rem;
    }
    .form-row input:focus, .form-row select:focus {
      outline: none;
      border-color: var(--red);
    }
    .form-actions {
      grid-column: 1 / -1;
      display: flex;
      gap: 14px;
      margin-top: 6px;
    }
    .btn-save-new, .btn-cancel {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .85rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 11px 24px;
      border-radius: 50px;
      cursor: pointer;
      border: none;
    }
    .btn-save-new { background: var(--red); color: var(--white); }
    .btn-cancel { background: transparent; color: var(--grey); border: 1px solid var(--border); }
    .btn-cancel:hover { color: var(--white); border-color: rgba(255,255,255,.25); }

    /* ── Brand filter bar ─── */
    .brand-bar {
      display: flex; align-items: center; gap: 10px;
      padding: 28px 60px; overflow-x: auto; scrollbar-width: none;
      border-bottom: 1px solid var(--border);
    }
    .brand-bar::-webkit-scrollbar { display: none; }
    .brand-btn {
      flex-shrink: 0;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .85rem; letter-spacing: .15em;
      text-transform: uppercase; color: var(--grey);
      background: transparent; border: 1px solid var(--border);
      padding: 9px 22px; border-radius: 50px; cursor: pointer;
      transition: color .2s, border-color .2s, background .2s;
      white-space: nowrap;
    }
    .brand-btn:hover { color: var(--white); border-color: rgba(255,255,255,.25); }
    .brand-btn.active { background: var(--red); color: var(--white); border-color: var(--red); }

    /* ── Section label ─── */
    .section-label { display: flex; align-items: center; gap: 18px; padding: 36px 60px 20px; }
    .section-label h2 { font-family: 'Bebas Neue', sans-serif; font-size: 1.5rem; letter-spacing: .08em; }
    .section-label .label-line { flex: 1; height: 1px; background: var(--border); }
    .section-label .label-count {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .85rem; letter-spacing: .1em; color: var(--grey); text-transform: uppercase;
    }

    /* ── Products grid ─── */
    .products-wrap { padding: 0 60px 80px; }
    .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 24px; }

    /* ── Product card ─── */
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      position: relative;
      opacity: 0;
      animation: cardIn .45s forwards;
    }
    .card-img {
      position: relative; background: #1c1c1c; height: 210px;
      display: flex; align-items: center; justify-content: center; overflow: hidden;
    }
    .card-img img { width: 100%; height: 100%; object-fit: cover; }

    .badge {
      position: absolute; top: 14px; left: 14px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .72rem; letter-spacing: .15em;
      text-transform: uppercase; padding: 4px 10px; border-radius: 4px; z-index: 2;
    }
    .badge-hot  { background: var(--red); color: var(--white); }
    .badge-new  { background: #1a6fff; color: var(--white); }
    .badge-sale { background: #e8a319; color: #111; }

    .card-id {
      position: absolute; top: 14px; right: 14px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .7rem; letter-spacing: .1em;
      background: rgba(0,0,0,.55); color: var(--grey);
      padding: 3px 9px; border-radius: 4px; z-index: 2;
    }

    .card-body { padding: 18px 20px 20px; }
    .card-brand {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .75rem; letter-spacing: .2em;
      color: var(--red); text-transform: uppercase; margin-bottom: 5px;
    }
    .card-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 900; font-size: 1.15rem; letter-spacing: .03em;
      line-height: 1.2; margin-bottom: 4px;
    }
    .card-colorway { font-size: .82rem; color: var(--grey); margin-bottom: 16px; }

    /* ── Admin controls ─── */
    .admin-controls { border-top: 1px solid var(--border); padding-top: 14px; }
    .price-form {
      display: flex; align-items: center; gap: 8px; margin-bottom: 10px;
    }
    .price-form .dollar {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.2rem; color: var(--grey);
    }
    .price-input {
      flex: 1;
      background: #101010;
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 8px 10px;
      color: var(--white);
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.1rem;
    }
    .price-input:focus { outline: none; border-color: var(--red); }
    .btn-update {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .78rem; letter-spacing: .1em;
      text-transform: uppercase; color: var(--white);
      background: #1a6fff; border: none; padding: 9px 16px;
      border-radius: 6px; cursor: pointer; white-space: nowrap;
    }
    .btn-update:hover { filter: brightness(1.1); }
    .delete-form { display: block; }
    .btn-delete {
      width: 100%;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .78rem; letter-spacing: .12em;
      text-transform: uppercase; color: var(--red);
      background: transparent; border: 1px solid rgba(232,25,44,.4);
      padding: 9px 16px; border-radius: 6px; cursor: pointer;
      transition: background .2s, color .2s;
    }
    .btn-delete:hover { background: var(--red); color: var(--white); }

    /* ── No results ─── */
    .no-results { grid-column: 1 / -1; text-align: center; padding: 80px 20px; display: none; }
    .no-results h3 { font-family: 'Bebas Neue', sans-serif; font-size: 2rem; color: var(--grey); letter-spacing: .1em; }
    .no-results p { color: var(--grey); margin-top: 8px; font-size: .9rem; }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes cardIn { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 768px) {
      nav, .page-hero, .brand-bar, .section-label, .products-wrap, .toolbar { padding-left: 20px; padding-right: 20px; }
      .add-panel, .flash { margin-left: 20px; margin-right: 20px; }
      .products-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; }
      .card-img { height: 150px; }
      .add-form { grid-template-columns: 1fr; }
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
    <a href="adminProduct.php" class="active">Admin</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
  </div>
</nav>

<!-- ── Page hero strip ─── -->
<div class="page-hero">
  <div class="hero-accent"></div>
  <p class="hero-eyebrow">Admin Dashboard</p>
  <h1 class="hero-title">Manage the <em>Catalog</em></h1>
  <p class="hero-sub">Add new drops, update pricing, or pull sneakers from the store</p>
</div>

<!-- ── Flash message ─── -->
<?php if ($flash == 'added') { ?>
  <div class="flash success show">Sneaker added successfully.</div>
<?php } else if ($flash == 'updated') { ?>
  <div class="flash success show">Price updated successfully.</div>
<?php } else if ($flash == 'deleted') { ?>
  <div class="flash success show">Sneaker deleted.</div>
<?php } else if ($flash == 'error') { ?>
  <div class="flash error show">Something went wrong. Please try again.</div>
<?php } else if ($flash == 'uploaderror') { ?>
  <div class="flash error show">Image upload failed. Use a JPG, PNG, GIF, or WEBP under 5MB.</div>
<?php } ?>

<!-- ── Toolbar ─── -->
<div class="toolbar">
  <span></span>
  <button class="btn-primary" onclick="toggleAddPanel()">+ Add New Sneaker</button>
</div>

<!-- ── Add product panel ─── -->
<div class="add-panel" id="add-panel">
  <h3>New Sneaker</h3>
  <form method="POST" action="adminProduct.php" class="add-form" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add">

    <div class="form-row">
      <label>Brand</label>
      <select name="brand" required>
        <option value="nike">Nike</option>
        <option value="adidas">Adidas</option>
        <option value="new balance">New Balance</option>
        <option value="under armour">Under Armour</option>
        <option value="converse">Converse</option>
        <option value="crocs">Crocs</option>
        <option value="puma">Puma</option>
        <option value="vans">Vans</option>
      </select>
    </div>

    <div class="form-row">
      <label>Price (Rs)</label>
      <input type="number" name="price" step="0.01" min="0" required>
    </div>

    <div class="form-row full">
      <label>Name</label>
      <input type="text" name="name" placeholder="e.g. Air Jordan 1 Retro High OG" required>
    </div>

    <div class="form-row full">
      <label>Colorway</label>
      <input type="text" name="colorway" placeholder="e.g. Chicago / Red & White">
    </div>

    <div class="form-row">
      <label>Badge</label>
      <select name="badge">
        <option value="">None</option>
        <option value="hot">Hot</option>
        <option value="new">New</option>
        <option value="sale">Sale</option>
      </select>
    </div>

    <div class="form-row">
      <label>Product Image</label>
      <input type="file" name="img_file" accept=".jpg,.jpeg,.png,.gif,.webp" required>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-save-new">Add Sneaker</button>
      <button type="button" class="btn-cancel" onclick="toggleAddPanel()">Cancel</button>
    </div>
  </form>
</div>

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
  <h2 id="section-title">All Inventory</h2>
  <div class="label-line"></div>
  <span class="label-count" id="count-label"><?php echo count($products); ?> styles</span>
</div>

<!-- ── Products grid ─── -->
<div class="products-wrap">
  <div class="products-grid" id="products-grid">
    <!-- Cards injected by JS -->
  </div>
</div>

<script>
  /* ── Product data (from database) ─── */
  const products = <?php echo json_encode($products); ?>;

  /* ── State ─── */
  let activeBrand = 'all';

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
          <p>Try a different brand filter, or add a new one.</p>
        </div>`;
      countLabel.textContent = '0 styles';
      return;
    }

    countLabel.textContent = `${list.length} style${list.length !== 1 ? 's' : ''}`;
    sectionTitle.textContent = activeBrand === 'all'
      ? 'All Inventory'
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
          <span class="card-id">#${p.id}</span>
          <img src="${p.img ? p.img : ''}" alt="${p.name}" loading="lazy" />
        </div>
        <div class="card-body">
          <p class="card-brand">${p.brand}</p>
          <p class="card-name">${p.name}</p>
          <p class="card-colorway">${p.colorway ? p.colorway : ''}</p>

          <div class="admin-controls">
            <form method="POST" action="adminProduct.php" class="price-form">
              <span class="rupees">Rs</span>
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="${p.id}">
              <input type="number" name="price" step="0.01" min="0" class="price-input" value="${parseFloat(p.price)}">
              <button type="submit" class="btn-update">Save</button>
            </form>
            <form method="POST" action="adminProduct.php" class="delete-form"
                  onsubmit="return confirm('Delete ${p.name.replace(/'/g, "\\'")}? This cannot be undone.');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="${p.id}">
              <button type="submit" class="btn-delete">Delete Sneaker</button>
            </form>
          </div>
        </div>`;

      grid.appendChild(card);
    });
  }

  /* ── Filter logic ─── */
  function filterProducts(brand) {
    activeBrand = brand;
    const filtered = brand === 'all' ? products : products.filter(p => p.brand === brand);
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

  /* ── Add panel toggle ─── */
  function toggleAddPanel() {
    document.getElementById('add-panel').classList.toggle('open');
  }

  /* ── Init ─── */
  filterProducts('all');
</script>
</body>
</html>