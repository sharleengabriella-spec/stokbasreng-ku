CREATE DATABASE IF NOT EXISTS db_stok_basreng;
USE db_stok_basreng;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    sku VARCHAR(50) NOT NULL,
    kategori VARCHAR(50) DEFAULT 'Basreng',
    stok INT NOT NULL DEFAULT 0,
    harga_beli DECIMAL(10,2) NOT NULL DEFAULT 0,
    harga_jual DECIMAL(10,2) NOT NULL DEFAULT 0,
    satuan VARCHAR(20) DEFAULT 'Pcs/Bungkus',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE stock_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    tipe_transaksi ENUM('MASUK', 'KELUAR') NOT NULL,
    jumlah INT NOT NULL,
    keterangan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO products (nama_produk, sku, stok, harga_beli, harga_jual, satuan) VALUES
('Basreng Daun Jeruk 100g', 'BSR-DJ-100', 50, 5000, 8000, 'Bungkus'),
('Basreng Daun Jeruk 250g', 'BSR-DJ-250', 30, 12000, 18000, 'Bungkus'),
('Basreng Pedas Gila 100g', 'BSR-PG-100', 40, 5000, 8000, 'Bungkus'),
('Basreng Original Asin', 'BSR-ORG-100', 25, 4500, 7500, 'Bungkus');
