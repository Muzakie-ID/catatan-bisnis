<?php
/**
 * backend/transaksi_handler.php
 * Handler untuk input transaksi
 */

require_once 'config.php';
require_once 'session.php';

require_login();

$user_id = $_SESSION['user_id'];

// INPUT TRANSAKSI BARU
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_transaksi') {
    $tipe_transaksi = trim($_POST['tipe_transaksi'] ?? '');
    $tanggal_transaksi = trim($_POST['tanggal_transaksi'] ?? '');
    $waktu_transaksi = trim($_POST['waktu_transaksi'] ?? '');
    $nominal = floatval($_POST['nominal'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');
    $stok_id = !empty($_POST['stok_id']) ? intval($_POST['stok_id']) : null;
    
    // Untuk transaksi jual
    $harga_jual = !empty($_POST['harga_jual']) ? floatval($_POST['harga_jual']) : null;
    
    $errors = [];
    
    // Validasi
    if (!in_array($tipe_transaksi, ['beli', 'jual', 'operasional'])) {
        $errors[] = 'Tipe transaksi tidak valid';
    }
    
    if (empty($tanggal_transaksi)) {
        $errors[] = 'Tanggal transaksi harus diisi';
    }
    
    if ($nominal <= 0) {
        $errors[] = 'Nominal harus lebih besar dari 0';
    }
    
    if ($tipe_transaksi === 'jual' && ($harga_jual === null || $harga_jual <= 0)) {
        $errors[] = 'Harga jual harus diisi untuk transaksi penjualan';
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/transaksi.php?type=' . $tipe_transaksi);
        exit();
    }
    
    // Insert transaksi
    $stmt = $conn->prepare("
        INSERT INTO transaksi 
        (user_id, stok_id, tipe_transaksi, nominal, keterangan, tanggal_transaksi, waktu_transaksi, harga_jual) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        'iisdsss',
        $user_id,
        $stok_id,
        $tipe_transaksi,
        $nominal,
        $keterangan,
        $tanggal_transaksi,
        $waktu_transaksi,
        $harga_jual
    );
    
    if ($stmt->execute()) {
        // Jika transaksi jual, update status stok menjadi terjual
        if ($tipe_transaksi === 'jual' && $stok_id) {
            $update_stmt = $conn->prepare("UPDATE stok SET status = 'terjual' WHERE id = ? AND user_id = ?");
            $update_stmt->bind_param('ii', $stok_id, $user_id);
            $update_stmt->execute();
        }
        
        $_SESSION['success'] = 'Transaksi berhasil dicatat!';
        header('Location: ../pages/transaksi.php?type=' . $tipe_transaksi);
        exit();
    } else {
        $_SESSION['errors'] = ['Gagal menyimpan transaksi. Coba lagi.'];
        header('Location: ../pages/transaksi.php?type=' . $tipe_transaksi);
        exit();
    }
}

// INPUT STOK BARU
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_stok') {
    $nama_hp = trim($_POST['nama_hp'] ?? '');
    $merk = trim($_POST['merk'] ?? '');
    $tipe_hp = trim($_POST['tipe_hp'] ?? '');
    $harga_modal = floatval($_POST['harga_modal'] ?? 0);
    $kondisi = trim($_POST['kondisi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tanggal_beli = trim($_POST['tanggal_beli'] ?? '');
    
    $errors = [];
    
    if (empty($nama_hp)) {
        $errors[] = 'Nama HP harus diisi';
    }
    
    if ($harga_modal <= 0) {
        $errors[] = 'Harga modal harus lebih besar dari 0';
    }
    
    if (empty($kondisi)) {
        $errors[] = 'Kondisi harus dipilih';
    }
    
    if (empty($tanggal_beli)) {
        $errors[] = 'Tanggal beli harus diisi';
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/stok.php');
        exit();
    }
    
    // Insert stok
    $stmt = $conn->prepare("
        INSERT INTO stok 
        (user_id, nama_hp, merk, tipe_hp, harga_modal, kondisi, keterangan, tanggal_beli) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        'isssdsss',
        $user_id,
        $nama_hp,
        $merk,
        $tipe_hp,
        $harga_modal,
        $kondisi,
        $keterangan,
        $tanggal_beli
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Stok HP berhasil ditambahkan!';
        header('Location: ../pages/stok.php');
        exit();
    } else {
        $_SESSION['errors'] = ['Gagal menambahkan stok. Coba lagi.'];
        header('Location: ../pages/stok.php');
        exit();
    }
}

// DELETE TRANSAKSI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_transaksi') {
    $transaksi_id = intval($_POST['transaksi_id'] ?? 0);
    
    if ($transaksi_id <= 0) {
        $_SESSION['errors'] = ['ID transaksi tidak valid'];
        header('Location: ../pages/transaksi.php');
        exit();
    }
    
    // Cek ownership
    $stmt = $conn->prepare("SELECT id FROM transaksi WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $transaksi_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        $_SESSION['errors'] = ['Transaksi tidak ditemukan'];
        header('Location: ../pages/transaksi.php');
        exit();
    }
    
    $delete_stmt = $conn->prepare("DELETE FROM transaksi WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param('ii', $transaksi_id, $user_id);
    
    if ($delete_stmt->execute()) {
        $_SESSION['success'] = 'Transaksi berhasil dihapus!';
    } else {
        $_SESSION['errors'] = ['Gagal menghapus transaksi'];
    }
    
    header('Location: ../pages/transaksi.php');
    exit();
}

// DELETE STOK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_stok') {
    $stok_id = intval($_POST['stok_id'] ?? 0);
    
    if ($stok_id <= 0) {
        $_SESSION['errors'] = ['ID stok tidak valid'];
        header('Location: ../pages/stok.php');
        exit();
    }
    
    // Cek ownership
    $stmt = $conn->prepare("SELECT id FROM stok WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $stok_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        $_SESSION['errors'] = ['Stok tidak ditemukan'];
        header('Location: ../pages/stok.php');
        exit();
    }
    
    $delete_stmt = $conn->prepare("DELETE FROM stok WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param('ii', $stok_id, $user_id);
    
    if ($delete_stmt->execute()) {
        $_SESSION['success'] = 'Stok berhasil dihapus!';
    } else {
        $_SESSION['errors'] = ['Gagal menghapus stok'];
    }
    
    header('Location: ../pages/stok.php');
    exit();
}
?>
