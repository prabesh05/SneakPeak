<?php
include 'database.php';
include 'cartHelper.php';

header('Content-Type: application/json');

$id   = isset($_POST['id']) ? intval($_POST['id']) : 0;
$size = isset($_POST['size']) ? trim($_POST['size']) : '';
$qty  = isset($_POST['qty']) ? intval($_POST['qty']) : 1;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing product.']);
    exit;
}

if ($size === '') {
    echo json_encode(['success' => false, 'message' => 'Please select a size first.']);
    exit;
}

$result = mysqli_query($conn, "SELECT id FROM products WHERE id = $id");
if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

cart_add($id, $size, $qty);

echo json_encode([
    'success'   => true,
    'cartCount' => cart_count(),
]);