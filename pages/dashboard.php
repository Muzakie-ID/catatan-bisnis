<?php
/**
 * pages/dashboard.php
 * Halaman dashboard ringkas
 */
require_once '../backend/config.php';
require_once '../backend/session.php';

require_login(); // Cek session, redirect ke login jika tidak ada

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// GET STATISTICS
// Total Stok
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM stok WHERE user_id = ? AND status = 'tersedia'");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$total_stok = $stmt->get_result()->fetch_assoc()['total'];

// Total Modal (Investasi)
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

// Total Operasional
$stmt = $conn->prepare("SELECT COALESCE(SUM(nominal), 0) as total FROM transaksi WHERE user_id = ? AND tipe_transaksi = 'operasional'");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$total_operasional = $stmt->get_result()->fetch_assoc()['total'];

// Transaksi Terbaru (5 data)
$stmt = $conn->prepare("
    SELECT t.*, s.nama_hp, s.merk 
    FROM transaksi t 
    LEFT JOIN stok s ON t.stok_id = s.id 
    WHERE t.user_id = ? 
    ORDER BY t.tanggal_transaksi DESC, t.waktu_transaksi DESC 
    LIMIT 5
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$recent_transactions = $stmt->get_result();

// Format rupiah
function formatRupiah($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

// Get transaction type label
function getTypeLabel($type) {
    $labels = [
        'beli' => ['label' => 'Pembelian', 'badge' => 'info'],
        'jual' => ['label' => 'Penjualan', 'badge' => 'success'],
        'operasional' => ['label' => 'Operasional', 'badge' => 'warning']
    ];
    return $labels[$type] ?? ['label' => 'Lainnya', 'badge' => 'secondary'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manajemen Stok HP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-phone"></i> StokHP
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaksi.php">
                            <i class="bi bi-arrow-left-right"></i> Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stok.php">
                            <i class="bi bi-boxes"></i> Stok
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../backend/auth.php?action=logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </h1>
                <p class="text-muted">Selamat datang, <?php echo htmlspecialchars($username); ?>! 👋</p>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card info">
                    <div class="stat-label">Total Stok</div>
                    <div class="stat-number"><?php echo $total_stok; ?></div>
                    <small class="text-muted">Unit Tersedia</small>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-label">Total Modal</div>
                    <div class="stat-number" style="font-size: 1.5rem; color: var(--primary-color);">
                        <?php echo formatRupiah($total_modal); ?>
                    </div>
                    <small class="text-muted">Investasi</small>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card success">
                    <div class="stat-label">Total Penjualan</div>
                    <div class="stat-number" style="font-size: 1.5rem; color: var(--success-color);">
                        <?php echo formatRupiah($total_penjualan); ?>
                    </div>
                    <small class="text-muted">Omset</small>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card success">
                    <div class="stat-label">Total Keuntungan</div>
                    <div class="stat-number" style="font-size: 1.5rem; color: var(--success-color);">
                        <?php echo formatRupiah($total_keuntungan); ?>
                    </div>
                    <small class="text-muted">Profit</small>
                </div>
            </div>
        </div>

        <!-- ADDITIONAL STATS -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6">
                <div class="stat-card warning">
                    <div class="stat-label">Total Operasional</div>
                    <div class="stat-number" style="font-size: 1.5rem; color: var(--warning-color);">
                        <?php echo formatRupiah($total_operasional); ?>
                    </div>
                    <small class="text-muted">Pengeluaran</small>
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="stat-card">
                    <div class="stat-label">ROI (Return on Investment)</div>
                    <?php
                    $roi = $total_modal > 0 ? ($total_keuntungan / $total_modal) * 100 : 0;
                    ?>
                    <div class="stat-number" style="font-size: 1.5rem; color: var(--success-color);">
                        <?php echo number_format($roi, 1); ?>%
                    </div>
                    <small class="text-muted">Tingkat Pengembalian</small>
                </div>
            </div>
        </div>

        <!-- RECENT TRANSACTIONS -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-clock-history"></i> Transaksi Terbaru
                        </span>
                        <a href="transaksi.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_transactions->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>HP / Keterangan</th>
                                            <th>Tipe</th>
                                            <th>Nominal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($trans = $recent_transactions->fetch_assoc()): ?>
                                            <?php $type_info = getTypeLabel($trans['tipe_transaksi']); ?>
                                            <tr>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y', strtotime($trans['tanggal_transaksi'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($trans['nama_hp']) {
                                                        echo htmlspecialchars($trans['merk'] . ' ' . $trans['nama_hp']);
                                                    } else {
                                                        echo htmlspecialchars($trans['keterangan'] ?? 'Transaksi');
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $type_info['badge']; ?>">
                                                        <?php echo $type_info['label']; ?>
                                                    </span>
                                                </td>
                                                <td class="fw-bold">
                                                    <?php echo formatRupiah($trans['nominal']); ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars(substr($trans['keterangan'] ?? '', 0, 30)); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="bi bi-info-circle"></i> Belum ada transaksi. 
                                <a href="transaksi.php">Buat transaksi pertama Anda</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTION BUTTONS -->
        <div class="row g-3 mt-4">
            <div class="col-12 col-sm-6">
                <a href="transaksi.php?type=beli" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-plus-circle"></i> Input Pembelian
                </a>
            </div>
            <div class="col-12 col-sm-6">
                <a href="transaksi.php?type=jual" class="btn btn-success btn-lg w-100">
                    <i class="bi bi-check-circle"></i> Input Penjualan
                </a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-light py-3 mt-5 border-top">
        <div class="container-fluid text-center text-muted">
            <small>&copy; 2025 Manajemen Stok HP. Dibuat dengan ❤️</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
