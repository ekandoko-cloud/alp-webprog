<?php
session_start();
// 1. Session & Auth Check
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
$email = $logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : '') : '';

// 2. Database Connection
$host = "localhost";
$user = "root";
$password = "";
$database = "industrialhub"; // Sesuai nama DB Anda

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proteksi akses Admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== 2) {
    header("Location: login.php");
    exit;
}

// 3. Query Statistik Dashboard

// a. Summary Cards (Total Users, Sales Orders, Spare Parts, Suppliers)
// a. Summary Cards (Total Users, Sales Orders, Spare Parts, Suppliers, Purchase Orders)
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_sales_orders = $conn->query("SELECT COUNT(*) as total FROM sales_orders")->fetch_assoc()['total'];
$total_spare_parts = $conn->query("SELECT COUNT(*) as total FROM spare_parts")->fetch_assoc()['total'];
$total_purchase_orders = $conn->query("SELECT COUNT(*) as total FROM purchase_orders")->fetch_assoc()['total'];

// b. Low Stock Alerts (spare_parts dimana stock_qty <= min_stock)
$low_stock_query = $conn->query("SELECT name, stock_qty, min_stock FROM spare_parts WHERE stock_qty <= min_stock");
$has_low_stock = $low_stock_query->num_rows > 0;

// c. Recent Sales Orders Table
$recent_sales_query = $conn->query("
    SELECT so.salesorders_id, u.username AS user, so.order_date, so.total_amount, so.payment_method 
    FROM sales_orders so 
    LEFT JOIN users u ON so.user_id = u.user_id 
    ORDER BY so.order_date DESC 
    LIMIT 5
");

// c2. Recent Purchase Orders Table
$recent_purchase_query = $conn->query("
    SELECT po.purchaseorders_id, s.name AS supplier, po.order_date, po.created_at,
           COALESCE(SUM(od.qty_ordered * od.unit_price), 0) AS total_amount
    FROM purchase_orders po
    LEFT JOIN suppliers s ON po.suppliers_id = s.suppliers_id
    LEFT JOIN order_details od ON po.purchaseorders_id = od.purchaseorders_id
    GROUP BY po.purchaseorders_id, s.name, po.order_date, po.created_at
    ORDER BY po.created_at DESC, po.order_date DESC
    LIMIT 5
");

// d. Chart Data: Grafik total_amount dari sales_orders per bulan (6 bulan terakhir)
$chart_query = $conn->query("
    SELECT DATE_FORMAT(order_date, '%b %Y') as month_name, SUM(total_amount) as total 
    FROM sales_orders 
    GROUP BY DATE_FORMAT(order_date, '%Y-%m'), DATE_FORMAT(order_date, '%b %Y') 
    ORDER BY DATE_FORMAT(order_date, '%Y-%m') ASC 
    LIMIT 6
");

$chart_labels = [];
$chart_data = [];
while ($row = $chart_query->fetch_assoc()) {
    $chart_labels[] = $row['month_name'];
    $chart_data[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard - IndustrialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet">
    <style>body {
            font-family: 'DM Sans', sans-serif;
        }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="border-b border-gray-200 bg-white h-14 flex items-center shrink-0 z-50">
    <div class="max-w-[1400px] mx-auto px-6 flex items-center justify-between w-full">
            <a href="landing.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub - Admin</a>
        <div class="flex-1 flex items-center justify-end gap-3">
            <button id="darkToggle" class="text-gray-600 hover:text-[#1e3a5f] transition-colors p-1" title="Toggle Dark Mode">
                <span class="material-symbols-outlined text-[20px]">dark_mode</span>
            </button>
            <div class="relative group">
                <button class="flex items-center gap-2 border border-gray-300 rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 bg-white group-hover:bg-gray-50 transition cursor-default">
                    <span class="material-symbols-outlined text-[20px] text-[#1e3a5f]"
                          style="font-variation-settings:'FILL' 1;">account_circle</span>
                    <span class="hidden md:block">Admin</span>
                    <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:rotate-180 transition-transform duration-200">expand_more</span>
                </button>
                <div class="absolute right-0 top-full pt-2 w-64 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-5 flex items-center gap-3">
                            <span class="material-symbols-outlined text-[40px] text-[#1e3a5f] bg-blue-50 p-2 rounded-full"
                                  style="font-variation-settings:'FILL' 1;">account_circle</span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">admin</p>
                                <p class="text-xs text-gray-500 truncate">admin@gmail.com</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-200"></div>
                        <a href="logout.php"
                           class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">logout</span> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="flex flex-grow">
    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 min-h-full">
        <div class="p-6 flex items-center gap-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-full bg-slate-300 overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=Warehouse+Admin" alt="Admin">
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">Warehouse Admin</p>
            </div>
        </div>
        <nav class="flex-grow p-4 space-y-1">
            <a href="adminmenu.php"
               class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-[#1e3a5f] rounded-lg font-medium text-sm">
                <span class="material-symbols-outlined">dashboard</span> Dashboard
            </a>
            <a href="inventory.php"
               class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">inventory_2</span> Manajemen Inventaris
            </a>
            <a href="sales.php"
               class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">receipt_long</span> Transaksi Penjualan
            </a>
            <a href="admin_users.php"
               class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">group</span> Kelola Pengguna
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-grow p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-[#1e3a5f]">Dashboard Admin - Manajemen Pasok Suku Cadang</h1>
            <p class="text-sm text-slate-500">Ringkasan operasional sistem pasok suku cadang, data inventaris, dan peringatan stok.</p>
        </div>

        <?php if ($has_low_stock): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-lg shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-red-600">warning</span>
                    <h3 class="text-red-800 font-bold">Peringatan: Stok Suku Cadang Menipis! Segera Lakukan Restock.</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 ml-2">
                    <?php while ($item = $low_stock_query->fetch_assoc()): ?>
                        <li>
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            sisa stok: <?= $item['stock_qty'] ?> (Minimum: <?= $item['min_stock'] ?>) - <em>Perlu
                                Restock!</em>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex flex-col">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Pengguna</p>
                <h2 class="text-3xl font-bold text-slate-900 mt-2"><?= $total_users ?></h2>
                <div class="mt-auto pt-4"><span class="text-xs text-slate-400">Pengguna terdaftar di sistem</span></div>
            </div>
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex flex-col">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Pesanan Penjualan</p>
                <h2 class="text-3xl font-bold text-slate-900 mt-2"><?= $total_sales_orders ?></h2>
                <div class="mt-auto pt-4"><span class="text-xs text-slate-400">Transaksi dari pelanggan</span>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex flex-col">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Suku Cadang</p>
                <h2 class="text-3xl font-bold text-slate-900 mt-2"><?= $total_spare_parts ?></h2>
                <div class="mt-auto pt-4"><span class="text-xs text-slate-400">Item di gudang</span></div>
            </div>
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex flex-col">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Purchase Orders</p>
                <h2 class="text-3xl font-bold text-slate-900 mt-2"><?= $total_purchase_orders ?></h2>
                <div class="mt-auto pt-4"><span class="text-xs text-slate-400">Pembelian ke supplier</span></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-lg text-slate-800">Pesanan Penjualan Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-semibold">ID PESANAN</th>
                        <th class="px-6 py-3 font-semibold">PELANGGAN</th>
                        <th class="px-6 py-3 font-semibold">TANGGAL</th>
                        <th class="px-6 py-3 font-semibold">TOTAL</th>
                        <th class="px-6 py-3 font-semibold">METODE BAYAR</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <?php if ($recent_sales_query->num_rows > 0): ?>
                        <?php while ($row = $recent_sales_query->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    #<?= htmlspecialchars($row['salesorders_id']) ?></td>
                                <td class="px-6 py-4 text-slate-700"><?= htmlspecialchars($row['user'] ?? 'Guest') ?></td>
                                <td class="px-6 py-4 text-slate-500"><?= date('d M Y, H:i', strtotime($row['order_date'])) ?></td>
                                <td class="px-6 py-4 font-bold text-green-600">
                                    Rp <?= number_format($row['total_amount'], 2, ',', '.') ?></td>
                                <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium border border-blue-100">
                                    <?= htmlspecialchars($row['payment_method'] ?? '-') ?>
                                </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data penjualan
                                terbaru.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm mt-8">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-lg text-slate-800">Pesanan Pembelian Terbaru (Purchase Order)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-semibold">ID PO</th>
                        <th class="px-6 py-3 font-semibold">SUPPLIER</th>
                        <th class="px-6 py-3 font-semibold">TANGGAL</th>
                        <th class="px-6 py-3 font-semibold">TOTAL</th>
                        <th class="px-6 py-3 font-semibold">CATATAN</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <?php if ($recent_purchase_query->num_rows > 0): ?>
                        <?php while ($row = $recent_purchase_query->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    #<?= htmlspecialchars($row['purchaseorders_id']) ?></td>
                                <td class="px-6 py-4 text-slate-700"><?= htmlspecialchars($row['supplier'] ?? 'Unknown') ?></td>
                                <td class="px-6 py-4 text-slate-500"><?= date('d M Y', strtotime($row['order_date'])) ?></td>
                                <td class="px-6 py-4 font-bold text-orange-600">
                                    Rp <?= number_format($row['total_amount'], 2, ',', '.') ?></td>
                                <td class="px-6 py-4 text-slate-500 truncate max-w-xs"><?= htmlspecialchars($row['notes'] ?? '-') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data pembelian
                                terbaru.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- FOOTER -->
<footer class="bg-[#1a1a2e] text-white mt-auto">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <p class="font-bold text-lg">IndustrialHub</p>
            <p class="text-gray-400 text-sm mt-1">Sistem Pasok Suku Cadang Industri Terpercaya.</p>
        </div>
        <nav class="flex flex-wrap gap-6 text-sm text-gray-400">
            <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
            <a href="#" class="hover:text-white transition">Syarat &amp; Ketentuan</a>
            <a href="#" class="hover:text-white transition">Bantuan Teknis</a>
        </nav>
        <p class="text-sm text-gray-500 whitespace-nowrap">© 2024 IndustrialHub. Hak Cipta Dilindungi.</p>
    </div>
</footer>

<button id="backToTop" aria-label="Back to top">
  <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>