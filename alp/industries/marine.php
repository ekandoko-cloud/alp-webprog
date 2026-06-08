<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$username_sess = $logged_in ? $_SESSION['username'] : '';
$email_sess = $logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : '') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Kelautan dan Galangan Kapal - IndustrialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
          rel="stylesheet"/>
    <link href="../styles.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="border-b border-gray-200 bg-white sticky top-0 z-50 h-14 flex items-center">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between w-full relative">
        <div class="flex-shrink-0 flex justify-start">
            <a href="../landing.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>
        </div>

        <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 items-center gap-8 text-sm text-gray-600 font-medium">
            <a href="../products.php" class="hover:text-[#1e3a5f] transition-colors">Produk</a>
            <a href="../industries.php" class="text-[#1e3a5f] nav-active">Industri</a>
            <a href="../contacts.php" class="hover:text-[#1e3a5f] transition-colors">Kontak</a>
        </div>

        <div class="flex-shrink-0 flex items-center justify-end gap-3">
            <button id="darkToggle" class="text-gray-600 hover:text-[#1e3a5f] transition-colors p-1" title="Toggle Dark Mode">
                <span class="material-symbols-outlined text-[20px]">dark_mode</span>
            </button>

            <?php if ($logged_in): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 border border-gray-300 rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 bg-white group-hover:bg-gray-50 transition cursor-default">
                        <span class="material-symbols-outlined text-[20px] text-[#1e3a5f]"
                              style="font-variation-settings:'FILL' 1;">account_circle</span>
                        <span class="hidden md:block"><?= htmlspecialchars($username_sess) ?></span>
                        <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:rotate-180 transition-transform duration-200">expand_more</span>
                    </button>
                    <div class="absolute right-0 top-full pt-2 w-64 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-5 flex items-center gap-3">
                                <span class="material-symbols-outlined text-[40px] text-[#1e3a5f] bg-blue-50 p-2 rounded-full"
                                      style="font-variation-settings:'FILL' 1;">account_circle</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($username_sess) ?></p>
                                    <?php if ($email_sess): ?><p
                                            class="text-xs text-gray-500 truncate"><?= htmlspecialchars($email_sess) ?></p><?php endif; ?>
                                </div>
                            </div>
                            <div class="border-t border-gray-200"></div>
                            <a href="../logout.php"
                               class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                 <span class="material-symbols-outlined text-[20px]">logout</span> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="../login.php"
                   class="border border-gray-300 text-sm font-medium px-4 py-1.5 rounded-md text-gray-700 hover:bg-gray-50 transition">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="flex-grow w-full max-w-6xl mx-auto px-6 py-12 flex flex-col md:flex-row gap-10">

    <!-- SIDEBAR -->
    <aside class="w-64 flex-shrink-0 hidden md:block">
        <ul class="flex flex-col text-sm font-medium text-gray-600">
            <li><a href="cement.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Semen</a>
            </li>
            <li><a href="marine.php"
                   class="block py-3 px-4 text-[#1e3a5f] font-bold border-l-2 border-[#1e3a5f] bg-blue-50/50 pl-4">Kelautan
                    dan Galangan Kapal</a></li>
            <li><a href="mining.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Pertambangan</a>
            </li>
            <li><a href="oil.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Minyak
                    dan Gas</a></li>
            <li><a href="palm.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Kelapa
                    Sawit</a></li>
            <li><a href="paper.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Pulp
                    dan Kertas</a></li>
            <li><a href="robot.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Otomasi
                    Robotik</a></li>
            <li><a href="semiconductor.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Manufaktur
                    Semikonduktor</a></li>
            <li><a href="steel.php"
                   class="block py-3 px-4 border-l-2 border-transparent hover:text-[#1e3a5f] hover:border-gray-300 transition-colors pl-4">Baja</a>
            </li>
        </ul>
    </aside>

    <!-- CONTENT -->
    <section class="flex-1 max-w-3xl">
        <h1 class="text-3xl md:text-4xl font-semibold text-gray-900 mb-4">Keahlian Industri Kami</h1>
        <p class="text-gray-600 text-sm leading-relaxed mb-8">
            Kami memahami Penggerak Industri, Isu Kritis, dan Aplikasi Fokus. Kami berspesialisasi dalam menyediakan
            Produk dan Layanan sebagai solusi total. Kami berkomitmen untuk menciptakan keunggulan bagi kesuksesan Anda.
        </p>

        <!-- Image -->
        <div class="w-full h-64 md:h-80 bg-gray-200 mb-10 overflow-hidden">
            <img src="../img/marine.jpg"
                 alt="Kelautan dan Galangan Kapal" class="w-full h-full object-cover">
        </div>

        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Operasi Kelautan dan Galangan Kapal yang Lancar</h2>
        <p class="text-gray-600 text-sm leading-relaxed mb-8">
            Operasi bisnis di industri kelautan dan galangan kapal adalah proses yang kritis. Mulai dari pembuatan kapal hingga
            perbaikan mendesak hingga kerusakan tidak terjadwal, IndustrialHub memiliki reputasi keandalan dan
            kualitas yang mapan dan kami dapat menjamin layanan terbaik. IndustrialHub memiliki tim rekayasa dan teknis
            yang berdedikasi yang dapat bekerja sama dengan Anda untuk merencanakan proses dan sistem operasional Anda guna memenuhi kebutuhan penting Anda
            dan mencapai kesuksesan operasional.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mb-4">IndustrialHub memahami dan membangun solusi
            industri</h2>
        <ul class="list-disc list-inside text-gray-600 text-sm leading-relaxed mb-8 space-y-2">
            <li>Kami membantu Anda mengurangi total biaya kepemilikan</li>
            <li>Kami membantu Anda mengurangi waktu henti terencana dan tidak terencana</li>
            <li>Kami membantu Anda mengurangi biaya perawatan</li>
            <li>Kami membantu Anda mengurangi biaya operasi secara keseluruhan</li>
            <li>Kami membantu Anda mengurangi sistem dukungan teknis untuk tenaga kerja Anda</li>
        </ul>

        <p class="text-sm text-gray-600">
            <a href="../contacts.php" class="text-red-600 font-medium hover:underline">Hubungi kami</a> sekarang untuk mengetahui lebih
            lanjut tentang bagaimana kami dapat melayani industri Anda.
        </p>
    </section>

</main>

<!-- FOOTER -->
<footer class="bg-[#1a1a2e] text-white mt-auto">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div><p class="font-bold text-lg">IndustrialHub</p>
            <p class="text-gray-400 text-sm mt-1">Keandalan kelas teknik.</p></div>
        <nav class="flex flex-wrap gap-6 text-sm text-gray-400">
            <a href="#" class="hover:text-white transition">Kepatuhan</a>
            <a href="#" class="hover:text-white transition">Ketentuan Layanan</a>
            <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
            <a href="#" class="hover:text-white transition">Dukungan Teknis</a>
        </nav>
        <p class="text-sm text-gray-500 whitespace-nowrap">© 2026 IndustrialHub. Hak cipta dilindungi.</p>
    </div>
</footer>

<button id="backToTop" aria-label="Back to top">
  <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="../main.js"></script>
</body>
</html>