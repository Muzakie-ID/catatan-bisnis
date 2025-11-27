<?php
/**
 * backend/api.php
 * API endpoints untuk AJAX requests (optional)
 */

require_once 'config.php';
require_once 'session.php';

header('Content-Type: application/json');

require_login();

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// GET STOK DETAIL
if ($action === 'get_stok') {
    $stok_id = intval($_GET['stok_id'] ?? 0);
    
    $stmt = $conn->prepare("
        SELECT * FROM stok 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param('ii', $stok_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'success', 'data' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Stok tidak ditemukan']);
    }
    exit();
}

// GET STATS
if ($action === 'get_stats') {
    // Total Stok
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM stok WHERE user_id = ? AND status = 'tersedia'");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $total_stok = $stmt->get_result()->fetch_assoc()['total'];

    // Total Modal
    $stmt = $conn->prepare("SELECT COALESCE(SUM(nominal), 0) as total FROM transaksi WHERE user_id = ? AND tipe_transaksi = 'beli'");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $total_modal = $stmt->get_result()->fetch_assoc()['total'];

    // Total Penjualan
    $stmt = $conn->prepare("SELECT COALESCE(SUM(harga_jual), 0) as total FROM transaksi WHERE user_id = ? AND tipe_transaksi = 'jual'");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $total_penjualan = $stmt->get_result()->fetch_assoc()['total'];

    // Total Keuntungan
    $stmt = $conn->prepare("SELECT COALESCE(SUM(harga_jual - nominal), 0) as total FROM transaksi WHERE user_id = ? AND tipe_transaksi = 'jual'");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $total_keuntungan = $stmt->get_result()->fetch_assoc()['total'];

    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_stok' => $total_stok,
            'total_modal' => $total_modal,
            'total_penjualan' => $total_penjualan,
            'total_keuntungan' => $total_keuntungan,
            'roi' => $total_modal > 0 ? ($total_keuntungan / $total_modal) * 100 : 0
        ]
    ]);
    exit();
}

// GET TRANSAKSI BY DATE RANGE
if ($action === 'get_transaksi_range') {
    $from_date = $_GET['from_date'] ?? date('Y-m-01');
    $to_date = $_GET['to_date'] ?? date('Y-m-t');
    $type = $_GET['type'] ?? 'all';
    
    if ($type === 'all') {
        $stmt = $conn->prepare("
            SELECT t.*, s.nama_hp, s.merk
            FROM transaksi t 
            LEFT JOIN stok s ON t.stok_id = s.id 
            WHERE t.user_id = ? AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
            ORDER BY t.tanggal_transaksi DESC
        ");
        $stmt->bind_param('iss', $user_id, $from_date, $to_date);
    } else {
        $stmt = $conn->prepare("
            SELECT t.*, s.nama_hp, s.merk
            FROM transaksi t 
            LEFT JOIN stok s ON t.stok_id = s.id 
            WHERE t.user_id = ? AND t.tipe_transaksi = ? AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
            ORDER BY t.tanggal_transaksi DESC
        ");
        $stmt->bind_param('isss', $user_id, $type, $from_date, $to_date);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $transaksi = [];
    
    while ($row = $result->fetch_assoc()) {
        $transaksi[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $transaksi]);
    exit();
}

// DEFAULT ERROR
echo json_encode(['status' => 'error', 'message' => 'Action not found']);
?>
