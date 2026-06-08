<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
if (!$logged_in) {
    echo "<script>alert('Silakan masuk terlebih dahulu untuk mengakses katalog produk!'); window.location='login.php';</script>";
    exit;
}
$username = $_SESSION['username'];
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

$conn = new mysqli("localhost", "root", "", "industrialhub");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);


$where_clauses = [];
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clauses[] = "s.name LIKE '%$search%'";
}
if (!empty($_GET['category'])) {
    $cat = (int)$_GET['category'];
    $where_clauses[] = "s.categories_id = $cat";
}
$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

$query = "SELECT s.*, c.name as category_name FROM spare_parts s LEFT JOIN categories c ON s.categories_id = c.categories_id $where_sql";
$result = $conn->query($query);
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>IndustrialHub - Product Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
          rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col">

<!-- TopNavBar -->
<nav class="border-b border-gray-200 bg-white sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-14 relative">
        <a href="index.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>

        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600 font-medium absolute left-1/2 -translate-x-1/2">
            <a href="products.php"
               class="text-[#1e3a5f] font-bold underline decoration-2 underline-offset-8 transition-colors">Produk</a>
            <a href="industries.php" class="hover:text-[#1e3a5f] transition-colors">Sektor Industri</a>

            <a href="contacts.php" class="hover:text-[#1e3a5f] transition-colors">Kontak</a>
        </div>

        <div class="flex items-center gap-2">
            <button id="darkToggle" class="text-gray-600 hover:text-[#1e3a5f] transition-colors p-1"
                    title="Toggle Dark Mode">
                <span class="material-symbols-outlined text-[20px]">dark_mode</span>
            </button>
            <?php if ($logged_in): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 border border-gray-300 rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 bg-white group-hover:bg-gray-50 transition cursor-default">
                        <span class="material-symbols-outlined text-[20px] text-[#1e3a5f]"
                              style="font-variation-settings:'FILL' 1;">account_circle</span>
                        <span class="hidden md:block"><?= htmlspecialchars($username) ?></span>
                        <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:rotate-180 transition-transform duration-200">expand_more</span>
                    </button>
                    <div class="absolute right-0 top-full pt-2 w-64 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-5 flex items-center gap-3">
                                <span class="material-symbols-outlined text-[40px] text-[#1e3a5f] bg-blue-50 p-2 rounded-full"
                                      style="font-variation-settings:'FILL' 1;">account_circle</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($username) ?></p>
                                    <?php if ($email): ?><p
                                            class="text-xs text-gray-500 truncate"><?= htmlspecialchars($email) ?></p><?php endif; ?>
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
            <?php else: ?>
                <a href="login.php"
                   class="border border-gray-300 text-sm font-medium px-4 py-1.5 rounded-md text-gray-700 hover:bg-gray-50 transition">Masuk</a>
            <?php endif; ?>
            <a href="cartmenu.php"
               class="bg-[#1e3a5f] text-white text-sm font-medium px-3 py-1.5 rounded-md flex items-center gap-1 hover:bg-[#162d4a] transition">
                <span class="material-symbols-outlined text-[18px]">shopping_cart</span><span class="hidden sm:inline">Keranjang</span>
            </a>
            <button id="hamburger" class="md:hidden p-1 text-gray-600 hover:text-[#1e3a5f] transition-colors"
                    aria-label="Menu">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>
        </div>
    </div>

    <div id="mobileMenu" class="hidden md:hidden border-t border-gray-200 bg-white">
        <div class="px-6 py-4 flex flex-col gap-3 text-sm text-gray-600 font-medium">
            <a href="products.php" class="text-[#1e3a5f] font-bold underline decoration-2 underline-offset-8 py-1">Produk</a>
            <a href="industries.php" class="hover:text-[#1e3a5f] transition-colors py-1">Sektor Industri</a>
            <a href="contacts.php" class="hover:text-[#1e3a5f] transition-colors py-1">Kontak</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="flex-grow flex flex-col md:flex-row mt-10 w-full max-w-6xl mx-auto px-6">
    <aside class="w-full md:w-64 flex-shrink-0 pr-6">
        <form method="GET" class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase">Cari Produk</label>
                <input name="search" class="w-full pl-3 py-2 bg-gray-50 border rounded-md text-sm"
                       placeholder="Cari suku cadang..." type="text"
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"/>
            </div>
            <div class="flex flex-col gap-3">
                <h3 class="text-xs font-bold text-gray-500 uppercase">Kategori</h3>
                <select name="category" class="w-full border p-2 rounded text-sm">
                    <option value="">Semua Kategori</option>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?= $c['categories_id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $c['categories_id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="bg-[#1e3a5f] text-white py-2 rounded text-sm font-bold">Terapkan Filter
            </button>
        </form>
    </aside>

    <section class="flex-1 md:pl-6 pb-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php while ($row = $result->fetch_assoc()):
                $is_available = $row['stock_qty'] > $row['min_stock'];
                ?>
                <article class="bg-white rounded-lg p-5 flex flex-col border border-gray-100 shadow-sm">
                    <div class="h-48 w-full bg-gray-50 rounded-md border mb-4 overflow-hidden relative">
                        <img class="w-full h-full object-cover"
                             src="<?= htmlspecialchars($row['image_url'] ?? 'images/default.jpg') ?>"/>
                        <span class="absolute top-2 left-2 <?= $is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> text-xs font-bold px-2 py-1 rounded">
                        <?= $is_available ? 'STOK TERSEDIA' : 'STOK HABIS' ?>
                    </span>
                    </div>
                    <div class="flex flex-col flex-1">
                        <span class="font-mono text-xs text-gray-500 mb-1">SKU: <?= htmlspecialchars($row['sku']) ?></span>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($row['name']) ?></h2>
                        <p class="text-xs text-gray-400 mb-4"><?= htmlspecialchars($row['description'] ?? '-') ?></p>
                        <div class="mt-auto">
                            <p class="text-xl font-bold text-gray-900 mb-3">
                                Rp <?= number_format($row['selling_price'], 0, ',', '.') ?></p>
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="spareparts_id" value="<?= $row['spareparts_id'] ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                                <input type="hidden" name="price" value="<?= $row['selling_price'] ?>">
                                <input type="hidden" name="max_stock" value="<?= $row['stock_qty'] ?>">

                                <input type="number" name="qty" value="1" min="1" max="<?= $row['stock_qty'] ?>"
                                       class="w-full border p-2 mb-2 rounded text-sm">
                                <button type="submit" <?= !$is_available ? 'disabled' : '' ?>
                                        class="w-full bg-[#1e3a5f] disabled:bg-gray-400 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                                    + Tambah ke Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-[#1a1a2e] text-white mt-16 mt-auto">
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
        <p class="text-sm text-gray-500 whitespace-nowrap">© 2026 IndustrialHub. Hak Cipta Dilindungi.</p>
    </div>
</footer>

<button id="backToTop" aria-label="Back to top">
    <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>