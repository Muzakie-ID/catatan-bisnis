# 🚀 PANDUAN INSTALASI & SETUP LENGKAP

## 📌 Prasyarat Sistem

- **PHP** 7.4 atau lebih tinggi
- **MySQL** 5.7 atau lebih tinggi  
- **Web Server** (Apache/Nginx) atau PHP Built-in Server
- **Browser** Modern (Chrome, Firefox, Safari, Edge)

## 🔧 Step-by-Step Installation

### STEP 1: Setup Database MySQL

#### Option A: Menggunakan phpMyAdmin
1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Login dengan akun MySQL Anda
3. Klik menu **"Import"** atau **"SQL"**
4. Copy-paste isi file `database/database.sql` ke text area
5. Klik **"Go"** / **"Execute"**

#### Option B: Menggunakan Command Line
```bash
# Windows (PowerShell atau CMD)
mysql -u root -p stok_hp < D:\aaa project\bisnis\database\database.sql

# Linux/Mac
mysql -u root -p stok_hp < /path/to/bisnis/database/database.sql
```

**Output yang seharusnya:**
```
Query OK, 0 rows affected (0.02 sec)
Query OK, 0 rows affected (0.04 sec)
...
```

### STEP 2: Update Konfigurasi Database

Edit file: `backend/config.php`

```php
define('DB_HOST', 'localhost');      // Biasanya localhost
define('DB_USER', 'root');           // Username MySQL
define('DB_PASS', '');               // Password MySQL (kosong jika tidak ada)
define('DB_NAME', 'stok_hp');        // Nama database
define('DB_PORT', 3306);             // Port MySQL (standar 3306)
```

**Contoh jika pakai password:**
```php
define('DB_PASS', 'password_anda_disini');
```

### STEP 3: Setup Web Server

#### Option A: XAMPP/WAMP (Recommended untuk Pemula)

**XAMPP (Windows):**
1. Copy folder `bisnis` ke `C:\xampp\htdocs\`
2. Jalankan XAMPP Control Panel
3. Start **Apache** dan **MySQL**
4. Buka browser: `http://localhost/bisnis`

**WAMP (Windows):**
1. Copy folder `bisnis` ke `C:\wamp\www\`
2. Jalankan WampServer
3. Buka browser: `http://localhost/bisnis`

#### Option B: PHP Built-in Server (Untuk Testing Cepat)

```bash
# Windows PowerShell atau CMD
cd D:\aaa project\bisnis
php -S localhost:8000
```

Akses: `http://localhost:8000`

#### Option C: Apache/Nginx Manual

**Apache (httpd.conf):**
```apache
<VirtualHost *:80>
    ServerName bisnis.local
    DocumentRoot "D:/aaa project/bisnis"
    
    <Directory "D:/aaa project/bisnis">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Tambahkan ke `hosts` file:
```
127.0.0.1  bisnis.local
```

Akses: `http://bisnis.local`

### STEP 4: Verifikasi Instalasi

Buka browser dan test URL berikut:

| URL | Keterangan |
|-----|-----------|
| `http://localhost/bisnis` | Homepage (redirect ke login) |
| `http://localhost/bisnis/pages/login.php` | Halaman login |
| `http://localhost/bisnis/pages/register.php` | Halaman register |

## ✅ Checklist Sebelum Menggunakan

- [ ] MySQL sudah running
- [ ] Database `stok_hp` sudah ter-create
- [ ] File `backend/config.php` sudah dikonfigurasi
- [ ] Web server berjalan dengan baik
- [ ] Akses `http://localhost/bisnis` tanpa error
- [ ] Bisa akses halaman login

## 🎬 First Time Setup

### 1. Register Akun Baru
- Buka `http://localhost/bisnis/pages/register.php`
- Isi form dengan data Anda:
  - Username: `username_anda` (3+ karakter)
  - Email: `email@example.com`
  - Password: `password_anda` (6+ karakter)
- Klik tombol **"Daftar"**

### 2. Login
- Buka `http://localhost/bisnis/pages/login.php`
- Masukkan username/email dan password
- Klik **"Masuk"**

### 3. Mulai Menggunakan
- Anda akan diarahkan ke **Dashboard**
- Jelajahi menu: Dashboard → Transaksi → Stok
- Tambahkan data stok dan transaksi Anda

## 🆘 Troubleshooting

### Error 1: "Fatal error: Uncaught mysqli_sql_exception"
**Penyebab:** Database tidak terhubung

**Solusi:**
```php
// Periksa di backend/config.php:
// 1. MySQL running?
// 2. DB_HOST, DB_USER, DB_PASS benar?
// 3. Database sudah di-create?
```

### Error 2: "Database not found"
**Penyebab:** Database `stok_hp` belum dibuat

**Solusi:**
```bash
# Buat database manual via MySQL CLI
mysql -u root -p
> CREATE DATABASE stok_hp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> USE stok_hp;
> [copy-paste isi database.sql]
```

### Error 3: "Session not working"
**Penyebab:** Folder temporary session tidak ada permission

**Solusi:**
```bash
# Windows - Pastikan tmp folder write-able
# Linux/Mac
chmod 755 /tmp
chmod 777 /var/lib/php/sessions
```

### Error 4: "CSS/JS tidak muncul"
**Penyebab:** Path relatif salah

**Solusi:**
- Pastikan struktur folder sesuai
- Cek browser console (F12) untuk error path
- Pastikan file `assets/css/style.css` dan `assets/js/` ada

### Error 5: "Login berulang kali gagal"
**Penyebab:** Username/password salah atau database kosong

**Solusi:**
- Pastikan sudah register akun terlebih dahulu
- Cek di phpMyAdmin apakah user ada di tabel `users`
- Reset password dengan membuat akun baru

## 📞 Bantuan Lebih Lanjut

### Test Koneksi Database

Buat file `test_db.php`:

```php
<?php
require_once 'backend/config.php';

echo "MySQL Connection Test:<br>";
if ($conn->connect_error) {
    echo "❌ Connection Failed: " . $conn->connect_error;
} else {
    echo "✅ Connection Success!<br>";
    
    // List tables
    $result = $conn->query("SHOW TABLES FROM stok_hp");
    echo "<br>Tables in database:<br>";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
}
?>
```

Akses: `http://localhost/bisnis/test_db.php`

### Check PHP Version

Buat file `test_php.php`:

```php
<?php
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQL Support: " . (extension_loaded('mysqli') ? '✅ Yes' : '❌ No') . "<br>";
echo "Session Support: " . (extension_loaded('session') ? '✅ Yes' : '❌ No') . "<br>";
?>
```

Akses: `http://localhost/bisnis/test_php.php`

## 🎓 Tips & Trik

### 1. Backup Database Reguler
```bash
mysqldump -u root -p stok_hp > backup_stok_hp_$(date +%Y%m%d).sql
```

### 2. Reset Database
```bash
# Hapus semua data
mysql -u root -p -e "DROP DATABASE stok_hp; CREATE DATABASE stok_hp;"
# Re-import
mysql -u root -p stok_hp < database/database.sql
```

### 3. Debugging Query Error
Edit `backend/config.php` dan tambahkan:
```php
// Debug mode (hanya untuk development!)
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### 4. Custom Port (PHP Built-in Server)
```bash
php -S localhost:9000  # Gunakan port 9000 bukan 8000
```

### 5. Upload ke Live Server
```bash
# Jangan lupa ubah:
# 1. DB_HOST, DB_USER, DB_PASS di backend/config.php
# 2. Pastikan MySQL sudah ter-setup di hosting
# 3. Upload semua file via FTP
```

---

**Siap menggunakan? Mari dimulai! 🚀**

Jika ada masalah, silakan periksa kembali langkah-langkah di atas atau modifikasi sesuai dengan environment Anda.
