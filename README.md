# 📱 Manajemen Stok HP - Aplikasi Mobile-Friendly

Aplikasi web modern untuk mencatat dan mengelola stok penjualan HP dengan fitur modal, keuntungan, dan tracking kondisi barang. Dibangun dengan Bootstrap responsif dan PHP native untuk penggunaan di smartphone.

## ✨ Fitur Utama

- ✅ **Autentikasi User** - Login/Register dengan validasi lengkap
- ✅ **Session Management** - Session otomatis 1 bulan dengan pengecekan aktivitas
- ✅ **Dashboard Ringkas** - Overview total stok, modal, penjualan, keuntungan, dan ROI
- ✅ **Input Transaksi** - Mencatat modal pembelian, penjualan, dan operasional
- ✅ **Tracking Kondisi** - Catat kondisi HP (baru, bekas, rusak, dll)
- ✅ **Hitung Keuntungan Otomatis** - Margin dan profit langsung terhitung
- ✅ **Manajemen Stok** - Kelola daftar HP yang dijual
- ✅ **Mobile Responsive** - 100% responsif untuk smartphone dan tablet
- ✅ **Modern UI** - Desain clean dengan Bootstrap 5 dan animasi smooth

## 🛠️ Stack Teknologi

| Komponen | Teknologi |
|----------|-----------|
| **Frontend** | HTML5, CSS3, Bootstrap 5, JavaScript Vanilla |
| **Backend** | PHP 7.4+ (Native, tanpa framework) |
| **Database** | MySQL 5.7+ |
| **Server** | Apache / Nginx + PHP |

## 📋 Struktur Proyek

```
bisnis/
├── assets/
│   ├── css/
│   │   └── style.css           # Styling custom + responsive
│   └── js/
├── backend/
│   ├── config.php              # Konfigurasi database
│   ├── session.php             # Manajemen session (1 bulan)
│   ├── auth.php                # Handler login/register
│   └── transaksi_handler.php   # Handler transaksi & stok
├── pages/
│   ├── login.php               # Halaman login
│   ├── register.php            # Halaman register
│   ├── dashboard.php           # Dashboard utama
│   ├── transaksi.php           # Input transaksi
│   └── stok.php                # Manajemen stok
└── database/
    └── database.sql            # Schema database
```

## 📥 Instalasi & Setup

### 1. **Siapkan Database**

```sql
-- Import file database/database.sql ke MySQL Anda
mysql -u root -p stok_hp < database/database.sql
```

Atau copy-paste isi file `database/database.sql` ke phpMyAdmin.

### 2. **Update Konfigurasi Database**

Edit file `backend/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Masukkan password MySQL Anda
define('DB_NAME', 'stok_hp');
```

### 3. **Setup Web Server**

**Menggunakan XAMPP/WAMP:**
- Copy folder `bisnis` ke folder `htdocs` (XAMPP) atau `www` (WAMP)
- Akses: `http://localhost/bisnis`

**Menggunakan PHP Built-in Server:**
```bash
cd d:\aaa project\bisnis
php -S localhost:8000
# Akses: http://localhost:8000
```

**Menggunakan Apache/Nginx:**
- Sesuaikan DocumentRoot ke folder `bisnis`
- Pastikan `mod_rewrite` aktif

## 🚀 Cara Menggunakan

### 1. **Daftar Akun Baru**
- Klik "Daftar sekarang" di halaman login
- Isi form dengan username, email, dan password
- Password minimal 6 karakter

### 2. **Login**
- Gunakan username/email dan password untuk login
- Session akan bertahan 30 hari
- Jika 30 hari tidak aktif, session expire dan harus login ulang

### 3. **Dashboard**
- Lihat ringkasan stok, modal, penjualan, keuntungan
- Akses menu: Dashboard → Transaksi → Stok

### 4. **Input Transaksi**
- **Pembelian**: Catat modal beli HP
- **Penjualan**: Catat penjualan dengan harga jual (keuntungan otomatis terhitung)
- **Operasional**: Catat pengeluaran operasional lainnya

### 5. **Manajemen Stok**
- Tambah stok HP baru dengan kondisi
- Edit/hapus stok sesuai kebutuhan
- Lihat daftar semua HP yang tersedia

## 🔒 Keamanan

### Fitur Keamanan yang Diimplementasikan:

1. **Password Hashing** - Menggunakan `password_hash()` dengan BCRYPT
2. **Session Security** - Session ID unique dan time-based expiry
3. **SQL Injection Protection** - Prepared statements untuk semua query
4. **XSS Prevention** - `htmlspecialchars()` untuk semua output
5. **Automatic Session Timeout** - 30 hari inaktif = logout otomatis

## 📱 Responsive Design

Aplikasi 100% responsif untuk:
- 📱 **Smartphone** (≤576px)
- 📱 **Tablet** (576px - 992px)
- 🖥️ **Desktop** (≥992px)

Semua menu, form, tabel, dan button responsive dan mudah digunakan di mobile.

## 🎨 Desain & UX

- **Modern Interface** - Gradient colors dan smooth animations
- **Intuitive Navigation** - Menu sticky di atas untuk akses cepat
- **Color Coding** - Warna berbeda untuk transaksi beli/jual/operasional
- **Icons** - Bootstrap Icons untuk visual yang lebih baik
- **Feedback Messages** - Alert success/error yang jelas

## 📊 Fitur Kalkulasi

### Transaksi Penjualan
```
Keuntungan = Harga Jual - Harga Modal
Margin (%) = (Keuntungan / Harga Modal) × 100%
```

### Dashboard Stats
```
ROI (%) = (Total Keuntungan / Total Modal) × 100%
Omset = Jumlah Total Penjualan
```

## 🔄 Session Management

### Durasi Session: 30 Hari

**Skenario:**
- Login → Session aktif 30 hari
- Aktivitas dalam 30 hari → Session otomatis perpanjang ke 30 hari lagi
- Tidak aktif >30 hari → Session expire, harus login ulang
- Logout → Session langsung dihapus

Implementasi di `backend/session.php`

## 🐛 Troubleshooting

### Error: "Koneksi database gagal"
- Pastikan MySQL running
- Cek konfigurasi di `backend/config.php`
- Verifikasi username/password MySQL

### Error: "Database tidak ditemukan"
- Import file `database/database.sql`
- Atau buat database manual dengan nama `stok_hp`

### Session tidak berfungsi
- Pastikan file `tmp/` writable untuk PHP session
- Set `session.save_path` di php.ini

### CSS/JS tidak terbuka
- Pastikan path relatif di halaman benar
- Cek browser console untuk error

## 🔧 Maintenance

### Backup Database
```bash
mysqldump -u root -p stok_hp > backup_stok_hp.sql
```

### Restore Database
```bash
mysql -u root -p stok_hp < backup_stok_hp.sql
```

## 📝 Lisensi

Aplikasi ini bebas digunakan untuk keperluan pribadi dan komersial.

## 👨‍💻 Support

Untuk pertanyaan atau issues, silakan modifikasi sesuai kebutuhan Anda.

---

**Dibuat dengan ❤️ untuk memudahkan manajemen stok HP Anda**

**Happy Selling! 🎉**
