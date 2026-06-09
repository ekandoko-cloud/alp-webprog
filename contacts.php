<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
if (!$logged_in) {
    echo "<script>alert('Silakan masuk terlebih dahulu untuk mengakses halaman kontak!'); window.location='login.php';</script>";
    exit;
}
$username = $_SESSION['username'];
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>IndustrialHub – Global Contact Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
          rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col">


<nav class="border-b border-gray-200 bg-white sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-14 relative">
        <a href="index.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>

        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600 font-medium absolute left-1/2 -translate-x-1/2">
            <a href="products.php" class="hover:text-[#1e3a5f] transition-colors">Produk</a>
            <a href="industries.php" class="hover:text-[#1e3a5f] transition-colors">Sektor Industri</a>
            <a href="contacts.php"
               class="text-[#1e3a5f] font-bold underline decoration-2 underline-offset-8 transition-colors">Kontak</a>
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
            <a href="products.php" class="hover:text-[#1e3a5f] transition-colors py-1">Produk</a>
            <a href="industries.php" class="hover:text-[#1e3a5f] transition-colors py-1">Sektor Industri</a>
            <a href="contacts.php" class="text-[#1e3a5f] font-bold underline decoration-2 underline-offset-8 py-1">Kontak</a>
        </div>
    </div>
</nav>


<main class="flex-grow max-w-6xl mx-auto px-6 py-10 w-full">
    <h1 class="text-4xl font-bold text-[#1e3a5f] mb-3">Pusat Kontak &amp; Dukungan</h1>
    <p class="text-gray-600 max-w-lg mb-10 leading-relaxed">
        Hubungi tim dukungan teknis dan pengadaan kami. Kami menyediakan bantuan untuk kebutuhan pasok suku cadang
        dan konsultasi pengadaan komponen industri Anda.
    </p>

    <div class="flex flex-col lg:flex-row gap-6">

        <div class="flex-1 border border-gray-200 rounded-xl p-8">
            <h2 class="text-xl font-bold text-[#1e3a5f] mb-6">Formulir Pertanyaan</h2>
            <form action="https://formsubmit.co/ajax/ekandoko@student.ciputra.ac.id" method="POST"
                  onsubmit="event.preventDefault(); fetch(this.action,{method:'POST',body:new FormData(this),headers:{'Accept':'application/json'}}).then(()=>{alert('Pesan Anda telah terkirim! Terima kasih.'); this.reset();});">
                <input type="hidden" name="_subject" value="Pertanyaan Baru dari IndustrialHub">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700">Nama Lengkap <span
                                    class="text-red-500">*</span></label>
                        <input type="text" name="Nama Lengkap" required
                               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]/30 focus:border-[#1e3a5f]"/>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700">Perusahaan <span
                                    class="text-red-500">*</span></label>
                        <input type="text" name="Perusahaan" required
                               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]/30 focus:border-[#1e3a5f]"/>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700">Email Bisnis <span
                                    class="text-red-500">*</span></label>
                        <input type="email" name="Email" required
                               class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]/30 focus:border-[#1e3a5f]"/>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700">Jenis Pertanyaan</label>
                        <select name="Jenis Pertanyaan"
                                class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]/30 focus:border-[#1e3a5f] bg-white appearance-none">
                            <option>Dukungan Teknis</option>
                            <option>Informasi Produk</option>
                            <option>Pengadaan</option>
                            <option>Umum</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Detail Pesan <span
                                    class="text-red-500">*</span></label>
                        <textarea name="Detail Pesan" rows="6" required
                                  class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]/30 focus:border-[#1e3a5f] resize-none"></textarea>
                    </div>
                </div>
                <button type="submit"
                        class="mt-6 bg-[#1e3a5f] text-white text-sm font-semibold px-6 py-2.5 rounded-md hover:bg-[#162d4a] transition">
                    Kirim Pertanyaan
                </button>
            </form>
        </div>


        <div class="lg:w-96 flex flex-col gap-6">
            <div class="border border-gray-200 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[#1e3a5f]">support_agent</span>
                    <h3 class="text-lg font-bold text-[#1e3a5f]">Dukungan Teknis &amp; Pengadaan</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    Untuk bantuan terkait sistem pasok suku cadang, pemesanan, atau konsultasi teknis, silakan hubungi
                    tim kami.
                </p>
                <div class="border border-gray-200 rounded-lg px-4 py-3 flex items-center gap-3 mb-3">
                    <span class="material-symbols-outlined text-gray-400">call</span>
                    <div>
                        <p class="text-xs text-gray-500">Telepon</p>
                        <p class="text-sm font-semibold text-[#1e7a8f] font-mono">+62 838-3317-5104</p>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg px-4 py-3 flex items-center gap-3">
                    <span class="material-symbols-outlined text-gray-400">mail</span>
                    <div>
                        <p class="text-xs text-gray-500">Email Dukungan</p>
                        <a href="mailto:support@industrialhub.com" class="text-sm font-medium text-[#1e7a8f]">craharjo@student.ciputra.ac.id</a>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg px-4 py-3 flex items-center gap-3">
                    <span class="material-symbols-outlined text-gray-400">location_on</span>
                    <div>
                        <p class="text-xs text-gray-500">Kantor Pusat</p>
                        <a href="mailto:support@industrialhub.com" class="text-sm font-medium text-[#1e7a8f]">Universitas
                            Ciputra, Surabaya, Jawa Timur</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<footer class="bg-[#1a1a2e] dark:bg-slate-950 text-white mt-auto py-6 px-6 border-t border-slate-800/40 dark:border-slate-800">
    <div class="max-w-6xl mx-auto text-center md:text-left">
        <p class="font-bold text-lg text-slate-100 dark:text-white">IndustrialHub</p>
        <p class="text-gray-400 dark:text-slate-400 text-sm mt-1">Sistem Pasok Suku Cadang Industri Terpercaya.</p>
        <p class="text-sm text-gray-500 dark:text-slate-500 mt-4">© 2026 IndustrialHub. Hak Cipta Dilindungi.</p>
    </div>
</footer>

<button id="backToTop" aria-label="Back to top">
    <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>