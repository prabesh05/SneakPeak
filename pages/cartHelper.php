<?php
// ── Cart Helper ───────────────────────────────────────────────
// Session-based cart. No real payment processing — cart items are
// stored as: key => ['id' => productId, 'size' => 'UK 8', 'qty' => 2]
// key format: "{productId}_{size}" so the same shoe in two sizes
// is tracked as two separate lines.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function cart_key($id, $size) {
    return intval($id) . '_' . preg_replace('/[^A-Za-z0-9]/', '', $size);
}

function cart_add($id, $size, $qty = 1) {
    $qty = max(1, intval($qty));
    $key = cart_key($id, $size);
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$key] = [
            'id'   => intval($id),
            'size' => $size,
            'qty'  => $qty,
        ];
    }
}

function cart_set_qty($key, $qty) {
    $qty = intval($qty);
    if (!isset($_SESSION['cart'][$key])) return;
    if ($qty <= 0) {
        unset($_SESSION['cart'][$key]);
    } else {
        $_SESSION['cart'][$key]['qty'] = min($qty, 10); // sane per-line cap
    }
}

function cart_remove($key) {
    unset($_SESSION['cart'][$key]);
}

function cart_clear() {
    $_SESSION['cart'] = [];
}

function cart_count() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) $count += $item['qty'];
    return $count;
}

function cart_is_empty() {
    return empty($_SESSION['cart']);
}

// Joins session cart quantities with live product data from the DB,
// so price/name/image always reflect the current catalog.
function cart_items_with_products($conn) {
    $items = [];
    if (empty($_SESSION['cart'])) return $items;

    $ids = [];
    foreach ($_SESSION['cart'] as $line) $ids[] = intval($line['id']);
    $ids = array_unique($ids);
    if (empty($ids)) return $items;

    $idList = implode(',', $ids);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($idList)");
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[$row['id']] = $row;
        }
    }

    foreach ($_SESSION['cart'] as $key => $line) {
        $pid = intval($line['id']);
        if (!isset($products[$pid])) continue; // product removed from catalog
        $p = $products[$pid];
        $items[] = [
            'key'      => $key,
            'id'       => $pid,
            'size'     => $line['size'],
            'qty'      => $line['qty'],
            'name'     => $p['name'],
            'brand'    => $p['brand'],
            'img'      => $p['img'],
            'price'    => $p['price'],
            'lineTotal'=> $p['price'] * $line['qty'],
        ];
    }
    return $items;
}

function cart_subtotal($conn) {
    $subtotal = 0;
    foreach (cart_items_with_products($conn) as $item) {
        $subtotal += $item['lineTotal'];
    }
    return $subtotal;
}