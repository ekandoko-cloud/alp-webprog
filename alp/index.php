<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
$email = $logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : '') : '';

$host = "localhost";
$user = "root";
$password = "";
$database = "industrialhub";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$new_arrivals = [];


// 2. Query New Arrivals: Diurutkan berdasarkan created_at
$sqlNew = "
    SELECT name, sku, selling_price, stock_qty, image_url, created_at
    FROM spare_parts
    ORDER BY created_at DESC
    LIMIT 4
";
$resultNew = $conn->query($sqlNew);
if ($resultNew && $resultNew->num_rows > 0) {
    while ($row = $resultNew->fetch_assoc()) {
        $new_arrivals[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>IndustrialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
          rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet">

</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col font-['DM_Sans']">

<nav class="border-b border-gray-200 bg-white sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-14 relative">

        <a href="index.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>

        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600 font-medium absolute left-1/2 -translate-x-1/2">
            <a href="products.php" class="hover:text-[#1e3a5f] transition-colors">Produk</a>
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
                        <div class="bg-white border border-gray-200 rounded-xl">
                            <div class="p-5 flex items-center gap-3">
                                <span class="material-symbols-outlined text-[40px] text-[#1e3a5f] bg-blue-50 p-2 rounded-full"
                                      style="font-variation-settings:'FILL' 1;">account_circle</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($username) ?></p>
                                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($email) ?></p>
                                </div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <a href="logout.php"
                               class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">logout</span>
                                Logout
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
            <a href="products.php" class="hover:text-[#1e3a5f] transition-colors py-1">Produk</a>
            <a href="industries.php" class="hover:text-[#1e3a5f] transition-colors py-1">Sektor Industri</a>
            <a href="contacts.php" class="hover:text-[#1e3a5f] transition-colors py-1">Kontak</a>
        </div>
    </div>
</nav>

<main class="flex-grow">
    <section class="relative w-full bg-gray-900 border-b border-gray-200 overflow-hidden" style="min-height:480px;">
        <div class="carousel absolute inset-0 z-0">
            <div class="img-wrap flex h-full transition-transform duration-500 ease-in-out">
                <img class="w-full h-full object-cover flex-shrink-0" src="img/wmremove-transformed.jpeg" alt="Image 1">
                <img class="w-full h-full object-cover flex-shrink-0" src="img/wmremove-transformed%20(1).jpeg"
                     alt="Image 2">
                <img class="w-full h-full object-cover flex-shrink-0" src="img/gambar3.png" alt="Image 3">
            </div>
        </div>

        <div class="absolute inset-0 z-10 bg-black/60"></div>

        <div class="absolute inset-0 z-10 opacity-20"
             style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="max-w-6xl mx-auto px-6 py-24 relative z-20 flex flex-col items-center text-center">
            <span class="font-medium text-sm text-blue-300 uppercase tracking-widest mb-4 drop-shadow-md">Sistem Pasok Suku Cadang Industri</span>

            <h1 class="text-3xl md:text-5xl font-bold text-white mb-6 max-w-4xl drop-shadow-lg">
                Supplier Suku Cadang Industri<br>Terintegrasi & Andal.
            </h1>

            <p class="text-lg text-gray-200 mb-12 max-w-2xl drop-shadow-md">
                Platform e-commerce pengadaan suku cadang dengan manajemen stok real-time, data teknis lengkap, dan alat
                pengadaan grosir untuk efisiensi bisnis Anda.
            </p>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-16 border-b border-gray-100">
        <div class="text-center mb-12">
            <h2 class="text-2xl font-bold text-gray-900">Cara Kerja Sistem Pasok</h2>
            <p class="text-gray-600 mt-2">Proses pengadaan suku cadang yang sederhana, cepat, dan terstruktur untuk
                kebutuhan industri Anda.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center relative">
            <div class="hidden md:block absolute top-10 left-[16%] right-[16%] h-0.5 bg-gray-200 z-0"></div>

            <div class="relative z-10 flex flex-col items-center bg-white">
                <div class="w-20 h-20 bg-blue-50 text-[#1e3a5f] rounded-full flex items-center justify-center mb-4 border-4 border-white shadow-sm">
                    <span class="material-symbols-outlined text-[32px]">search</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">1. Cari Suku Cadang</h3>
                <p class="text-sm text-gray-600">Temukan suku cadang yang tepat dengan fitur pencarian berdasarkan SKU,
                    nama, atau kategori industri yang sesuai dengan kebutuhan mesin Anda.</p>
            </div>

            <div class="relative z-10 flex flex-col items-center bg-white">
                <div class="w-20 h-20 bg-blue-50 text-[#1e3a5f] rounded-full flex items-center justify-center mb-4 border-4 border-white shadow-sm">
                    <span class="material-symbols-outlined text-[32px]">shopping_cart</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">2. Pesan via Keranjang</h3>
                <p class="text-sm text-gray-600">Atur kuantitas, tambahkan ke keranjang, dan lakukan checkout. Sistem
                    akan memperbarui stok secara otomatis saat pesanan dikonfirmasi.</p>
            </div>

            <div class="relative z-10 flex flex-col items-center bg-white">
                <div class="w-20 h-20 bg-blue-50 text-[#1e3a5f] rounded-full flex items-center justify-center mb-4 border-4 border-white shadow-sm">
                    <span class="material-symbols-outlined text-[32px]">local_shipping</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">3. Barang Diterima</h3>
                <p class="text-sm text-gray-600">Admin memproses pengiriman dan stok akan terkelola secara real-time.
                    Anda bisa melacak status pesanan melalui sistem.</p>
            </div>
        </div>
    </section>


    <section class="max-w-6xl mx-auto px-6 py-16 bg-gray-50 rounded-xl mb-16">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Produk Terbaru</h2>
                <p class="text-gray-600">Suku cadang dan komponen terbaru yang baru masuk ke gudang kami, siap memenuhi
                    kebutuhan operasional Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($new_arrivals as $item): ?>
                    <div class="relative bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md hover:border-[#1e3a5f] transition-all flex flex-col">
                        <div class="absolute top-3 left-3 bg-[#1e3a5f] text-white text-[10px] font-bold px-2 py-1 rounded tracking-wider z-10 uppercase">
                            NEW
                        </div>
                        <div class="h-48 bg-gray-100 flex items-center justify-center border-b border-gray-200 relative">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Product"
                                     class="h-full w-full object-cover">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-gray-300 text-[64px]">NO IMAGE</span>
                            <?php endif; ?>
                        </div>
                        <div class="p-5 flex-grow flex flex-col">
                            <div class="text-xs text-gray-500 font-mono mb-1">
                                SKU: <?= htmlspecialchars($item['sku']) ?></div>
                            <h3 class="text-md font-bold text-gray-900 mb-3 flex-grow"><?= htmlspecialchars($item['name']) ?></h3>
                            <div class="flex justify-between items-end mt-auto">
                                <div class="text-lg font-bold text-[#1e3a5f]">
                                    Rp <?= number_format($item['selling_price'], 0, ',', '.') ?></div>
                                <div class="text-xs font-medium px-2 py-1 bg-green-50 text-green-700 rounded border border-green-100">
                                    Stok: <?= htmlspecialchars($item['stock_qty']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 py-16">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900">Tanya Jawab Seputar Sistem Pasok</h2>
            <p class="text-gray-600 mt-2">Informasi lengkap seputar penggunaan platform pengadaan suku cadang industri
                kami.</p>
        </div>

        <div class="space-y-4">
            <details
                    class="group bg-white border border-gray-200 rounded-lg shadow-sm">
                <summary
                        class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-gray-900 hover:text-[#1e3a5f] transition-colors">
                    <span>Apa itu sistem pasok suku cadang industri?</span>
                    <span class="transition group-open:rotate-180">
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                </summary>
                <div class="p-5 pt-0 text-gray-600 text-sm leading-relaxed border-t border-gray-100 mt-2">
                    Sistem pasok suku cadang industri adalah platform e-commerce yang menghubungkan pemasok (supplier)
                    dengan pelaku industri untuk memenuhi kebutuhan komponen mesin dan peralatan produksi. IndustrialHub
                    menyediakan katalog lengkap dengan informasi teknis, stok real-time, dan fitur pengadaan yang
                    terintegrasi.
                </div>
            </details>

            <details
                    class="group bg-white border border-gray-200 rounded-lg shadow-sm [&_summary::-webkit-details-marker]:hidden">
                <summary
                        class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-gray-900 hover:text-[#1e3a5f] transition-colors">
                    <span>Bagaimana cara melakukan pemesanan?</span>
                    <span class="transition group-open:rotate-180">
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                </summary>
                <div class="p-5 pt-0 text-gray-600 text-sm leading-relaxed border-t border-gray-100 mt-2">
                    Prosesnya mudah: Cari produk melalui fitur pencarian berdasarkan SKU atau nama, tambahkan ke
                    keranjang, lalu selesaikan pemesanan. Setelah pesanan dikonfirmasi, sistem akan memperbarui stok
                    secara otomatis dan admin akan memproses pengiriman.
                </div>
            </details>

            <details
                    class="group bg-white border border-gray-200 rounded-lg shadow-sm [&_summary::-webkit-details-marker]:hidden">
                <summary
                        class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-gray-900 hover:text-[#1e3a5f] transition-colors">
                    <span>Apakah tersedia pembelian satuan (retail)?</span>
                    <span class="transition group-open:rotate-180">
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                </summary>
                <div class="p-5 pt-0 text-gray-600 text-sm leading-relaxed border-t border-gray-100 mt-2">
                    Tentu. Meskipun sistem ini dirancang untuk kebutuhan industri berskala besar, Anda tetap dapat
                    membeli suku cadang secara satuan untuk perbaikan darurat atau perawatan harian, selama stok
                    tersedia. Informasi ketersediaan stok ditampilkan secara real-time.
                </div>
            </details>

            <details
                    class="group bg-white border border-gray-200 rounded-lg shadow-sm [&_summary::-webkit-details-marker]:hidden">
                <summary
                        class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-gray-900 hover:text-[#1e3a5f] transition-colors">
                    <span>Fitur apa saja yang tersedia untuk pemilik bisnis?</span>
                    <span class="transition group-open:rotate-180">
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                </summary>
                <div class="p-5 pt-0 text-gray-600 text-sm leading-relaxed border-t border-gray-100 mt-2">
                    IndustrialHub menyediakan panel admin lengkap dengan manajemen inventaris (Create, Read, Update,
                    Delete spare parts), manajemen supplier, penerimaan barang (purchase order), dan laporan penjualan.
                    Pemilik bisnis dapat memantau stok secara real-time, menerima peringatan stok menipis, serta
                    mengelola data pengguna dengan mudah.
                </div>
            </details>

            <details
                    class="group bg-white border border-gray-200 rounded-lg shadow-sm [&_summary::-webkit-details-marker]:hidden">
                <summary
                        class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-gray-900 hover:text-[#1e3a5f] transition-colors">
                    <span>Bagaimana metode pembayaran dan pengiriman?</span>
                    <span class="transition group-open:rotate-180">
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                </summary>
                <div class="p-5 pt-0 text-gray-600 text-sm leading-relaxed border-t border-gray-100 mt-2">
                    Kami mendukung berbagai metode pembayaran dan bekerja sama dengan jasa logistik untuk pengiriman
                    kargo laut, darat, maupun udara. Faktur pajak juga tersedia bagi perusahaan yang memerlukan klaim
                    PPN.
                </div>
            </details>
        </div>
    </section>

</main>

<footer class="bg-[#1a1a2e] text-white mt-16 mt-auto">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <p class="font-bold text-lg">IndustrialHub</p>
            <p class="text-gray-400 text-sm mt-1">Sistem Pasok Suku Cadang Industri Terpercaya.</p>
        </div>
        <nav class="flex flex-wrap gap-6 text-sm text-gray-400">
            <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
            <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
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