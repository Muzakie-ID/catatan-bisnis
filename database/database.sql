-- Database untuk Manajemen Stok HP
CREATE DATABASE IF NOT EXISTS stok_hp;
USE stok_hp;

-- Tabel Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Stok HP
CREATE TABLE stok (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    nama_hp VARCHAR(100) NOT NULL,
    merk VARCHAR(50),
    tipe_hp VARCHAR(50),
    harga_modal DECIMAL(12, 2) NOT NULL,
    kondisi VARCHAR(50) NOT NULL COMMENT 'seperti: baru, bekas, layar rusak, dll',
    keterangan TEXT,
    jumlah_stok INT DEFAULT 1,
    tanggal_beli DATE NOT NULL,
    status ENUM('tersedia', 'terjual', 'rusak') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Transaksi (Modal, Penjualan, Operasional)
CREATE TABLE transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    stok_id INT,
    tipe_transaksi ENUM('beli', 'jual', 'operasional') NOT NULL,
    nominal DECIMAL(12, 2) NOT NULL,
    keterangan TEXT,
    tanggal_transaksi DATE NOT NULL,
    waktu_transaksi TIME,
    harga_jual DECIMAL(12, 2) COMMENT 'Khusus untuk transaksi jual',
    keuntungan DECIMAL(12, 2) COMMENT 'Otomatis hitung: harga_jual - modal',
    margin_persen DECIMAL(5, 2) COMMENT 'Otomatis hitung: (keuntungan / modal) * 100',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (stok_id) REFERENCES stok(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_tipe (tipe_transaksi),
    INDEX idx_tanggal (tanggal_transaksi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Session
CREATE TABLE sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index tambahan untuk performa
ALTER TABLE transaksi ADD GENERATED ALWAYS AS (harga_jual - nominal) STORED COLUMN keuntungan_auto;
