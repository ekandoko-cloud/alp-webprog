<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$username  = $logged_in ? $_SESSION['username'] : '';
$email     = $logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : '') : '';

$host = "localhost";
$user = "root";
$password = "";
$database = "industrialhub";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== 2) {
    die("Akses ditolak!");
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_users.php");
    exit;
}


$query = mysqli_query($conn, "SELECT user_id, username, email, role_id FROM users ORDER BY user_id ASC");
$users = [];
while ($row = mysqli_fetch_assoc($query)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Users - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

<nav class="border-b border-gray-200 bg-white h-14 flex items-center shrink-0 z-50">
    <div class="max-w-[1400px] mx-auto px-6 flex items-center justify-between w-full">
        <div class="flex items-center gap-3">
            <button id="sidebarToggle" class="md:hidden p-1 text-gray-600 hover:text-[#1e3a5f] transition-colors" aria-label="Toggle Sidebar">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>
            <a href="index.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>
        </div>
        <div class="flex items-center gap-3">
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
                <a href="login.php" class="border border-gray-300 text-sm font-medium px-4 py-1.5 rounded-md text-gray-700 hover:bg-gray-50 transition">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="flex flex-grow relative">
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/40 z-40 md:hidden"></div>
    <aside id="adminSidebar" class="w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 min-h-full hidden md:flex fixed md:relative z-50 md:z-auto inset-y-0 left-0">
        <div class="p-6 flex items-center gap-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-full bg-slate-300 overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=Warehouse+Admin" alt="Admin">
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">Warehouse Admin</p>
            </div>
        </div>
        <nav class="flex-grow p-4 space-y-1">
            <a href="adminmenu.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">dashboard</span> Dashboard
            </a>
            <a href="inventory.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">inventory_2</span> Manajemen Inventaris
            </a>
            <a href="sales.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">receipt_long</span> Transaksi Penjualan
            </a>
            <a href="admin_users.php" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-[#1e3a5f] rounded-lg font-medium text-sm">
                <span class="material-symbols-outlined">group</span> Kelola Pengguna
            </a>
        </nav>
    </aside>

    <main class="flex-grow p-4 md:p-8 w-full overflow-hidden">
        <div class="bg-white border border-gray-200 rounded-lg p-4 md:p-8 shadow-sm relative w-full">
            <div class="absolute top-0 left-0 w-full h-1 bg-[#00346f]"></div>

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900">Panel Admin - Kelola Pengguna</h1>
                    <p class="text-xs md:text-sm text-gray-500">Manajemen data pengguna sistem pasok suku cadang</p>
                </div>
            </div>

            <div class="hidden md:block w-full overflow-x-auto">
                <table class="w-full border border-gray-300 text-left">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-3 text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="border p-3 text-xs font-semibold text-gray-600 uppercase">Username</th>
                        <th class="border p-3 text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="border p-3 text-xs font-semibold text-gray-600 uppercase">Role</th>
                        <th class="border p-3 text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $row) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border p-3 text-center text-sm"><?= $row['user_id']; ?></td>
                            <td class="border p-3 text-sm"><?= htmlspecialchars($row['username']); ?></td>
                            <td class="border p-3 text-sm"><?= htmlspecialchars($row['email']); ?></td>
                            <td class="border p-3 text-center text-sm">
                                <?php if ((int)$row['role_id'] === 2): ?>
                                    <span class="text-blue-600 font-bold">Admin</span>
                                <?php else: ?>
                                    <span class="text-green-600">User</span>
                                <?php endif; ?>
                            </td>
                            <td class="border p-3 text-center">
                                <?php if ((int)$row['user_id'] !== (int)$_SESSION['user_id']) : ?>
                                    <a href="admin_users.php?delete=<?= $row['user_id']; ?>"
                                       onclick="return confirm('Yakin ingin menghapus pengguna ini?')"
                                       class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">
                                        Delete
                                    </a>
                                <?php else : ?>
                                    <span class="text-gray-400 text-sm">Aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                <?php foreach ($users as $row) : ?>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-gray-400">ID: <?= $row['user_id']; ?></span>
                            <span class="text-xs font-semibold px-2 py-1 rounded <?= ((int)$row['role_id'] === 2 ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') ?>">
                            <?= ((int)$row['role_id'] === 2 ? 'Admin' : 'User') ?>
                        </span>
                        </div>
                        <div class="mb-3">
                            <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($row['username']); ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($row['email']); ?></p>
                        </div>
                        <div class="border-t pt-3 mt-2">
                            <?php if ((int)$row['user_id'] !== (int)$_SESSION['user_id']) : ?>
                                <a href="admin_users.php?delete=<?= $row['user_id']; ?>"
                                   onclick="return confirm('Yakin ingin menghapus pengguna ini?')"
                                   class="block w-full text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm font-semibold transition-colors">
                                    Delete
                                </a>
                            <?php else : ?>
                                <button disabled class="w-full text-center bg-gray-200 text-gray-400 py-2 rounded text-sm cursor-not-allowed">
                                    Pengguna Saat Ini
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>
</div>

<footer class="bg-[#1a1a2e] dark:bg-slate-950 text-white mt-auto py-6 px-6 border-t border-slate-800/40 dark:border-slate-800">
    <div class="max-w-6xl mx-auto text-center md:text-left">
        <p class="font-bold text-lg text-slate-100 dark:text-white">IndustrialHub</p>
        <p class="text-gray-400 dark:text-slate-400 text-sm mt-1">Sistem Pasok Suku Cadang Industri Terpercaya.</p>
        <p class="text-sm text-gray-500 dark:text-slate-500 mt-4">© 2026 IndustrialHub. Hak Cipta Dilindungi.</p>
    </div>
</footer>

<button id="backToTop" aria-label="Back to top" class="fixed bottom-6 right-6 bg-[#1e3a5f] text-white p-2 rounded-full shadow-lg hover:bg-blue-900 transition-opacity">
    <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>