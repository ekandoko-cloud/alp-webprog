<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'add') {
    header("Location: products.php");
    exit;
}

$id    = (int) ($_POST['spareparts_id'] ?? 0);
$name  = trim($_POST['name'] ?? '');
$price = (float) ($_POST['price'] ?? 0);
$qty   = (int) ($_POST['qty'] ?? 0);

if ($id <= 0 || $qty <= 0 || $name === '' || $price < 0) {
    echo "<script>alert('Data produk tidak valid!'); window.location='products.php';</script>";
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty'] += $qty;
} else {
    $_SESSION['cart'][$id] = [
        'name'  => $name,
        'price' => $price,
        'qty'   => $qty,
    ];
}

echo "<script>alert('Suku cadang berhasil ditambahkan ke keranjang!'); window.location='products.php';</script>";
exit;
