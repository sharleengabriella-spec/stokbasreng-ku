# Sistem Perhitungan Stok Otomatis Usaha Basreng

Aplikasi web berbasis PHP & MySQL (XAMPP) untuk mengelola inventaris, pencatatan stok masuk (produksi/restock), dan stok keluar (penjualan) produk basreng secara offline dan otomatis.

## Fitur Utama
1. **Dashboard Statistik Ringkas**: Memantau total jenis produk, akumulasi stok fisik, dan peringatan stok menipis.
2. **Pencatatan Transaksi Masuk & Keluar**: Stok otomatis terpotong saat terjadi penjualan dan bertambah saat restock.
3. **Manajemen Produk**: Tambah varian produk baru lengkap dengan SKU, harga beli, dan harga jual.
4. **Riwayat Mutasi (Log Aktivitas)**: Melacak jejak keluar-masuk barang secara transparan.

## Cara Instalasi (XAMPP Offline)
1. Ekstrak folder `stok_basreng` ke dalam direktori `C:\xampp\htdocs\`.
2. Nyalakan **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Buka browser dan akses `http://localhost/phpmyadmin/`.
4. Buat database baru bernama `db_stok_basreng`, lalu import file `database.sql`.
5. Akses aplikasi melalui browser di URL: `http://localhost/stok_basreng/`
