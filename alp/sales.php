<?php
session_start();
$conn = new mysqli("localhost", "root", "", "industrialhub");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'sales';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

function build_filter($date_col) {
    global $start_date, $end_date;
    $where = "1=1";
    if ($start_date) $where .= " AND $date_col >= '$start_date'";
    if ($end_date) $where .= " AND $date_col <= '$end_date'";
    return $where;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Purchase - IndustrialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

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
            <div class="relative group">
                <button class="flex items-center gap-2 border border-gray-300 rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 bg-white group-hover:bg-gray-50 transition cursor-default">
                    <span class="material-symbols-outlined text-[20px] text-[#1e3a5f]" style="font-variation-settings:'FILL' 1;">account_circle</span>
                    <span class="hidden md:block">Admin</span>
                    <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:rotate-180 transition-transform duration-200">expand_more</span>
                </button>
                <div class="absolute right-0 top-full pt-2 w-64 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-5 flex items-center gap-3">
                            <span class="material-symbols-outlined text-[40px] text-[#1e3a5f] bg-blue-50 p-2 rounded-full" style="font-variation-settings:'FILL' 1;">account_circle</span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">admin</p>
                                <p class="text-xs text-gray-500 truncate">admin@gmail.com</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-200"></div>
                        <a href="logout.php" class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">logout</span> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="flex flex-grow relative w-full">
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
            <a href="adminmenu.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm"><span class="material-symbols-outlined">dashboard</span> Dashboard</a>
            <a href="inventory.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm"><span class="material-symbols-outlined">inventory_2</span> Manajemen Inventaris</a>
            <a href="sales.php" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-[#1e3a5f] rounded-lg font-medium text-sm"><span class="material-symbols-outlined">receipt_long</span> Transaksi Penjualan</a>
            <a href="admin_users.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm"><span class="material-symbols-outlined">group</span> Kelola Pengguna</a>
        </nav>
    </aside>

    <main class="flex-grow p-4 md:p-8 w-full overflow-hidden">
        <h1 class="text-2xl font-bold text-[#1e3a5f] mb-6">Transaksi Penjualan &amp; Pembelian</h1>

        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-4 mb-6 bg-white p-4 rounded-lg border shadow-sm">
            <input type="hidden" name="tab" value="<?= $tab ?>">
            <input type="text" name="search" placeholder="Cari ID..." value="<?= $search ?>" class="border rounded px-3 py-2 text-sm w-full sm:w-auto flex-grow">
            <input type="date" name="start_date" value="<?= $start_date ?>" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
            <input type="date" name="end_date" value="<?= $end_date ?>" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
            <button type="submit" class="bg-[#1e3a5f] text-white px-4 py-2 rounded text-sm w-full sm:w-auto">Terapkan Filter</button>
        </form>

        <div class="flex gap-4 border-b mb-6 overflow-x-auto">
            <a href="?tab=sales" class="pb-2 text-sm font-bold whitespace-nowrap <?= $tab == 'sales' ? 'border-b-2 border-[#1e3a5f] text-[#1e3a5f]' : 'text-slate-500' ?>">Pesanan Penjualan (Sales Orders)</a>
            <a href="?tab=purchase" class="pb-2 text-sm font-bold whitespace-nowrap <?= $tab == 'purchase' ? 'border-b-2 border-[#1e3a5f] text-[#1e3a5f]' : 'text-slate-500' ?>">Pesanan Pembelian (Purchase Orders)</a>
        </div>

        <div class="w-full">
            <?php
            // Fetch data ke array untuk tabel dan card
            $data = [];
            if ($tab == 'sales') {
                $where = build_filter('so.order_date');
                if ($search) $where .= " AND so.salesorders_id = '$search'";
                $sql = "SELECT so.*, u.username FROM sales_orders so JOIN users u ON so.user_id = u.user_id WHERE $where ORDER BY so.order_date DESC";
            } else {
                $where = build_filter('po.order_date');
                if ($search) $where .= " AND po.purchaseorders_id = '$search'";
                $sql = "SELECT po.*, s.name as supplier_name FROM purchase_orders po JOIN suppliers s ON po.suppliers_id = s.suppliers_id WHERE $where ORDER BY po.order_date DESC";
            }
            $res = $conn->query($sql);
            while ($row = $res->fetch_assoc()) $data[] = $row;
            ?>

            <div class="hidden md:block bg-white border rounded-lg shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="p-4 text-left">ID</th>
                        <th class="p-4 text-left"><?= $tab == 'sales' ? 'Pelanggan' : 'Pemasok' ?></th>
                        <th class="p-4 text-left">Tanggal</th>
                        <th class="p-4 text-left">Detail</th>
                        <th class="p-4 text-right">Total/Catatan</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    <?php foreach ($data as $row): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-bold text-[#1e3a5f]">#<?= $tab == 'sales' ? 'SO-'.$row['salesorders_id'] : 'PO-'.$row['purchaseorders_id'] ?></td>
                            <td class="p-4"><?= htmlspecialchars($tab == 'sales' ? $row['username'] : $row['supplier_name']) ?></td>
                            <td class="p-4"><?= $row['order_date'] ?></td>
                            <td class="p-4 text-xs">
                                <?php
                                if ($tab == 'sales') {
                                    $details = $conn->query("SELECT sod.*, sp.name FROM sales_order_details sod JOIN spare_parts sp ON sod.spareparts_id = sp.spareparts_id WHERE sod.salesorders_id = {$row['salesorders_id']}");
                                    while ($d = $details->fetch_assoc()) echo "• {$d['name']} <span class='text-gray-400'>({$d['qty']} x Rp" . number_format($d['selling_price'], 0, ',', '.') . ")</span><br>";
                                } else {
                                    $details = $conn->query("SELECT od.*, sp.name FROM order_details od JOIN spare_parts sp ON od.spareparts_id = sp.spareparts_id WHERE od.purchaseorders_id = {$row['purchaseorders_id']}");
                                    while ($d = $details->fetch_assoc()) echo "• {$d['name']} <span class='text-gray-400'>({$d['qty_ordered']} Ord / {$d['qty_received']} Recv)</span><br>";
                                }
                                ?>
                            </td>
                            <td class="p-4 text-right font-bold text-green-600"><?= $tab == 'sales' ? 'Rp '.number_format($row['total_amount'], 0, ',', '.') : '<span class="text-xs text-gray-500 font-normal">'.htmlspecialchars($row['notes'] ?? '-').'</span>' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                <?php foreach ($data as $row): ?>
                    <div class="bg-white p-4 rounded-lg border shadow-sm">
                        <div class="flex justify-between mb-2">
                            <span class="font-bold text-[#1e3a5f]">#<?= $tab == 'sales' ? 'SO-'.$row['salesorders_id'] : 'PO-'.$row['purchaseorders_id'] ?></span>
                            <span class="text-xs text-gray-400"><?= $row['order_date'] ?></span>
                        </div>
                        <p class="text-sm font-semibold mb-2"><?= htmlspecialchars($tab == 'sales' ? $row['username'] : $row['supplier_name']) ?></p>
                        <div class="border-t pt-2 mt-2 text-xs text-gray-600">
                            <?php
                            if ($tab == 'sales') {
                                $details = $conn->query("SELECT sod.*, sp.name FROM sales_order_details sod JOIN spare_parts sp ON sod.spareparts_id = sp.spareparts_id WHERE sod.salesorders_id = {$row['salesorders_id']}");
                                while ($d = $details->fetch_assoc()) echo "<div>• {$d['name']} ({$d['qty']} x)</div>";
                                echo "<div class='mt-2 font-bold text-green-600'>Total: Rp " . number_format($row['total_amount'], 0, ',', '.') . "</div>";
                            } else {
                                $details = $conn->query("SELECT od.*, sp.name FROM order_details od JOIN spare_parts sp ON od.spareparts_id = sp.spareparts_id WHERE od.purchaseorders_id = {$row['purchaseorders_id']}");
                                while ($d = $details->fetch_assoc()) echo "<div>• {$d['name']} ({$d['qty_ordered']} Ord)</div>";
                                echo "<div class='mt-2 text-xs text-gray-500 italic'>Catatan: ".htmlspecialchars($row['notes'] ?? '-')."</div>";
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

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
        <p class="text-sm text-gray-500 whitespace-nowrap">© 2026 IndustrialHub. Hak Cipta Dilindungi.</p>
    </div>
</footer>

<button id="backToTop" aria-label="Back to top" class="fixed bottom-5 right-5 bg-[#1e3a5f] text-white p-2 rounded-full shadow-lg hidden">
    <span class="material-symbols-outlined">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>