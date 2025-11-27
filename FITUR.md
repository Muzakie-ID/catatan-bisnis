# 📚 DOKUMENTASI FITUR LENGKAP

## 🏠 1. HALAMAN LOGIN

### Fitur:
- ✅ Login dengan username/email
- ✅ Validasi input
- ✅ Error messages yang jelas
- ✅ Link ke halaman register
- ✅ Session 30 hari otomatis

### URL: `pages/login.php`

### Validasi:
- Username/email dan password harus diisi
- Minimal 1 username dan password (tidak ada batasan panjang min di sini)
- Pencocokan dengan database

### Keamanan:
- Password di-hash dengan BCRYPT
- SQL Injection protection (prepared statement)
- XSS prevention (htmlspecialchars)

---

## 📝 2. HALAMAN REGISTER

### Fitur:
- ✅ Daftar akun baru
- ✅ Validasi lengkap
- ✅ Check duplicate username/email
- ✅ Password confirmation
- ✅ Link ke halaman login

### URL: `pages/register.php`

### Validasi:
- **Username**: 3+ karakter, huruf/angka/underscore saja
- **Email**: Format email valid
- **Password**: 6+ karakter
- **Confirm Password**: Harus cocok dengan password
- **Unique Check**: Username dan email tidak boleh sama

### Error Messages:
```
- Username minimal 3 karakter
- Email tidak valid
- Password minimal 6 karakter
- Username hanya boleh berisi huruf, angka, dan underscore
- Password tidak cocok
- Username atau email sudah terdaftar
```

---

## 📊 3. HALAMAN DASHBOARD

### Fitur:
- ✅ Ringkasan statistik lengkap
- ✅ Transaksi terbaru (5 data terakhir)
- ✅ Quick action buttons
- ✅ Real-time calculations
- ✅ Responsive design

### URL: `pages/dashboard.php` (require login)

### Statistik yang Ditampilkan:

| Stat | Keterangan | Formula |
|------|-----------|---------|
| **Total Stok** | Jumlah HP tersedia | COUNT(stok) WHERE status='tersedia' |
| **Total Modal** | Total investasi pembelian | SUM(nominal) WHERE tipe='beli' |
| **Total Penjualan** | Total omset penjualan | SUM(harga_jual) WHERE tipe='jual' |
| **Total Keuntungan** | Total profit | SUM(harga_jual - nominal) WHERE tipe='jual' |
| **Total Operasional** | Total biaya operasional | SUM(nominal) WHERE tipe='operasional' |
| **ROI** | Return on Investment | (Total Keuntungan / Total Modal) × 100% |

### Data Terbaru:
- Menampilkan 5 transaksi terakhir
- Sorts by: tanggal_transaksi DESC, waktu_transaksi DESC
- Tipe transaksi dibedakan warna (info, success, warning)

### Quick Actions:
- 🔵 **Input Pembelian** → Link ke transaksi.php?type=beli
- 🟢 **Input Penjualan** → Link ke transaksi.php?type=jual

---

## 🔄 4. HALAMAN TRANSAKSI

### Fitur:
- ✅ Input transaksi beli/jual/operasional
- ✅ Tab navigation untuk filter tipe
- ✅ List transaksi dengan pagination
- ✅ Hitung keuntungan otomatis (khusus penjualan)
- ✅ Delete transaksi

### URL: `pages/transaksi.php?type=[beli|jual|operasional]` (require login)

### Tab 1: PEMBELIAN (Beli)

**Form Input:**
- Tanggal (required)
- Waktu (optional)
- Nominal/Harga (required)
- Keterangan (required)

**Example Input:**
```
Tanggal: 2025-01-15
Waktu: 14:30
Nominal: Rp 3.500.000
Keterangan: HP Sony Xperia 5 II beli dari pak Tomo
```

**Validasi:**
- Semua field harus diisi
- Nominal > 0

### Tab 2: PENJUALAN (Jual)

**Form Input:**
- Tanggal (required)
- Waktu (optional)
- Pilih HP (required) - dropdown dari stok tersedia
- Harga Modal (read-only) - auto-fill dari stok
- Harga Jual (required)
- Keuntungan (read-only) - hitung otomatis

**Formula Keuntungan:**
```javascript
Keuntungan = Harga Jual - Harga Modal
Margin (%) = (Keuntungan / Harga Modal) × 100%
```

**Example Kalkulasi:**
```
HP: Sony Xperia 5 II
Harga Modal: Rp 3.500.000
Harga Jual: Rp 4.200.000
────────────────────────────
Keuntungan: Rp 700.000
Margin: 20%
```

**Warna Indicator:**
- 🟢 Keuntungan (positif)
- 🔴 Rugi (negatif)
- ⚪ Break-even (0%)

### Tab 3: OPERASIONAL

**Form Input:**
- Tanggal (required)
- Waktu (optional)
- Nominal (required)
- Keterangan (required)

**Example Input:**
```
Tanggal: 2025-01-15
Waktu: 10:00
Nominal: Rp 250.000
Keterangan: Bensin perjalanan ke Cirebon untuk ambil stok
```

### List Transaksi:
- Tampil 50 transaksi terbaru per tipe
- Tabel responsive
- Delete button untuk setiap row
- Confirmation dialog sebelum delete

---

## 📦 5. HALAMAN STOK

### Fitur:
- ✅ Tambah stok HP baru
- ✅ List semua stok dengan filter status
- ✅ Delete stok
- ✅ Track kondisi barang

### URL: `pages/stok.php` (require login)

### Form Tambah Stok:

**Field:**
- **Merk** (required) - Contoh: Sony, Samsung, iPhone
- **Model/Tipe** (required) - Contoh: Xperia 5, Galaxy A50
- **Nama HP** (required) - Full name, Contoh: Sony Xperia 5 II
- **Harga Modal** (required) - Harga beli
- **Kondisi** (required) - Pilih dari dropdown
- **Tanggal Beli** (required)
- **Keterangan** (optional) - Catatan khusus

### Opsi Kondisi:

```
✨ Baru (Box Sempurna)
   - Masih dalam box original
   - Belum pernah dipakai

🔄 Bekas Mulus
   - Bekas pakai tapi masih bagus
   - Tidak ada kerusakan

📱 Bekas (Layar Gores)
   - Layar sedikit gores
   - Masih bisa digunakan

⚠️ Bekas (Rusak Minor)
   - Ada kerusakan kecil
   - Tombol atau kamera rusak

❌ Bekas (Layar Retak)
   - Layar retak parah
   - Masih bisa nyala/berfungsi

🔴 Rusak Total
   - Tidak bisa digunakan
   - Tidak nyala
```

### Status Stok:

| Status | Arti | Warna |
|--------|------|-------|
| **Tersedia** | Belum dijual | 🟢 Hijau |
| **Terjual** | Sudah dijual (auto-update) | ⚪ Abu |
| **Rusak** | Tidak jual | 🔴 Merah |

### List Stok:
- Menampilkan semua stok dengan sorting terbaru
- Info: Nama, Harga Modal, Kondisi, Status
- Delete button untuk setiap HP

---

## 🔐 6. SISTEM SESSION & LOGIN MANAGEMENT

### Durasi Session: 30 Hari

### Flow:

```
┌─────────────────┐
│  User Login     │
│  (username+pwd) │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────┐
│ Verifikasi password & create    │
│ session dengan expiry 30 hari   │
└────────┬────────────────────────┘
         │
         ▼
┌──────────────────────────┐
│ Setiap klik halaman      │
│ check aktivitas & extend │
│ session jika dalam 30h   │
└────────┬─────────────────┘
         │
    ┌────┴─────┐
    │           │
    ▼           ▼
┌─────────┐ ┌──────────────┐
│Aktif <  │ │ Timeout >    │
│  30h    │ │   30h        │
└────┬────┘ └────┬─────────┘
     │            │
     ▼            ▼
 Extend      Redirect Login
 Session
```

### Implementation:

**File:** `backend/session.php`

**Functions:**
```php
check_session()      // Cek & validasi session
set_session()        // Set session setelah login
destroy_session()    // Hapus session saat logout
require_login()      // Redirect jika tidak ada session
require_logout()     // Redirect jika sudah login
```

**Session Variables:**
```php
$_SESSION['user_id']        // ID user
$_SESSION['username']       // Username
$_SESSION['login_time']     // Waktu login awal
$_SESSION['last_activity']  // Update setiap aktivitas
```

### Skenario:

**Scenario 1: Normal Usage**
```
Jan 1 Login → Session aktif s/d Jan 31
Jan 15 Buka app → Activity update, session aktif s/d Feb 15
Jan 20 Buka app → Activity update, session aktif s/d Feb 20
```

**Scenario 2: Timeout**
```
Jan 1 Login → Session aktif s/d Jan 31
Jan 31 Tidak buka app
Feb 1 Buka app → Session expired (>30h), redirect login
```

---

## 🎨 7. DESAIN & RESPONSIVE DESIGN

### Breakpoints:

| Screen Size | Breakpoint | Device |
|------------|-----------|--------|
| Smartphone | ≤576px | Mobile |
| Tablet | 576px - 992px | Tablet/iPad |
| Desktop | ≥992px | PC/Laptop |

### Responsive Features:

**Mobile (≤576px):**
- Font size smaller
- Padding/margin reduced
- Single column layout
- Full-width buttons

**Tablet (576px - 992px):**
- Medium font size
- 2-column layouts
- Half-width buttons
- Touch-friendly spacing

**Desktop (≥992px):**
- Normal font size
- Multi-column layouts
- Side-by-side components
- Full features

### CSS Classes:

```css
/* Bootstrap Grid */
.col-12              /* Full width */
.col-sm-6            /* 50% di tablet */
.col-lg-3            /* 25% di desktop */

/* Custom Responsive */
@media (max-width: 768px)  /* Tablet down */
@media (max-width: 576px)  /* Mobile */
```

---

## 🔒 8. KEAMANAN & BEST PRACTICES

### Implementasi Keamanan:

#### 1. SQL Injection Protection
```php
// ❌ TIDAK AMAN
$query = "SELECT * FROM users WHERE username = '$username'";

// ✅ AMAN - Prepared Statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
```

#### 2. Password Security
```php
// Hashing dengan BCRYPT (SHA1/MD5 jangan!)
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Verifikasi
if (password_verify($input_password, $hashed)) {
    // Correct password
}
```

#### 3. XSS Prevention
```php
// ❌ TIDAK AMAN
echo "Hello " . $username;

// ✅ AMAN
echo "Hello " . htmlspecialchars($username);
```

#### 4. Session Security
```php
// Session timeout
$timeout = 60 * 60 * 24 * 30; // 30 hari
if ((time() - $_SESSION['last_activity']) > $timeout) {
    destroy_session();
}

// Update activity setiap request
$_SESSION['last_activity'] = time();
```

#### 5. CSRF Protection (Optional)
Untuk production, tambahkan token CSRF:
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate di form
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}
```

---

## 📱 9. TIPS PENGGUNAAN MOBILE

### Best Practices:

1. **Landscape Mode** - Gunakan portrait untuk optimal UX
2. **Internet Connection** - Pastikan terhubung untuk sync data
3. **Browser** - Gunakan Chrome/Firefox terbaru
4. **Zoom** - Jangan zoom in/out (sudah responsive)

### Shortcuts:

- **Login cepat** - Simpan password di browser (optional)
- **Back button** - Gunakan back browser bukan back button app
- **Pull refresh** - F5 untuk refresh data

---

## 🐛 10. DEBUGGING & TROUBLESHOOTING

### Enable Debug Mode

Edit `backend/config.php`:

```php
// Development mode
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Production mode
ini_set('display_errors', 0);
error_reporting(0);
```

### Log Error

```php
// Tulis error ke file
error_log("Database error: " . $conn->error, 3, "error.log");
```

### Test Database Connection

```php
// test_db.php
require_once 'backend/config.php';
echo $conn->connect_error ? "Error: " . $conn->connect_error : "Connected!";
```

---

**Dokumentasi Lengkap ✅**

Untuk pertanyaan lebih lanjut, silakan modifikasi code sesuai kebutuhan!
