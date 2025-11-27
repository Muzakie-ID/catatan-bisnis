# ⚡ QUICK START GUIDE (5 Menit)

## 🎯 Tujuan
Mulai menggunakan aplikasi Manajemen Stok HP dalam 5 menit

---

## STEP 1️⃣: SIAPKAN DATABASE (2 menit)

### Buka phpMyAdmin
```
http://localhost/phpmyadmin
```

### Import Database
1. Klik tab **"Import"**
2. Buka file: `database/database.sql`
3. Klik **"Execute"** / **"Go"**
4. ✅ Database siap!

**Alternatif Command Line:**
```bash
mysql -u root -p stok_hp < "D:\aaa project\bisnis\database\database.sql"
```

---

## STEP 2️⃣: CONFIGURE (1 menit)

### Edit File: `backend/config.php`

Pastikan settings MySQL benar:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Username MySQL
define('DB_PASS', '');            // Password MySQL (kosong jika tidak ada)
define('DB_NAME', 'stok_hp');
```

---

## STEP 3️⃣: START SERVER (1 menit)

### Option A: XAMPP/WAMP
1. Start Apache & MySQL
2. Copy folder `bisnis` ke `htdocs` atau `www`
3. Buka: `http://localhost/bisnis`

### Option B: PHP Built-in Server
```bash
cd D:\aaa project\bisnis
php -S localhost:8000
```
Buka: `http://localhost:8000`

---

## STEP 4️⃣: DAFTAR & LOGIN (1 menit)

### Register Akun
1. Klik **"Daftar sekarang"**
2. Isi form:
   - Username: `username_anda`
   - Email: `email@example.com`
   - Password: `password123`
3. Klik **"Daftar"**

### Login
1. Klik **"Masuk"** atau langsung di halaman login
2. Isi:
   - Username/Email
   - Password
3. Klik **"Masuk"**
4. ✅ Selamat di Dashboard!

---

## ✅ SELESAI!

Aplikasi Anda sudah siap pakai! 🎉

---

## 📱 MULAI MENGGUNAKAN

### 1. Dashboard
- Lihat ringkasan statistik
- Total stok, modal, penjualan, keuntungan

### 2. Tambah Stok
- Menu → Stok
- Isi form HP baru
- Contoh: Sony Xperia 5 II, Harga Modal Rp 3.500.000

### 3. Input Transaksi
- Menu → Transaksi
- Tab "Pembelian": Catat modal beli HP
- Tab "Penjualan": Catat penjualan (keuntungan auto-hitung)
- Tab "Operasional": Catat biaya lainnya

---

## 🆘 QUICK TROUBLESHOOTING

| Error | Solusi |
|-------|--------|
| "Database error" | Cek konfigurasi di `backend/config.php` |
| "File not found" | Pastikan struktur folder sesuai |
| "Session error" | Refresh halaman atau logout-login |
| "CSS tidak muncul" | Clear browser cache (Ctrl+Shift+R) |

---

## 📞 HELP

**Referensi Lengkap:**
- 📖 README.md - Dokumentasi lengkap
- 🔧 INSTALL.md - Setup detail
- 📚 FITUR.md - Dokumentasi fitur

---

**Selamat menggunakan! Happy Selling! 🚀**
