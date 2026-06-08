<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once 'koneksi.php';

$host = "localhost";
$user = "root";
$password = "";
$database = "industrialhub";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan masuk terlebih dahulu untuk mengakses keranjang!'); window.location='login.php';</script>";
    exit;
}

$logged_in = isset($_SESSION['user_id']);
$username  = $logged_in ? $_SESSION['username'] : '';
$email     = $logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : '') : '';

$user_id = (int) $_SESSION['user_id'];
$message = '';

// 1. Update Qty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $id      = (int) ($_POST['spareparts_id'] ?? 0);
    $new_qty = (int) ($_POST['qty'] ?? 0);
    if ($id > 0 && $new_qty > 0 && isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] = $new_qty;
    }
    header("Location: cartmenu.php");
    exit;
}

// 2. Hapus Item
if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cartmenu.php");
    exit;
}

// 3. Checkout -> insert sales_orders + sales_order_details, kurangi stok, kosongkan cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $message = "Keranjang kosong, tidak ada yang bisa di-checkout.";
    } else {
        $address = trim($_POST['shipping_address'] ?? '');
        $payment = trim($_POST['payment_method'] ?? '');

        if ($address === '' || $payment === '') {
            $message = "Alamat pengiriman dan metode pembayaran wajib diisi.";
        } else {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += (float) $item['price'] * (int) $item['qty'];
            }

            $stock_ok = true;
            foreach ($_SESSION['cart'] as $id => $item) {
                $id  = (int) $id;
                $qty = (int) $item['qty'];
                $stmt = $conn->prepare("SELECT stock_qty, name FROM spare_parts WHERE spareparts_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$row) {
                    $stock_ok = false;
                    $message = "Produk dengan ID $id tidak ditemukan.";
                    break;
                }
                if ((int) $row['stock_qty'] < $qty) {
                    $stock_ok = false;
                    $message = "Stok tidak cukup untuk produk: " . $row['name'] . " (tersisa " . $row['stock_qty'] . ").";
                    break;
                }
            }

            if ($stock_ok) {
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("INSERT INTO sales_orders (user_id, order_date, total_amount, shipping_address, payment_method, created_at) VALUES (?, NOW(), ?, ?, ?, NOW())");
                    $stmt->bind_param("idss", $user_id, $total, $address, $payment);
                    $stmt->execute();
                    $so_id = (int) $stmt->insert_id;
                    $stmt->close();

                    foreach ($_SESSION['cart'] as $id => $item) {
                        $id    = (int) $id;
                        $qty   = (int) $item['qty'];
                        $price = (float) $item['price'];

                        $stmt = $conn->prepare("INSERT INTO sales_order_details (salesorders_id, spareparts_id, qty, selling_price) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("iiid", $so_id, $id, $qty, $price);
                        $stmt->execute();
                        $stmt->close();

                        $stmt = $conn->prepare("UPDATE spare_parts SET stock_qty = stock_qty - ? WHERE spareparts_id = ?");
                        $stmt->bind_param("ii", $qty, $id);
                        $stmt->execute();
                        $stmt->close();
                    }

                    $conn->commit();
                    $_SESSION['cart'] = [];
                    header("Location: products.php?status=success");
                    exit;
                } catch (Throwable $e) {
                    $conn->rollback();
                    $message = "Gagal memproses pesanan: " . $e->getMessage();
                }
            }
        }
    }
}

$grand_total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += (float) $item['price'] * (int) $item['qty'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Shopping Cart - IndustrialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

<nav class="border-b border-gray-200 bg-white sticky top-0 z-50 h-14 flex items-center">
<div class="max-w-6xl mx-auto px-6 flex items-center w-full">
    <!-- Kiri -->
    <div class="flex-1 flex justify-start">
        <a href="landing.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>
    </div>
    <!-- Tengah -->
    <div class="flex-1 hidden md:flex justify-center items-center gap-8 text-sm text-gray-600 font-medium">
        <a href="products.php" class="hover:text-[#1e3a5f] transition-colors">Produk</a>
        <a href="industries.php" class="hover:text-[#1e3a5f] transition-colors">Sektor Industri</a>
        <a href="contacts.php" class="hover:text-[#1e3a5f] transition-colors">Kontak</a>
    </div>
    <!-- Kanan -->
    <div class="flex-1 flex items-center justify-end gap-3">
        <button id="darkToggle" class="text-gray-600 hover:text-[#1e3a5f] transition-colors p-1" title="Toggle Dark Mode">
            <span class="material-symbols-outlined text-[20px]">dark_mode</span>
        </button>

        <?php if ($logged_in): ?>
            <!-- CSS-Only Dropdown User Menu -->
            <div class="relative group">
                <button class="flex items-center gap-2 border border-gray-300 rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 bg-white group-hover:bg-gray-50 transition cursor-default">
                    <span class="material-symbols-outlined text-[20px] text-[#1e3a5f]" style="font-variation-settings:'FILL' 1;">account_circle</span>
                    <span class="hidden md:block"><?= htmlspecialchars($username) ?></span>
                    <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:rotate-180 transition-transform duration-200">expand_more</span>
                </button>
                <div class="absolute right-0 top-full pt-2 w-64 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-5 flex items-center gap-3">
                            <span class="material-symbols-outlined text-[40px] text-[#1e3a5f] bg-blue-50 p-2 rounded-full" style="font-variation-settings:'FILL' 1;">account_circle</span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($username) ?></p>
                                <?php if ($email): ?>
                                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($email) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border-t border-gray-200"></div>
                        <a href="logout.php" class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" class="border border-gray-300 text-sm font-medium px-4 py-1.5 rounded-md text-gray-700 hover:bg-gray-50 transition">Masuk</a>
        <?php endif; ?>
    </div>
</div>
</nav>

<main class="flex-grow max-w-4xl mx-auto w-full p-6">
    <h1 class="text-2xl font-bold mb-6 text-[#1e3a5f]">Keranjang Belanja</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="bg-white rounded-lg shadow-sm border p-10 text-center">
            <span class="material-symbols-outlined text-6xl text-gray-300">shopping_cart</span>
            <p class="text-gray-500 mt-4">Keranjang kosong.</p>
            <a href="products.php" class="inline-block mt-4 bg-[#1e3a5f] text-white px-6 py-2 rounded font-medium hover:bg-[#152a46]">Lanjut Belanja</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b text-left text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Nama Item</th>
                        <th class="px-6 py-3">Qty</th>
                        <th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3">Subtotal</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <tr class="border-b last:border-0">
                        <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></td>
                        <td class="px-6 py-4">
                            <form method="POST" class="flex items-center gap-2">
                                <input type="hidden" name="spareparts_id" value="<?= htmlspecialchars((string)$id) ?>">
                                <input type="number" name="qty" value="<?= (int)$item['qty'] ?>" min="1" class="w-16 border p-1 rounded text-center">
                                <button type="submit" name="update_qty" class="text-xs text-blue-600 underline">Perbarui</button>
                            </form>
                        </td>
                        <td class="px-6 py-4">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="px-6 py-4 font-bold">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
                        <td class="px-6 py-4">
                            <a href="?remove=<?= (int)$id ?>" class="text-red-500 text-sm underline" onclick="return confirm('Hapus item ini dari keranjang?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-6 py-4 text-right font-bold">Total Belanja</td>
                        <td class="px-6 py-4 font-bold text-[#1e3a5f]">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <form method="POST" class="bg-white rounded-lg shadow-sm border p-6 mt-6">
            <h2 class="font-bold text-lg mb-4 text-[#1e3a5f]">Informasi Pengiriman &amp; Pembayaran</h2>

            <label class="block text-sm font-bold mb-2">Alamat Pengiriman</label>
            <textarea name="shipping_address" required class="w-full border p-2 rounded mb-4 text-sm" placeholder="Masukkan alamat lengkap pengiriman"><?= htmlspecialchars($_POST['shipping_address'] ?? '') ?></textarea>

            <label class="block text-sm font-bold mb-2">Metode Pembayaran</label>
            <select name="payment_method" required class="w-full border p-2 rounded mb-6 text-sm">
                <option value="">-- Pilih Metode --</option>
                <option value="Gopay">Gopay</option>
                <option value="QRIS">QRIS</option>
                <option value="Dana">Dana</option>
                <option value="OVO">OVO</option>
                <option value="ShopeePay">ShopeePay</option>
                <option value="Virtual Account">Virtual Account</option>
            </select>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="products.php" class="flex-1 text-center py-3 border border-gray-300 rounded font-bold text-gray-700 hover:bg-gray-50">Lanjut Belanja</a>
                <button type="submit" name="checkout" class="flex-1 bg-[#1e3a5f] text-white py-3 rounded font-bold hover:bg-[#152a46]" onclick="return confirm('Konfirmasi checkout?')">
                    Proses Pesanan (Checkout)
                </button>
            </div>
        </form>
    <?php endif; ?>
</main>

<button id="backToTop" aria-label="Back to top">
  <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>
