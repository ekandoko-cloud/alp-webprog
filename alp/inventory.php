<?php
session_start();
// 1. Session & Auth Check
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
$user_id = $logged_in ? $_SESSION['user_id'] : 1; // Default ke 1 jika sedang testing tanpa login

// Koneksi Database
$conn = new mysqli("localhost", "root", "", "industrialhub");
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'parts';
$view_action = isset($_GET['action']) ? $_GET['action'] : '';
$view_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- LOGIKA DELETE ---
if (isset($_GET['delete_part'])) {
    $id = (int)$_GET['delete_part'];
    $conn->query("DELETE FROM spare_parts WHERE spareparts_id = $id");
    header("Location: inventory.php?tab=parts"); exit;
}
if (isset($_GET['delete_supplier'])) {
    $id = (int)$_GET['delete_supplier'];
    $conn->query("DELETE FROM suppliers WHERE suppliers_id = $id");
    header("Location: inventory.php?tab=suppliers"); exit;
}

// --- LOGIKA POST (TAMBAH, EDIT, RESTOCK, RECEIVE) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_action = $_POST['post_action'] ?? '';

    // 1. Tambah / Edit Spare Part
    if ($post_action == 'save_part') {
        $id = (int)($_POST['spareparts_id'] ?? 0);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $sku = mysqli_real_escape_string($conn, $_POST['sku']);
        $cat_id = (int)$_POST['category_id'];
        $unit = mysqli_real_escape_string($conn, $_POST['unit']);
        $qty = (int)$_POST['stock_qty'];
        $min = (int)$_POST['min_stock'];
        $price = (float)$_POST['selling_price'];
        $description = !empty($_POST['description']) ? "'" . mysqli_real_escape_string($conn, $_POST['description']) . "'" : "NULL";

        $img_update = "";
        $image_url_value = "NULL";
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
            $target_path = 'images/' . time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['image_file']['name']));
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
                $image_url_value = "'" . mysqli_real_escape_string($conn, $target_path) . "'";
                $img_update = ", image_url=$image_url_value";
            }
        }

        if ($id > 0) {
            $conn->query("UPDATE spare_parts SET name='$name', sku='$sku', categories_id=$cat_id, unit='$unit', stock_qty=$qty, min_stock=$min, selling_price=$price, description=$description $img_update WHERE spareparts_id=$id");
        } else {
            $conn->query("INSERT INTO spare_parts (name, sku, categories_id, unit, stock_qty, min_stock, selling_price, image_url, description) VALUES ('$name', '$sku', $cat_id, '$unit', $qty, $min, $price, $image_url_value, $description)");
        }
        header("Location: inventory.php?tab=parts"); exit;
    }

    // 2. Tambah / Edit Supplier
    if ($post_action == 'save_supplier') {
        $id = (int)($_POST['suppliers_id'] ?? 0);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact_person']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        if ($id > 0) {
            $conn->query("UPDATE suppliers SET name='$name', contact_person='$contact', phone='$phone', email='$email', address='$address' WHERE suppliers_id=$id");
        } else {
            $conn->query("INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES ('$name', '$contact', '$phone', '$email', '$address')");
        }
        header("Location: inventory.php?tab=suppliers"); exit;
    }

// 3. Restock (Create Purchase Order)
    if ($post_action == 'restock_part') {
        $sp_id = (int)$_POST['spareparts_id'];
        $supp_id = (int)$_POST['suppliers_id'];
        $qty = (int)$_POST['qty_ordered'];
        $price = (float)$_POST['unit_price'];
        $notes = mysqli_real_escape_string($conn, $_POST['notes']);
        $date = date('Y-m-d');

        // Insert ke purchase_orders (Header)
        $conn->query("INSERT INTO purchase_orders (suppliers_id, order_date, notes) VALUES ($supp_id, '$date', '$notes')");
        $po_id = $conn->insert_id;

        // Insert ke order_details (Detail) - Tanpa kolom 'total_price'
        $conn->query("INSERT INTO order_details (purchaseorders_id, spareparts_id, qty_ordered, qty_received, unit_price) 
                      VALUES ($po_id, $sp_id, $qty, 0, $price)");

        header("Location: inventory.php?tab=inbound"); exit;
    }

    // 4. Receive Goods
    if ($post_action == 'receive_part') {
        $od_id = (int)$_POST['orderdetails_id'];
        $sp_id = (int)$_POST['spareparts_id'];
        $qty_recv = (int)$_POST['qty_received'];

        $conn->query("UPDATE order_details SET qty_received = qty_received + $qty_recv WHERE orderdetails_id = $od_id");
        $conn->query("UPDATE spare_parts SET stock_qty = stock_qty + $qty_recv WHERE spareparts_id = $sp_id");
        header("Location: inventory.php?tab=inbound"); exit;
    }
}

// --- FETCH DATA UMUM ---
$categories = $conn->query("SELECT * FROM categories");
$suppliers_list = $conn->query("SELECT * FROM suppliers");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory - IndustrialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

<nav class="border-b border-gray-200 bg-white h-14 flex items-center shrink-0 z-50">
    <div class="max-w-[1400px] mx-auto px-6 flex items-center justify-between w-full">
        <a href="landing.php" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>
        <div class="flex-1 flex items-center justify-end gap-3">
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


<div class="flex flex-grow">
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 min-h-full">
        <div class="p-6 flex items-center gap-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-full bg-slate-300 overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=Warehouse+Admin" alt="Admin">
            </div>
            <div>
                    <p class="text-sm font-bold text-slate-900">Admin Inventaris</p>
                <p class="text-xs text-slate-500">Panel Manajemen Suku Cadang</p>
            </div>
        </div>
        <nav class="flex-grow p-4 space-y-1">
            <a href="adminmenu.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">dashboard</span> Dashboard
            </a>
            <a href="inventory.php" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-[#1e3a5f] rounded-lg font-medium text-sm">
                <span class="material-symbols-outlined">inventory_2</span> Manajemen Inventaris
            </a>
            <a href="sales.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">receipt_long</span> Transaksi Penjualan
            </a>
            <a href="admin_users.php" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-lg text-sm">
                <span class="material-symbols-outlined">group</span> Kelola Pengguna
            </a>
        </nav>
    </aside>

    <main class="flex-grow p-8">
        <h1 class="text-2xl font-bold text-[#1e3a5f] mb-2">Manajemen Inventaris Suku Cadang</h1>

        <div class="flex space-x-6 border-b border-gray-200 mb-6">
            <a href="?tab=parts" class="pb-3 text-sm font-bold <?= $tab == 'parts' ? 'border-b-2 border-[#1e3a5f] text-[#1e3a5f]' : 'text-gray-500 hover:text-gray-700' ?>">Data Suku Cadang (CRUD)</a>
            <a href="?tab=suppliers" class="pb-3 text-sm font-bold <?= $tab == 'suppliers' ? 'border-b-2 border-[#1e3a5f] text-[#1e3a5f]' : 'text-gray-500 hover:text-gray-700' ?>">Data Pemasok (CRUD)</a>
            <a href="?tab=inbound" class="pb-3 text-sm font-bold <?= $tab == 'inbound' ? 'border-b-2 border-[#1e3a5f] text-[#1e3a5f]' : 'text-gray-500 hover:text-gray-700' ?>">Penerimaan Barang</a>
        </div>

        <?php if ($tab == 'parts'): ?>
            <?php if ($view_action == 'add_part' || $view_action == 'edit_part'):
                $edit_data = null;
                if ($view_action == 'edit_part' && $view_id > 0) {
                    $edit_data = $conn->query("SELECT * FROM spare_parts WHERE spareparts_id = $view_id")->fetch_assoc();
                }
                ?>
                <div class="bg-white p-6 border rounded-lg shadow-sm mb-6 max-w-3xl">
                    <h2 class="font-bold mb-4 text-xl"><?= $edit_data ? 'Edit Data Suku Cadang (Update)' : 'Tambah Suku Cadang Baru (Create)' ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="post_action" value="save_part">
                        <?php if($edit_data): ?><input type="hidden" name="spareparts_id" value="<?= $edit_data['spareparts_id'] ?>"><?php endif; ?>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Suku Cadang</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">SKU (Kode Unik)</label>
                                <input type="text" name="sku" value="<?= htmlspecialchars($edit_data['sku'] ?? '') ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Kategori</label>
                                <select name="category_id" class="w-full border p-2 rounded bg-gray-50" required>
                                    <option value="" disabled <?= !$edit_data ? 'selected' : '' ?>>Pilih Kategori</option>
                                    <?php $categories->data_seek(0); while($c = $categories->fetch_assoc()): ?>
                                        <option value="<?= $c['categories_id'] ?>" <?= ($edit_data && $edit_data['categories_id'] == $c['categories_id']) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Satuan Unit</label>
                                <input type="text" name="unit" value="<?= htmlspecialchars($edit_data['unit'] ?? '') ?>" placeholder="Pcs, Kg, Roll, dll" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Stok Saat Ini</label>
                                <input type="number" name="stock_qty" value="<?= $edit_data['stock_qty'] ?? '' ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Stok Minimum</label>
                                <input type="number" name="min_stock" value="<?= $edit_data['min_stock'] ?? '' ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Harga Jual (Rp)</label>
                                <input type="number" name="selling_price" value="<?= $edit_data['selling_price'] ?? '' ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                        </div>

                        <label class="block text-xs font-bold text-gray-600 mb-1 mt-2">Upload Gambar (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="file" name="image_file" accept="image/*" class="w-full border p-2 mb-3 rounded bg-gray-50 text-sm">

                        <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                        <textarea name="description" class="w-full border p-2 mb-4 rounded bg-gray-50" rows="2"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>

                        <div class="flex gap-3">
                            <button type="submit" class="px-4 py-2 bg-[#1e3a5f] text-white rounded">Simpan Data (Create/Update)</button>
                            <a href="?tab=parts" class="px-4 py-2 border rounded hover:bg-gray-50">Batal</a>
                        </div>
                    </form>
                </div>
            <?php elseif ($view_action == 'restock' && $view_id > 0):
                $part_data = $conn->query("SELECT * FROM spare_parts WHERE spareparts_id = $view_id")->fetch_assoc();
                ?>
                <div class="bg-white p-6 border rounded-lg shadow-sm mb-6 max-w-2xl border-blue-300">
                    <h2 class="font-bold mb-1 text-xl text-blue-800">Restock / Buat Purchase Order (Create PO)</h2>
                    <p class="text-sm text-gray-500 mb-4 font-medium">Item: <?= htmlspecialchars($part_data['name']) ?> (SKU: <?= htmlspecialchars($part_data['sku']) ?>)</p>

                    <form method="POST">
                        <input type="hidden" name="post_action" value="restock_part">
                        <input type="hidden" name="spareparts_id" value="<?= $part_data['spareparts_id'] ?>">

                        <label class="block text-xs font-bold text-gray-600 mb-1">Pilih Pemasok (Supplier)</label>
                        <select name="suppliers_id" class="w-full border p-2 mb-3 rounded bg-gray-50" required>
                            <option value="" disabled selected>-- Pilih Supplier --</option>
                            <?php $suppliers_list->data_seek(0); while($s = $suppliers_list->fetch_assoc()): ?>
                                <option value="<?= $s['suppliers_id'] ?>"><?= $s['name'] ?></option>
                            <?php endwhile; ?>
                        </select>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Qty Dipesan</label>
                                <input type="number" name="qty_ordered" min="1" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Harga Satuan (Rp)</label>
                                <input type="number" name="unit_price" value="<?= $part_data['selling_price'] ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                        </div>

                        <label class="block text-xs font-bold text-gray-600 mb-1">Catatan (Opsional)</label>
                        <textarea name="notes" class="w-full border p-2 mb-5 rounded bg-gray-50" rows="2" placeholder="Contoh: Restock urgent"></textarea>

                        <div class="flex gap-3">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-bold">Buat Purchase Order (Create)</button>
                            <a href="?tab=parts" class="px-4 py-2 border rounded hover:bg-gray-50">Batal</a>
                        </div>
                    </form>
                </div>
            <?php else:
                // TABLE SPARE PARTS (Ditampilkan jika tidak sedang add/edit/restock)
                $parts = $conn->query("SELECT s.*, c.name AS category_name FROM spare_parts s LEFT JOIN categories c ON s.categories_id = c.categories_id");
                ?>
                <div class="flex justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-700">Data Master Suku Cadang (Read - Tabel)</h2>
                    <a href="?tab=parts&action=add_part" class="bg-[#1e3a5f] text-white px-4 py-2 rounded text-sm shadow-sm">+ Tambah Barang (Create)</a>
                </div>
                <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-left border-b">
                        <tr>
                            <th class="p-4">GAMBAR</th>
                            <th class="p-4">SKU & INFO ITEM</th>
                            <th class="p-4">KATEGORI/SATUAN</th>
                            <th class="p-4">STOK / MIN</th>
                            <th class="p-4">HARGA</th>
                            <th class="p-4">AKSI (CRUD)</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <?php while($row = $parts->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-4">
                                    <?php if($row['image_url']): ?>
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" class="w-12 h-12 object-cover rounded border">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-100 border text-gray-400 flex items-center justify-center rounded text-[10px]">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <span class="text-xs text-gray-400 font-mono">SKU: <?= htmlspecialchars($row['sku']) ?></span>
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($row['name']) ?></p>
                                </td>
                                <td class="p-4">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($row['category_name']) ?></span>
                                    <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($row['unit']) ?></p>
                                </td>
                                <td class="p-4">
                                    <span class="font-medium <?= $row['stock_qty'] <= $row['min_stock'] ? 'text-red-600' : 'text-green-600' ?>">
                                        <?= $row['stock_qty'] ?>
                                    </span>
                                    <span class="text-gray-400 text-xs">/ <?= $row['min_stock'] ?></span>
                                </td>
                                <td class="p-4 font-medium text-slate-700">Rp <?= number_format($row['selling_price'], 0, ',', '.') ?></td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <a href="?tab=parts&action=edit_part&id=<?= $row['spareparts_id'] ?>" class="text-yellow-600 hover:text-yellow-800 font-medium text-xs flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">edit</span> Update (Edit)</a>
                                        <a href="?tab=parts&action=restock&id=<?= $row['spareparts_id'] ?>" class="text-blue-600 hover:text-blue-800 font-medium text-xs flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">shopping_cart</span> Restock</a>
                                        <a href="?delete_part=<?= $row['spareparts_id'] ?>" onclick="return confirm('Hapus data suku cadang ini?')" class="text-red-600 hover:text-red-800 font-medium text-xs flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">delete</span> Delete (Hapus)</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($tab == 'suppliers'): ?>
            <?php if ($view_action == 'add_supplier' || $view_action == 'edit_supplier'):
                $supp_data = null;
                if ($view_action == 'edit_supplier' && $view_id > 0) {
                    $supp_data = $conn->query("SELECT * FROM suppliers WHERE suppliers_id = $view_id")->fetch_assoc();
                }
                ?>
                <div class="bg-white p-6 border rounded-lg shadow-sm mb-6 max-w-2xl">
                    <h2 class="font-bold mb-4 text-xl"><?= $supp_data ? 'Edit Data Pemasok (Update)' : 'Tambah Pemasok Baru (Create)' ?></h2>
                    <form method="POST">
                        <input type="hidden" name="post_action" value="save_supplier">
                        <?php if($supp_data): ?><input type="hidden" name="suppliers_id" value="<?= $supp_data['suppliers_id'] ?>"><?php endif; ?>

                        <label class="block text-xs font-bold text-gray-600 mb-1">Nama Pemasok (Supplier)</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($supp_data['name'] ?? '') ?>" class="w-full border p-2 mb-3 rounded bg-gray-50" required>

                        <label class="block text-xs font-bold text-gray-600 mb-1">Kontak Person</label>
                        <input type="text" name="contact_person" value="<?= htmlspecialchars($supp_data['contact_person'] ?? '') ?>" class="w-full border p-2 mb-3 rounded bg-gray-50" required>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Telepon</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($supp_data['phone'] ?? '') ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($supp_data['email'] ?? '') ?>" class="w-full border p-2 rounded bg-gray-50" required>
                            </div>
                        </div>

                        <label class="block text-xs font-bold text-gray-600 mb-1">Alamat</label>
                        <textarea name="address" class="w-full border p-2 mb-5 rounded bg-gray-50" rows="3" required><?= htmlspecialchars($supp_data['address'] ?? '') ?></textarea>

                        <div class="flex gap-3">
                            <button type="submit" class="px-4 py-2 bg-[#1e3a5f] text-white rounded">Simpan Data Pemasok (Create/Update)</button>
                            <a href="?tab=suppliers" class="px-4 py-2 border rounded hover:bg-gray-50">Batal</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="flex justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-700">Data Pemasok (Read - Tabel)</h2>
                    <a href="?tab=suppliers&action=add_supplier" class="bg-[#1e3a5f] text-white px-4 py-2 rounded text-sm shadow-sm">+ Tambah Pemasok (Create)</a>
                </div>
                <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 border-b">
                        <tr>
                            <th class="p-4">NAMA PEMASOK</th>
                            <th class="p-4">KONTAK PERSON</th>
                            <th class="p-4">TELEPON & EMAIL</th>
                            <th class="p-4">ALAMAT</th>
                            <th class="p-4">AKSI (CRUD)</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <?php $suppliers_list->data_seek(0); while($s = $suppliers_list->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($s['name']) ?></td>
                                <td class="p-4"><?= htmlspecialchars($s['contact_person']) ?></td>
                                <td class="p-4">
                                    <p><?= htmlspecialchars($s['phone']) ?></p>
                                    <p class="text-xs text-blue-500"><?= htmlspecialchars($s['email']) ?></p>
                                </td>
                                <td class="p-4 text-xs text-gray-600 max-w-xs truncate" title="<?= htmlspecialchars($s['address']) ?>"><?= htmlspecialchars($s['address']) ?></td>
                                <td class="p-4 flex gap-3">
                                    <a href="?tab=suppliers&action=edit_supplier&id=<?= $s['suppliers_id'] ?>" class="text-yellow-600 hover:underline text-xs">Update (Edit)</a>
                                    <a href="?delete_supplier=<?= $s['suppliers_id'] ?>" onclick="return confirm('Hapus data pemasok ini?')" class="text-red-600 hover:underline text-xs">Delete (Hapus)</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($tab == 'inbound'): ?>
            <?php if ($view_action == 'receive' && $view_id > 0):
                $od_data = $conn->query("
                    SELECT od.*, sp.name as part_name, sp.unit 
                    FROM order_details od 
                    JOIN spare_parts sp ON od.spareparts_id = sp.spareparts_id 
                    WHERE od.orderdetails_id = $view_id
                ")->fetch_assoc();
                $sisa_terima = $od_data['qty_ordered'] - $od_data['qty_received'];
                ?>
                <div class="bg-white p-6 border rounded-lg shadow-sm mb-6 max-w-md border-green-300">
                    <h2 class="font-bold mb-4 text-xl text-green-700">Terima Barang (Update Stok &amp; Status PO)</h2>
                    <form method="POST">
                        <input type="hidden" name="post_action" value="receive_part">
                        <input type="hidden" name="orderdetails_id" value="<?= $od_data['orderdetails_id'] ?>">
                        <input type="hidden" name="spareparts_id" value="<?= $od_data['spareparts_id'] ?>">

                        <p class="text-sm font-bold text-gray-700 mb-1"><?= htmlspecialchars($od_data['part_name']) ?> (PO-<?= $od_data['purchaseorders_id'] ?>)</p>
                        <p class="text-xs text-gray-500 mb-4">Sisa yang belum diterima: <span class="font-bold text-red-600"><?= $sisa_terima ?> <?= htmlspecialchars($od_data['unit']) ?></span></p>

                        <label class="block text-xs font-bold text-gray-600 mb-1">Jumlah Diterima</label>
                        <input type="number" name="qty_received" max="<?= $sisa_terima ?>" value="<?= $sisa_terima ?>" min="1" class="w-full border p-3 mb-6 rounded bg-gray-50 text-lg font-bold" required>

                        <div class="flex gap-3">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded font-bold">Konfirmasi Penerimaan (Update)</button>
                            <a href="?tab=inbound" class="px-4 py-2 border rounded hover:bg-gray-50">Batal</a>
                        </div>
                    </form>
                </div>
            <?php else:
                // TABLE PENDING INBOUND
                $inbounds = $conn->query("
                    SELECT od.*, po.order_date, s.name as supplier_name, sp.name as part_name, sp.sku 
                    FROM order_details od 
                    JOIN purchase_orders po ON od.purchaseorders_id = po.purchaseorders_id 
                    JOIN suppliers s ON po.suppliers_id = s.suppliers_id 
                    JOIN spare_parts sp ON od.spareparts_id = sp.spareparts_id 
                    WHERE od.qty_received < od.qty_ordered
                    ORDER BY po.order_date DESC
                ");
                ?>
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-slate-700">Penerimaan Barang Tertunda (Pending Inbound)</h2>
                    <p class="text-sm text-slate-500">Klik "Terima" ketika barang dari supplier tiba di gudang untuk memperbarui stok.</p>
                </div>
                <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 border-b">
                        <tr>
                            <th class="p-4">TANGGAL & ID PO</th>
                            <th class="p-4">PEMASOK</th>
                            <th class="p-4">ITEM (SKU)</th>
                            <th class="p-4">QTY DIPESAN</th>
                            <th class="p-4">QTY DITERIMA</th>
                            <th class="p-4">AKSI</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <?php if($inbounds && $inbounds->num_rows > 0): ?>
                            <?php while($ib = $inbounds->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-4 text-xs font-mono"><?= date('d M Y', strtotime($ib['order_date'])) ?> <br>#PO-<?= $ib['purchaseorders_id'] ?></td>
                                    <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($ib['supplier_name']) ?></td>
                                    <td class="p-4">
                                        <p class="font-medium"><?= htmlspecialchars($ib['part_name']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($ib['sku']) ?></p>
                                    </td>
                                    <td class="p-4 font-bold text-blue-600"><?= $ib['qty_ordered'] ?></td>
                                    <td class="p-4 font-bold text-green-600"><?= $ib['qty_received'] ?></td>
                                    <td class="p-4">
                                        <a href="?tab=inbound&action=receive&id=<?= $ib['orderdetails_id'] ?>" class="inline-block bg-green-600 text-white px-3 py-1.5 rounded text-xs hover:bg-green-700 font-bold">Terima Barang</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="p-8 text-center text-gray-400">Semua pesanan sudah diterima atau belum ada pesanan tertunda.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

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
        <p class="text-sm text-gray-500 whitespace-nowrap">© 2024 IndustrialHub. Hak Cipta Dilindungi.</p>
    </div>
</footer>

<button id="backToTop" aria-label="Back to top">
  <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>