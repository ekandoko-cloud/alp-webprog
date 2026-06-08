<?php
session_start();

// Variable untuk Navbar agar seragam
$logged_in = isset($_SESSION['user_id']);
$username_sess = $logged_in ? $_SESSION['username'] : '';
$email_sess    = $logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : '') : '';

$host = "localhost";
$user = "root";
$password = "";
$database = "industrialhub";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field wajib diisi.";
    } elseif ($new_password != $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->bind_param("ss", $new_password, $email);

            if ($update->execute()) {
                $message = "Password berhasil diubah.";
            } else {
                $error = "Gagal mengubah password.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password - IndustrialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col">

<!-- ═══ NAVBAR ═══ -->
<nav class="border-b border-gray-200 bg-white sticky top-0 z-50 h-14 flex items-center">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between w-full relative">

        <!-- Kiri -->
        <div class="flex-shrink-0 flex justify-start">
            <a href="landing.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>
        </div>

        <!-- Tengah (Absolute Centered) -->
        <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 items-center gap-8 text-sm text-gray-600 font-medium">
            <a href="products.php" class="hover:text-[#1e3a5f] transition-colors">Produk</a>
            <a href="industries.php" class="hover:text-[#1e3a5f] transition-colors">Sektor Industri</a>
            <a href="contacts.php" class="hover:text-[#1e3a5f] transition-colors">Kontak</a>
        </div>

        <!-- Kanan -->
        <div class="flex-shrink-0 flex items-center justify-end gap-3">
            <button id="darkToggle" class="text-gray-600 hover:text-[#1e3a5f] transition-colors p-1" title="Toggle Dark Mode">
                <span class="material-symbols-outlined text-[20px]">dark_mode</span>
            </button>
            <?php if ($logged_in): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 border border-gray-300 rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 bg-white group-hover:bg-gray-50 transition cursor-default">
                        <span class="material-symbols-outlined text-[20px] text-[#1e3a5f]" style="font-variation-settings:'FILL' 1;">account_circle</span>
                        <span class="hidden md:block"><?= htmlspecialchars($username_sess) ?></span>
                        <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:rotate-180 transition-transform duration-200">expand_more</span>
                    </button>
                    <div class="absolute right-0 top-full pt-2 w-64 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-5 flex items-center gap-3">
                                <span class="material-symbols-outlined text-[40px] text-[#1e3a5f] bg-blue-50 p-2 rounded-full" style="font-variation-settings:'FILL' 1;">account_circle</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($username_sess) ?></p>
                                    <?php if ($email_sess): ?>
                                        <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($email_sess) ?></p>
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
                <a href="login.php" class="border border-gray-300 text-sm font-medium px-4 py-1.5 rounded-md text-gray-700 hover:bg-gray-50 transition">Login</a>
            <?php endif; ?>
        </div>

    </div>
</nav>

<main class="flex-grow flex items-center justify-center px-4 py-12">
    <div class="bg-white border border-gray-200 rounded-lg p-8 md:p-12 w-full max-w-md shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-[#00346f]"></div>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">Reset Password</h1>
            <p class="text-sm text-gray-500">Enter your email and new password.</p>
        </div>

        <?php if ($message): ?>
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1" for="email">Email</label>
                <div class="relative">
                    <input type="email" id="email" name="email" required placeholder="christian@gmail.com"
                           class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#00346f] focus:border-[#00346f]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1" for="new_password">New Password</label>
                <div class="relative">
                    <input type="password" id="new_password" name="new_password" required placeholder="••••••••"
                           class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#00346f] focus:border-[#00346f]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1" for="confirm_password">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••"
                           class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#00346f] focus:border-[#00346f]">
                </div>
            </div>
            <button type="submit"
                    class="w-full bg-[#00346f] text-white text-xs font-semibold uppercase tracking-wider py-3 rounded hover:bg-blue-900 transition-colors">
                Reset Password
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <a href="login.php" class="text-[#00346f] font-semibold hover:underline">Back to Login</a>
        </div>
    </div>
</main>

<!-- ═══ FOOTER ═══ -->
<footer class="bg-[#1a1a2e] text-white mt-auto">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <p class="font-bold text-lg">IndustrialHub</p>
            <p class="text-gray-400 text-sm mt-1">Engineering-grade reliability.</p>
        </div>
        <nav class="flex flex-wrap gap-6 text-sm text-gray-400">
            <a href="#" class="hover:text-white transition">Compliance</a>
            <a href="#" class="hover:text-white transition">Terms of Service</a>
            <a href="#" class="hover:text-white transition">Privacy Policy</a>
            <a href="#" class="hover:text-white transition">Technical Support</a>
        </nav>
        <p class="text-sm text-gray-500 whitespace-nowrap">© 2026 IndustrialHub. All rights reserved.</p>
    </div>
</footer>

<script src="main.js"></script>
</body>
</html>