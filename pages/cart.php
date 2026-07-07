<?php
session_start();
include 'database.php';
include 'cartHelper.php';

$errors = [];
$shippingFee = 150; // flat fee, adjust as needed

// ── Handle POST actions ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_cart') {
        if (!empty($_POST['qty']) && is_array($_POST['qty'])) {
            foreach ($_POST['qty'] as $key => $qty) {
                cart_set_qty($key, intval($qty));
            }
        }
        header('Location: cart.php?view=cart');
        exit;
    }

    if ($action === 'remove') {
        $key = $_POST['key'] ?? '';
        if ($key !== '') cart_remove($key);
        header('Location: cart.php?view=cart');
        exit;
    }

    if ($action === 'place_order') {
        $name    = trim($_POST['full_name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $payment = $_POST['payment_method'] ?? '';

        if ($name === '') $errors[] = 'Full name is required.';
        if (!preg_match('/^[0-9+\-\s]{7,15}$/', $phone)) $errors[] = 'Enter a valid phone number.';
        if ($address === '') $errors[] = 'Delivery address is required.';
        if (!in_array($payment, ['esewa', 'mobile_banking', 'cod'], true)) $errors[] = 'Please select a payment method.';
        if (cart_is_empty()) $errors[] = 'Your cart is empty.';

        if (empty($errors)) {
            $orderItems  = cart_items_with_products($conn);
            $orderSub    = cart_subtotal($conn);
            $orderId     = 'SP-' . strtoupper(substr(md5(uniqid('', true)), 0, 7));
            $userId      = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
            $createdAt   = date('Y-m-d H:i:s');
            $status      = 'pending';

            $stmt = $conn->prepare("INSERT INTO cart (
                order_id, user_id, product_id, product_name, size, quantity,
                unit_price, line_total, customer_name, phone, address,
                payment_method, subtotal, shipping_fee, total_amount, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                $errors[] = 'Unable to save your order right now.';
            } else {
                $saved = true;
                foreach ($orderItems as $item) {
                    $productId     = intval($item['id']);
                    $productName   = $item['name'];
                    $itemSize      = $item['size'];
                    $itemQty       = intval($item['qty']);
                    $unitPrice     = floatval($item['price']);
                    $lineTotal     = floatval($item['lineTotal']);
                    $customerName  = $name;
                    $customerPhone = $phone;
                    $customerAddr  = $address;
                    $paymentMethod = $payment;
                    $subtotalValue = floatval($orderSub);
                    $shippingValue = floatval($shippingFee);
                    $totalValue    = floatval($orderSub + $shippingFee);
                    $orderStatus   = $status;
                    $createdAtValue = $createdAt;

                    if (!$stmt->bind_param(
                        'siissiddssssdddss',
                        $orderId,
                        $userId,
                        $productId,
                        $productName,
                        $itemSize,
                        $itemQty,
                        $unitPrice,
                        $lineTotal,
                        $customerName,
                        $customerPhone,
                        $customerAddr,
                        $paymentMethod,
                        $subtotalValue,
                        $shippingValue,
                        $totalValue,
                        $orderStatus,
                        $createdAtValue
                    )) {
                        $saved = false;
                        break;
                    }

                    if (!$stmt->execute()) {
                        $saved = false;
                        break;
                    }

                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO cart (
                        order_id, user_id, product_id, product_name, size, quantity,
                        unit_price, line_total, customer_name, phone, address,
                        payment_method, subtotal, shipping_fee, total_amount, status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                }

                if (isset($stmt) && is_object($stmt)) {
                    $stmt->close();
                }

                if (!$saved) {
                    $errors[] = 'Unable to save your order right now.';
                }
            }

            if (empty($errors)) {
                $_SESSION['last_order'] = [
                    'order_id'  => $orderId,
                    'name'      => $name,
                    'phone'     => $phone,
                    'address'   => $address,
                    'payment'   => $payment,
                    'items'     => $orderItems,
                    'subtotal'  => $orderSub,
                    'shipping'  => $shippingFee,
                    'total'     => $orderSub + $shippingFee,
                    'placed_at' => date('D, d M Y — h:i A'),
                ];

                cart_clear();
                header('Location: cart.php?view=confirmation');
                exit;
            }
        }
    }
}

// ── Determine current view ─────────────────────────────────
$view = $_GET['view'] ?? 'cart';
if (!in_array($view, ['cart', 'checkout', 'confirmation'], true)) $view = 'cart';

// if a failed place_order kept us here via POST, force checkout view so errors show
if (!empty($errors)) $view = 'checkout';

if ($view === 'confirmation' && !isset($_SESSION['last_order'])) {
    header('Location: cart.php?view=cart');
    exit;
}

if ($view === 'checkout' && cart_is_empty() && empty($errors)) {
    header('Location: cart.php?view=cart');
    exit;
}

$cartItems = cart_items_with_products($conn);
$subtotal  = cart_subtotal($conn);
$shipping  = cart_is_empty() ? 0 : $shippingFee;
$total     = $subtotal + $shipping;

$order = $_SESSION['last_order'] ?? null;

$paymentLabels = [
    'esewa'          => 'eSewa',
    'mobile_banking' => 'Mobile Banking',
    'cod'            => 'Cash on Delivery',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Cart — SneakPeak</title>
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
      --green: #1DB954;
      --blue:  #1a6fff;
      --amber: #e8a319;
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--black);
      color: var(--white);
      font-family: 'Barlow', sans-serif;
      min-height: 100vh;
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

    .nav-user {
      font-family: 'Barlow', sans-serif;
      font-size: .85rem;
      color: var(--grey);
      white-space: nowrap;
    }
    .nav-logout, .nav-login {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .85rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--grey);
      text-decoration: none;
      transition: color .2s;
    }
    .nav-logout:hover { color: var(--red); }
    .nav-login:hover { color: var(--white); }

    .nav-cart { position: relative; color: var(--white); }
    .nav-cart svg {
      width: 22px; height: 22px;
      fill: none; stroke: currentColor;
      stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
    }
    .cart-badge {
      position: absolute; top: -6px; right: -8px;
      background: var(--red); color: var(--white);
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .65rem;
      width: 16px; height: 16px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
    }

    /* ── Page header / steps ─── */
    .page-header {
      padding: 40px 60px 0;
      max-width: 1300px;
      margin: 0 auto;
    }

    .page-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(30px, 4vw, 44px);
      letter-spacing: .03em;
    }

    .step-track {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 22px 0 10px;
      flex-wrap: wrap;
    }

    .step {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .8rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--grey);
      padding: 6px 14px;
      border: 1px solid var(--border);
      border-radius: 50px;
    }
    .step.active { color: var(--white); border-color: var(--red); background: rgba(232,25,44,.1); }
    .step.done { color: var(--white); }
    .step-arrow { color: var(--border); font-size: .8rem; }

    /* ── Layout ─── */
    .cart-wrap {
      padding: 30px 60px 80px;
      max-width: 1300px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 40px;
      align-items: start;
    }

    /* ── Empty state ─── */
    .empty-cart {
      grid-column: 1 / -1;
      text-align: center;
      padding: 100px 20px;
      border: 1px dashed var(--border);
      border-radius: 16px;
    }
    .empty-cart svg {
      width: 64px; height: 64px;
      stroke: var(--grey); fill: none;
      stroke-width: 1.4; margin-bottom: 20px;
    }
    .empty-cart h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.8rem;
      margin-bottom: 10px;
    }
    .empty-cart p { color: var(--grey); margin-bottom: 24px; }
    .empty-cart a {
      display: inline-block;
      padding: 14px 32px;
      background: var(--red);
      color: var(--white);
      text-decoration: none;
      border-radius: 10px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
    }

    /* ── Cart items ─── */
    .cart-items { display: flex; flex-direction: column; gap: 14px; }

    .cart-item {
      display: grid;
      grid-template-columns: 100px 1fr auto;
      gap: 20px;
      align-items: center;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 16px;
    }

    .cart-item-img {
      width: 100px; height: 100px;
      background: #f2f0f2;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
      padding: 8px;
    }
    .cart-item-img img { width: 100%; height: 100%; object-fit: contain; }

    .cart-item-info { min-width: 0; }
    .cart-item-brand {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700; font-size: .68rem;
      letter-spacing: .18em; color: var(--red);
      text-transform: uppercase; margin-bottom: 4px;
    }
    .cart-item-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 900; font-size: 1.05rem;
      color: var(--white); margin-bottom: 4px;
    }
    .cart-item-size {
      font-size: .85rem; color: var(--grey); margin-bottom: 10px;
    }

    .qty-stepper {
      display: inline-flex;
      align-items: center;
      border: 1px solid var(--border);
      border-radius: 8px;
      overflow: hidden;
      width: fit-content;
    }
    .qty-stepper button {
      width: 30px; height: 30px;
      background: rgba(255,255,255,.03);
      border: none;
      color: var(--white);
      font-size: 1.1rem;
      cursor: pointer;
    }
    .qty-stepper button:hover { background: rgba(232,25,44,.15); color: var(--red); }
    .qty-stepper input {
      width: 42px; height: 30px;
      background: transparent;
      border: none;
      border-left: 1px solid var(--border);
      border-right: 1px solid var(--border);
      text-align: center;
      color: var(--white);
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .95rem;
      -moz-appearance: textfield;
    }
    .qty-stepper input::-webkit-outer-spin-button,
    .qty-stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .cart-item-right {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 14px;
      height: 100%;
      justify-content: space-between;
    }
    .cart-item-price {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.3rem;
      letter-spacing: .03em;
    }
    .remove-btn {
      background: none;
      border: none;
      color: var(--grey);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .78rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      transition: color .2s;
    }
    .remove-btn:hover { color: var(--red); }
    .remove-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }

    .cart-update-row {
      display: flex;
      justify-content: flex-end;
      margin-top: 8px;
    }
    .btn-update {
      padding: 12px 24px;
      background: transparent;
      border: 1px solid var(--border);
      color: var(--white);
      border-radius: 10px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .85rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      cursor: pointer;
      transition: border-color .2s, color .2s;
    }
    .btn-update:hover { border-color: var(--white); }

    /* ── Summary card ─── */
    .summary-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 28px;
      position: sticky;
      top: 100px;
    }
    .summary-card h3 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.4rem;
      letter-spacing: .04em;
      margin-bottom: 20px;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      font-size: .95rem;
      color: var(--grey);
      margin-bottom: 14px;
    }
    .summary-row.total {
      color: var(--white);
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.3rem;
      letter-spacing: .03em;
      padding-top: 14px;
      border-top: 1px solid var(--border);
      margin-top: 6px;
    }
    .btn-checkout, .btn-place-order {
      display: block;
      width: 100%;
      padding: 16px;
      margin-top: 20px;
      background: var(--red);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      text-align: center;
      text-decoration: none;
      cursor: pointer;
      box-shadow: 0 6px 24px rgba(232,25,44,.35);
      transition: transform .2s, box-shadow .2s;
    }
    .btn-checkout:hover, .btn-place-order:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 32px rgba(232,25,44,.5);
    }
    .continue-shopping {
      display: block;
      text-align: center;
      margin-top: 14px;
      color: var(--grey);
      font-size: .85rem;
      text-decoration: none;
      letter-spacing: .05em;
    }
    .continue-shopping:hover { color: var(--white); }

    /* ── Checkout view ─── */
    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 40px;
      align-items: start;
      grid-column: 1 / -1;
    }

    .checkout-form-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 32px;
    }
    .checkout-form-card h3 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.3rem;
      letter-spacing: .03em;
      margin-bottom: 20px;
    }

    .form-errors {
      background: rgba(232,25,44,.1);
      border: 1px solid rgba(232,25,44,.4);
      color: #ff8f97;
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 20px;
      font-size: .9rem;
    }
    .form-errors ul { padding-left: 18px; }

    .form-group { margin-bottom: 18px; }
    .form-group label {
      display: block;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .75rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      color: var(--grey);
      margin-bottom: 8px;
    }
    .form-group input, .form-group textarea {
      width: 100%;
      padding: 14px 16px;
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
      border-radius: 10px;
      color: var(--white);
      font-family: 'Barlow', sans-serif;
      font-size: .95rem;
      outline: none;
      transition: border-color .2s;
    }
    .form-group input:focus, .form-group textarea:focus { border-color: var(--red); }
    .form-group textarea { resize: vertical; min-height: 80px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    /* ── Payment methods ─── */
    .payment-section { margin-top: 28px; }
    .payment-section > label {
      display: block;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .75rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      color: var(--grey);
      margin-bottom: 14px;
    }

    .payment-options {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
    }

    .payment-card {
      position: relative;
      cursor: pointer;
    }
    .payment-card input {
      position: absolute;
      opacity: 0;
      inset: 0;
      cursor: pointer;
    }
    .payment-card-body {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      padding: 20px 12px;
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
      border-radius: 12px;
      transition: border-color .2s, background .2s, transform .15s;
      text-align: center;
    }
    .payment-card input:checked + .payment-card-body {
      border-color: var(--red);
      background: rgba(232,25,44,.08);
      transform: translateY(-2px);
    }
    .payment-card:hover .payment-card-body { border-color: rgba(255,255,255,.25); }

    .payment-logo {
      width: 48px; height: 48px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
    }
    .payment-logo svg { width: 24px; height: 24px; stroke: var(--white); fill: none; stroke-width: 2; }
    .payment-logo-esewa   { background: var(--green); }
    .payment-logo-banking { background: var(--blue); }
    .payment-logo-cod     { background: var(--amber); }
    .payment-logo-cod svg { stroke: #111; }

    .payment-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: .82rem;
      letter-spacing: .04em;
      color: var(--white);
    }
    .payment-sub {
      font-size: .68rem;
      color: var(--grey);
    }

    .payment-note {
      margin-top: 16px;
      font-size: .8rem;
      color: var(--grey);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .payment-note svg { width: 15px; height: 15px; stroke: var(--grey); fill: none; stroke-width: 2; flex-shrink: 0; }

    /* ── Confirmation view ─── */
    .confirmation-card {
      grid-column: 1 / -1;
      max-width: 640px;
      margin: 20px auto 0;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 48px;
      text-align: center;
    }
    .confirmation-icon {
      width: 72px; height: 72px;
      background: rgba(29,185,84,.12);
      border: 1px solid rgba(29,185,84,.4);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
    }
    .confirmation-icon svg { width: 34px; height: 34px; stroke: var(--green); fill: none; stroke-width: 2.4; }
    .confirmation-card h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2rem;
      margin-bottom: 10px;
    }
    .confirmation-card p.sub { color: var(--grey); margin-bottom: 28px; }
    .order-id-pill {
      display: inline-block;
      background: rgba(255,255,255,.05);
      border: 1px solid var(--border);
      padding: 10px 22px;
      border-radius: 50px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      letter-spacing: .1em;
      margin-bottom: 28px;
    }
    .confirmation-details {
      text-align: left;
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 20px 24px;
      margin-bottom: 28px;
    }
    .confirmation-details .row {
      display: flex;
      justify-content: space-between;
      font-size: .9rem;
      padding: 8px 0;
      border-bottom: 1px solid var(--border);
      color: var(--grey);
    }
    .confirmation-details .row:last-child { border-bottom: none; }
    .confirmation-details .row span:last-child { color: var(--white); font-weight: 600; }

    /* ── Responsive ─── */
    @media (max-width: 1000px) {
      nav, .page-header, .cart-wrap { padding-left: 24px; padding-right: 24px; }
      .cart-wrap, .checkout-grid { grid-template-columns: 1fr; }
      .summary-card { position: static; }
      .payment-options { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
      .cart-item { grid-template-columns: 70px 1fr; }
      .cart-item-img { width: 70px; height: 70px; }
      .cart-item-right { grid-column: 1 / -1; flex-direction: row; align-items: center; justify-content: space-between; margin-top: 10px; }
      .form-row { grid-template-columns: 1fr; }
      .payment-options { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">
    <span class="sneak">Sneak</span><span class="peak">Peak</span>
  </a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="products.php">Shop</a>
    <a href="AboutUs.php">About</a>
    <a href="#">Contact</a>
    <a href="cart.php" class="nav-cart active" title="View Cart">
      <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <span class="cart-badge"><?= cart_count() ?></span>
    </a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <span class="nav-user"><?= htmlspecialchars($_SESSION['email']) ?></span>
      <a href="logout.php" class="nav-logout">Logout</a>
    <?php else: ?>
      <a href="login.php" class="nav-login">Login</a>
    <?php endif; ?>
  </div>
</nav>

<div class="page-header">
  <h1 class="page-title"><?= $view === 'confirmation' ? 'Order Confirmed' : ($view === 'checkout' ? 'Checkout' : 'Your Cart') ?></h1>
  <div class="step-track">
    <span class="step <?= $view === 'cart' ? 'active' : 'done' ?>">1. Cart</span>
    <span class="step-arrow">→</span>
    <span class="step <?= $view === 'checkout' ? 'active' : ($view === 'confirmation' ? 'done' : '') ?>">2. Checkout</span>
    <span class="step-arrow">→</span>
    <span class="step <?= $view === 'confirmation' ? 'active' : '' ?>">3. Payment</span>
  </div>
</div>

<div class="cart-wrap">

<?php if ($view === 'cart'): ?>

  <?php if (empty($cartItems)): ?>
    <div class="empty-cart">
      <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <h2>Your cart is empty</h2>
      <p>Looks like you haven't added any sneakers yet.</p>
      <a href="products.php">Start Shopping</a>
    </div>
  <?php else: ?>

    <form method="POST" action="cart.php" class="cart-items-form">
      <input type="hidden" name="action" value="update_cart">
      <div class="cart-items">
        <?php foreach ($cartItems as $item): ?>
          <div class="cart-item">
            <div class="cart-item-img">
              <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            </div>
            <div class="cart-item-info">
              <p class="cart-item-brand"><?= htmlspecialchars($item['brand']) ?></p>
              <p class="cart-item-name"><?= htmlspecialchars($item['name']) ?></p>
              <p class="cart-item-size">Size: <?= htmlspecialchars($item['size']) ?> · Rs.<?= number_format($item['price'], 0) ?> each</p>
              <div class="qty-stepper">
                <button type="button" class="qty-minus">−</button>
                <input type="number" name="qty[<?= htmlspecialchars($item['key']) ?>]" value="<?= $item['qty'] ?>" min="0" max="10">
                <button type="button" class="qty-plus">+</button>
              </div>
            </div>
            <div class="cart-item-right">
              <p class="cart-item-price">Rs.<?= number_format($item['lineTotal'], 0) ?></p>
              <button type="button" class="remove-btn" onclick="document.getElementById('remove-form-<?= htmlspecialchars($item['key']) ?>').submit();">
                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                Remove
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="cart-update-row">
        <button type="submit" class="btn-update">Update Quantities</button>
      </div>
    </form>

    <!-- separate small forms for remove, one per item, injected via JS button below each row -->
    <?php foreach ($cartItems as $item): ?>
      <form method="POST" action="cart.php" id="remove-form-<?= htmlspecialchars($item['key']) ?>" style="display:none;">
        <input type="hidden" name="action" value="remove">
        <input type="hidden" name="key" value="<?= htmlspecialchars($item['key']) ?>">
      </form>
    <?php endforeach; ?>

    <div class="summary-card">
      <h3>Order Summary</h3>
      <div class="summary-row"><span>Subtotal</span><span>Rs.<?= number_format($subtotal, 0) ?></span></div>
      <div class="summary-row"><span>Shipping</span><span>Rs.<?= number_format($shipping, 0) ?></span></div>
      <div class="summary-row total"><span>Total</span><span>Rs.<?= number_format($total, 0) ?></span></div>
      <form method="GET" action="cart.php">
        <input type="hidden" name="view" value="checkout">
        <button type="submit" class="btn-checkout">Proceed to Checkout</button>
      </form>
      <a href="products.php" class="continue-shopping">Continue Shopping</a>
    </div>

  <?php endif; ?>

<?php elseif ($view === 'checkout'): ?>

  <div class="checkout-grid">
    <div class="checkout-form-card">

      <?php if (!empty($errors)): ?>
        <div class="form-errors">
          <ul>
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="cart.php">
        <input type="hidden" name="action" value="place_order">

        <h3>Delivery Details</h3>
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" placeholder="e.g. Aayush Sharma" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="98XXXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" placeholder="e.g. Kathmandu" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="address">Delivery Address</label>
          <textarea id="address" name="address" placeholder="Street, ward no., landmark..." required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>

        <div class="payment-section">
          <label>Payment Method</label>
          <div class="payment-options">

            <label class="payment-card">
              <input type="radio" name="payment_method" value="esewa" <?= (($_POST['payment_method'] ?? '') === 'esewa') ? 'checked' : '' ?>>
              <div class="payment-card-body">
                <div class="payment-logo payment-logo-esewa">
                  <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="16" cy="14.5" r="1.4" fill="currentColor" stroke="none"/></svg>
                </div>
                <span class="payment-name">eSewa</span>
                <span class="payment-sub">Digital wallet</span>
              </div>
            </label>

            <label class="payment-card">
              <input type="radio" name="payment_method" value="mobile_banking" <?= (($_POST['payment_method'] ?? '') === 'mobile_banking') ? 'checked' : '' ?>>
              <div class="payment-card-body">
                <div class="payment-logo payment-logo-banking">
                  <svg viewBox="0 0 24 24"><path d="M3 21h18"/><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/></svg>
                </div>
                <span class="payment-name">Mobile Banking</span>
                <span class="payment-sub">Bank app transfer</span>
              </div>
            </label>

            <label class="payment-card">
              <input type="radio" name="payment_method" value="cod" <?= (($_POST['payment_method'] ?? '') === 'cod') ? 'checked' : '' ?>>
              <div class="payment-card-body">
                <div class="payment-logo payment-logo-cod">
                  <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="12" rx="2"/><circle cx="12" cy="13" r="3"/></svg>
                </div>
                <span class="payment-name">Cash on Delivery</span>
                <span class="payment-sub">Pay when it arrives</span>
              </div>
            </label>

          </div>
          <p class="payment-note">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            This is a demo checkout — no real payment is processed for any option.
          </p>
        </div>

        <button type="submit" class="btn-place-order">Place Order · Rs.<?= number_format($total, 0) ?></button>
      </form>
    </div>

    <div class="summary-card">
      <h3>Order Summary</h3>
      <?php foreach ($cartItems as $item): ?>
        <div class="summary-row">
          <span><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['size']) ?>) × <?= $item['qty'] ?></span>
          <span>Rs.<?= number_format($item['lineTotal'], 0) ?></span>
        </div>
      <?php endforeach; ?>
      <div class="summary-row"><span>Subtotal</span><span>Rs.<?= number_format($subtotal, 0) ?></span></div>
      <div class="summary-row"><span>Shipping</span><span>Rs.<?= number_format($shipping, 0) ?></span></div>
      <div class="summary-row total"><span>Total</span><span>Rs.<?= number_format($total, 0) ?></span></div>
    </div>
  </div>

<?php elseif ($view === 'confirmation' && $order): ?>

  <div class="confirmation-card">
    <div class="confirmation-icon">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h2>Thank you, <?= htmlspecialchars($order['name']) ?>!</h2>
    <p class="sub">Your order has been placed successfully.</p>
    <div class="order-id-pill">Order #<?= htmlspecialchars($order['order_id']) ?></div>

    <div class="confirmation-details">
      <div class="row"><span>Placed on</span><span><?= htmlspecialchars($order['placed_at']) ?></span></div>
      <div class="row"><span>Payment method</span><span><?= htmlspecialchars($paymentLabels[$order['payment']] ?? $order['payment']) ?></span></div>
      <div class="row"><span>Delivery address</span><span><?= htmlspecialchars($order['address']) ?></span></div>
      <div class="row"><span>Phone</span><span><?= htmlspecialchars($order['phone']) ?></span></div>
      <div class="row"><span>Items</span><span><?= count($order['items']) ?> line item(s)</span></div>
      <div class="row"><span>Subtotal</span><span>Rs.<?= number_format($order['subtotal'], 0) ?></span></div>
      <div class="row"><span>Shipping</span><span>Rs.<?= number_format($order['shipping'], 0) ?></span></div>
      <div class="row"><span>Total Paid</span><span>Rs.<?= number_format($order['total'], 0) ?></span></div>
    </div>

    <a href="products.php" class="btn-checkout" style="display:block;">Continue Shopping</a>
  </div>

<?php endif; ?>

</div>

<script>
  // Quantity stepper +/- buttons
  document.querySelectorAll('.qty-stepper').forEach(stepper => {
    const input = stepper.querySelector('input');
    stepper.querySelector('.qty-minus').addEventListener('click', () => {
      input.value = Math.max(0, parseInt(input.value || 0) - 1);
    });
    stepper.querySelector('.qty-plus').addEventListener('click', () => {
      input.value = Math.min(10, parseInt(input.value || 0) + 1);
    });
  });
</script>

</body>
</html>