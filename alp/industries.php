<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
$email = $logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : '') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>IndustrialHub - Industries</title>
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
            <a href="industries.php" class="text-[#1e3a5f] font-bold underline decoration-2 underline-offset-8 transition-colors">Sektor Industri</a>

            <a href="contacts.php" class="hover:text-[#1e3a5f] transition-colors">Kontak</a>
        </div>

        <div class="flex items-center gap-2">
            <button id="darkToggle" class="text-gray-600 hover:text-[#1e3a5f] transition-colors p-1" title="Toggle Dark Mode">
                <span class="material-symbols-outlined text-[20px]">dark_mode</span>
            </button>
            <?php if ($logged_in): ?>
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
                                    <?php if ($email): ?><p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($email) ?></p><?php endif; ?>
                                </div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <a href="logout.php" class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">logout</span> Logout
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="border border-gray-300 text-sm font-medium px-4 py-1.5 rounded-md text-gray-700 hover:bg-gray-50 transition">Masuk</a>
            <?php endif; ?>
            <a href="cartmenu.php" class="bg-[#1e3a5f] text-white text-sm font-medium px-3 py-1.5 rounded-md flex items-center gap-1 hover:bg-[#162d4a] transition">
                <span class="material-symbols-outlined text-[18px]">shopping_cart</span><span class="hidden sm:inline">Keranjang</span>
            </a>
            <button id="hamburger" class="md:hidden p-1 text-gray-600 hover:text-[#1e3a5f] transition-colors" aria-label="Menu">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>
        </div>
    </div>

    <div id="mobileMenu" class="hidden md:hidden border-t border-gray-200 bg-white">
        <div class="px-6 py-4 flex flex-col gap-3 text-sm text-gray-600 font-medium">
            <a href="products.php" class="hover:text-[#1e3a5f] transition-colors py-1">Produk</a>
            <a href="industries.php" class="text-[#1e3a5f] font-bold underline decoration-2 underline-offset-8 py-1">Sektor Industri</a>
            <a href="contacts.php" class="hover:text-[#1e3a5f] transition-colors py-1">Kontak</a>
        </div>
    </div>
</nav>

<main class="flex-grow">

    <section class="max-w-6xl mx-auto px-6 py-16 flex flex-col lg:flex-row items-center gap-12">
        <div class="flex-1">
            <span class="text-xs font-bold text-[#1e3a5f] uppercase tracking-widest mb-4 block">Dukungan Teknis Global</span>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">Suku Cadang untuk
                Setiap Sektor Industri</h1>
            <p class="text-gray-600 text-base mb-8 leading-relaxed">
                Menyediakan suku cadang dan komponen mesin berkualitas tinggi untuk berbagai sektor industri berat.
                Sistem pemetaan teknis kami memastikan kecocokan SKU yang tepat untuk lingkungan operasional dengan tekanan tinggi.
            </p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="contacts.php"
                   class="border border-gray-300 hover:border-[#1e3a5f] text-gray-700 hover:text-[#1e3a5f] text-sm font-medium px-6 py-3 rounded-md transition-colors bg-white">Hubungi
                    Tim Teknis</a>
            </div>
        </div>

        <div class="flex-1 w-full relative rounded-lg overflow-hidden shadow-sm h-[350px] lg:h-[400px]">
             <img src="img/industries.webp" alt="Heavy Infrastructure Pipes"
                  class="w-full h-full object-cover grayscale mix-blend-multiply opacity-90 bg-slate-200"/>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a192f]/60 to-transparent"></div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Sektor Industri yang Didukung</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <a href="industries/cement.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">factory</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Semen</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Roller alignment kiln, sistem konveyor pneumatik khusus, dan lapisan bata tahan panas tinggi untuk produksi berkelanjutan.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/marine.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">directions_boat</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Kelautan & Galangan Kapal</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Suku cadang propulsi tugas berat, hidrolik maritim, dan solusi penyegelan tahan garam untuk armada pengiriman dan angkatan laut.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/mining.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">construction</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Pertambangan</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Konveyor tahan abrasi, suku cadang alat berat pindah tanah, dan bagian penguat struktural untuk operasi tambang dalam.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/oil.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">water_drop</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Minyak & Gas</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Katup tekanan tinggi, unit filtrasi, dan material tahan korosi yang direkayasa untuk ekstraksi lepas pantai dan darat ekstrem.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/palm.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">eco</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Kelapa Sawit</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Komponen press minyak sawit khusus, gearbox torsi tinggi, dan segel tahan panas untuk kilang berkapasitas tinggi.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/paper.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">description</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Pulp & Kertas</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Bantalan rol tahan korosi, silinder pengering berkecepatan tinggi, dan bagian pengontrol kelembaban untuk operasi pabrik berat.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/robot.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">smart_toy</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Otomasi Robotik</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Aktuator servo presisi tinggi, end-effektor modular, dan antarmuka sensor canggih untuk jalur manufaktur dan perakitan fleksibel.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/semiconductor.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">memory</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Semikonduktor</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Penanganan fluida kemurnian ultra-tinggi, segel pompa vakum, dan bagian gerak robotik sesuai ruang bersih untuk fabrikasi wafer presisi.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

            <a href="industries/steel.php"
               class="group border border-gray-200 rounded-lg p-6 hover:border-[#1e3a5f] hover:shadow-md transition-all flex flex-col h-full bg-white">
                <div class="w-12 h-12 bg-blue-50 text-[#1e3a5f] rounded flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined">precision_manufacturing</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Baja</h3>
                <p class="text-sm text-gray-600 mb-8 flex-grow leading-relaxed">Bantalan rolling mill beban berat, sensor termal tungku, dan komponen mekanis benturan tinggi yang dirancang untuk beban ekstrem.</p>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm font-medium text-gray-900 group-hover:text-[#1e3a5f] transition-colors">
                    Lihat Komponen <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </a>

        </div>
    </section>

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