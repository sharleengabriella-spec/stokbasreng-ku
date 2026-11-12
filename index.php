<?php
include 'koneksi.php';

if (isset($_POST['tambah_produk'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $sku    = mysqli_real_escape_string($conn, $_POST['sku']);
    $stok   = (int)$_POST['stok'];
    $hbeli  = (float)$_POST['harga_beli'];
    $hjual  = (float)$_POST['harga_jual'];
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);

    $query = "INSERT INTO products (nama_produk, sku, stok, harga_beli, harga_jual, satuan) VALUES ('$nama', '$sku', $stok, $hbeli, $hjual, '$satuan')";
    if (mysqli_query($conn, $query)) {
        $pid = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO stock_logs (product_id, tipe_transaksi, jumlah, keterangan) VALUES ($pid, 'MASUK', $stok, 'Stok Awal')");
        header("Location: index.php?pesan=sukses_tambah");
        exit();
    }
}

if (isset($_POST['proses_transaksi'])) {
    $pid    = (int)$_POST['product_id'];
    $tipe   = $_POST['tipe_transaksi'];
    $jumlah = (int)$_POST['jumlah'];
    $ket    = mysqli_real_escape_string($conn, $_POST['keterangan']);

    if ($jumlah > 0) {
        $cek = mysqli_query($conn, "SELECT stok, nama_produk FROM products WHERE id = $pid");
        $data = mysqli_fetch_assoc($cek);
        
        if ($tipe == 'MASUK') {
            $stok_baru = $data['stok'] + $jumlah;
            mysqli_query($conn, "UPDATE products SET stok = $stok_baru WHERE id = $pid");
            mysqli_query($conn, "INSERT INTO stock_logs (product_id, tipe_transaksi, jumlah, keterangan) VALUES ($pid, 'MASUK', $jumlah, '$ket')");
        } else { 
            if ($data['stok'] >= $jumlah) {
                $stok_baru = $data['stok'] - $jumlah;
                mysqli_query($conn, "UPDATE products SET stok = $stok_baru WHERE id = $pid");
                mysqli_query($conn, "INSERT INTO stock_logs (product_id, tipe_transaksi, jumlah, keterangan) VALUES ($pid, 'KELUAR', $jumlah, '$ket')");
            } else {
                $error = "Stok " . $data['nama_produk'] . " tidak mencukupi!";
            }
        }
        if (!isset($error)) {
            header("Location: index.php?pesan=sukses_transaksi");
            exit();
        }
    }
}

$total_produk = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products"));
$total_stok_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stok) as total FROM products"));
$total_stok = $total_stok_q['total'] ?? 0;
$stok_menipis = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products WHERE stok <= 10"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Stok Otomatis Basreng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
    <header class="bg-orange-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wide">🔥 Sistem Stok Otomatis Basreng</h1>
            <div class="text-sm bg-orange-700 px-3 py-1 rounded-full">Mode Offline (XAMPP)</div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-8">
        <?php if (isset($_GET['pesan'])): ?>
            <div class="mb-6 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded shadow-sm">
                Aksi berhasil dijalankan! Data stok telah diperbarui secara otomatis.
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-rose-100 border-l-4 border-rose-500 text-rose-700 rounded shadow-sm">
                <?= $error; ?>
            </div>
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Jenis Produk</p>
                    <h3 class="text-3xl font-bold text-slate-900 mt-1"><?= $total_produk; ?></h3>
                </div>
                <div class="p-3 bg-orange-100 text-orange-600 rounded-lg text-2xl">📦</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Akumulasi Stok Fisik</p>
                    <h3 class="text-3xl font-bold text-slate-900 mt-1"><?= number_format($total_stok); ?> <span class="text-sm font-normal text-slate-500">Pcs</span></h3>
                </div>
                <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg text-2xl">📊</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Stok Menipis (≤ 10)</p>
                    <h3 class="text-3xl font-bold text-rose-600 mt-1"><?= $stok_menipis; ?></h3>
                </div>
                <div class="p-3 bg-rose-100 text-rose-600 rounded-lg text-2xl">⚠️</div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b">📝 Catat Masuk / Keluar Stok</h3>
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Pilih Produk</label>
                            <select name="product_id" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                                <option value="">-- Pilih Produk Basreng --</option>
                                <?php
                                $res_prod = mysqli_query($conn, "SELECT id, nama_produk, stok FROM products");
                                while ($p = mysqli_fetch_assoc($res_prod)) {
                                    echo "<option value='{$p['id']}'>{$p['nama_produk']} (Stok: {$p['stok']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Tipe Transaksi</label>
                            <select name="tipe_transaksi" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                                <option value="MASUK">Stok Masuk (Produksi / Restock)</option>
                                <option value="KELUAR">Stok Keluar (Penjualan / Terjual)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Jumlah (Pcs/Bungkus)</label>
                            <input type="number" name="jumlah" min="1" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Contoh: 15">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Keterangan / Catatan</label>
                            <input type="text" name="keterangan" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Contoh: Terjual via TikTok Shop">
                        </div>
                        <button type="submit" name="proses_transaksi" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 rounded-lg transition shadow-sm">
                            Proses & Update Stok
                        </button>
                    </form>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b">➕ Tambah Varian Basreng</h3>
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Nama Produk / Varian</label>
                            <input type="text" name="nama_produk" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm" placeholder="Contoh: Basreng Keju Pedas">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">SKU / Kode</label>
                                <input type="text" name="sku" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm" placeholder="BSR-KJ">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Stok Awal</label>
                                <input type="number" name="stok" min="0" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm" value="0">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Harga Beli (Rp)</label>
                                <input type="number" name="harga_beli" step="500" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm" placeholder="5000">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Harga Jual (Rp)</label>
                                <input type="number" name="harga_jual" step="500" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm" placeholder="8000">
                            </div>
                        </div>
                        <input type="hidden" name="satuan" value="Bungkus">
                        <button type="submit" name="tambah_produk" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2.5 rounded-lg transition shadow-sm">
                            Simpan Produk Baru
                        </button>
                    </form>
                </div>
            </div>
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-800">📋 Daftar Stok Varian Basreng</h3>
                        <a href="history.php" class="text-sm font-medium text-orange-600 hover:underline">Lihat Riwayat Mutasi &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-100 text-slate-600 uppercase text-xs tracking-wider">
                                    <th class="p-4">SKU / Produk</th>
                                    <th class="p-4">Harga Beli</th>
                                    <th class="p-4">Harga Jual</th>
                                    <th class="p-4 text-center">Sisa Stok</th>
                                    <th class="p-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $badge_class = $row['stok'] <= 10 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700';
                                    $status_text = $row['stok'] <= 10 ? 'Hampir Habis' : 'Aman';
                                    echo "<tr class='hover:bg-slate-50 transition'>";
                                    echo "<td class='p-4'>
                                            <div class='font-semibold text-slate-800'>{$row['nama_produk']}</div>
                                            <div class='text-xs text-slate-400 font-mono'>SKU: {$row['sku']}</div>
                                          </td>";
                                    echo "<td class='p-4 text-slate-600'>Rp " . number_format($row['harga_beli'], 0, ',', '.') . "</td>";
                                    echo "<td class='p-4 text-slate-600 font-medium'>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>";
                                    echo "<td class='p-4 text-center font-bold text-base text-slate-800'>{$row['stok']} <span class='text-xs font-normal text-slate-500'>{$row['satuan']}</span></td>";
                                    echo "<td class='p-4 text-center'><span class='px-2.5 py-1 rounded-full text-xs font-semibold {$badge_class}'>{$status_text}</span></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="text-center py-6 text-xs text-slate-400">
        &copy; 2026 Sistem Stok Otomatis Usaha Basreng. Berjalan Offline via XAMPP.
    </footer>
</body>
</html>