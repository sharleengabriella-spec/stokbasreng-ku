<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Mutasi Stok - Basreng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
    <header class="bg-orange-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wide">📜 Riwayat Mutasi Stok Basreng</h1>
            <a href="index.php" class="text-sm bg-orange-700 hover:bg-orange-800 px-3 py-1 rounded-full text-white transition">&larr; Kembali ke Dashboard</a>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">Catatan Aktivitas Barang Masuk & Keluar</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 uppercase text-xs tracking-wider">
                            <th class="p-4">Waktu</th>
                            <th class="p-4">Nama Produk</th>
                            <th class="p-4 text-center">Tipe</th>
                            <th class="p-4 text-center">Jumlah</th>
                            <th class="p-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php
                        $query = "SELECT stock_logs.*, products.nama_produk FROM stock_logs JOIN products ON stock_logs.product_id = products.id ORDER BY stock_logs.id DESC";
                        $result = mysqli_query($conn, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $is_masuk = $row['tipe_transaksi'] == 'MASUK';
                                $badge = $is_masuk ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700';
                                $sign = $is_masuk ? '+' : '-';
                                echo "<tr class='hover:bg-slate-50'>";
                                echo "<td class='p-4 text-slate-500 text-xs'>{$row['created_at']}</td>";
                                echo "<td class='p-4 font-medium text-slate-800'>{$row['nama_produk']}</td>";
                                echo "<td class='p-4 text-center'><span class='px-2.5 py-1 rounded-full text-xs font-bold {$badge}'>{$row['tipe_transaksi']}</span></td>";
                                echo "<td class='p-4 text-center font-bold text-slate-700'>{$sign}{$row['jumlah']}</td>";
                                echo "<td class='p-4 text-slate-600 text-xs'>{$row['keterangan']}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='p-6 text-center text-slate-400'>Belum ada riwayat transaksi tercatat.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>