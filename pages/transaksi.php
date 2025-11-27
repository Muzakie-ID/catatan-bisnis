<?php
/**
 * pages/transaksi.php
 * Halaman input dan list transaksi
 */
require_once '../backend/config.php';
require_once '../backend/session.php';

require_login();

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$type = isset($_GET['type']) && in_array($_GET['type'], ['beli', 'jual', 'operasional']) ? $_GET['type'] : 'beli';

// Get all stok untuk dropdown (khusus untuk transaksi jual)
$stok_list = null;
if ($type === 'jual') {
    $stmt = $conn->prepare("
        SELECT id, CONCAT(merk, ' ', nama_hp, ' (', kondisi, ')') as label, harga_modal 
        FROM stok 
        WHERE user_id = ? AND status = 'tersedia' 
        ORDER BY nama_hp ASC
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stok_list = $stmt->get_result();
}

// Get transaksi list
$stmt = $conn->prepare("
    SELECT t.*, s.nama_hp, s.merk, s.kondisi
    FROM transaksi t 
    LEFT JOIN stok s ON t.stok_id = s.id 
    WHERE t.user_id = ? AND t.tipe_transaksi = ?
    ORDER BY t.tanggal_transaksi DESC, t.waktu_transaksi DESC
    LIMIT 50
");
$stmt->bind_param('is', $user_id, $type);
$stmt->execute();
$transaksi_list = $stmt->get_result();

// Format rupiah
function formatRupiah($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

// Get type label
function getTypeLabel($type) {
    $labels = [
        'beli' => 'Pembelian',
        'jual' => 'Penjualan',
        'operasional' => 'Operasional'
    ];
    return $labels[$type] ?? 'Lainnya';
}

// Get type icon
function getTypeIcon($type) {
    $icons = [
        'beli' => 'bi-plus-circle',
        'jual' => 'bi-check-circle',
        'operasional' => 'bi-gear'
    ];
    return $icons[$type] ?? 'bi-info-circle';
}

// Get type color
function getTypeColor($type) {
    $colors = [
        'beli' => 'info',
        'jual' => 'success',
        'operasional' => 'warning'
    ];
    return $colors[$type] ?? 'secondary';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Manajemen Stok HP</title>
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="transaksi.php">
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
                    <i class="bi <?php echo getTypeIcon($type); ?>"></i>
                    Input <?php echo getTypeLabel($type); ?>
                </h1>
            </div>
        </div>

        <!-- ALERTS -->
        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                foreach ($_SESSION['errors'] as $error) {
                    echo '<div>• ' . htmlspecialchars($error) . '</div>';
                }
                unset($_SESSION['errors']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- TABS -->
        <div class="row mb-3">
            <div class="col-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $type === 'beli' ? 'active' : ''; ?>" 
                           href="?type=beli">
                            <i class="bi bi-plus-circle"></i> Pembelian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $type === 'jual' ? 'active' : ''; ?>" 
                           href="?type=jual">
                            <i class="bi bi-check-circle"></i> Penjualan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $type === 'operasional' ? 'active' : ''; ?>" 
                           href="?type=operasional">
                            <i class="bi bi-gear"></i> Operasional
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <!-- FORM INPUT -->
            <div class="col-12 col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-plus"></i> Form Input
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../backend/transaksi_handler.php">
                            <input type="hidden" name="action" value="add_transaksi">
                            <input type="hidden" name="tipe_transaksi" value="<?php echo htmlspecialchars($type); ?>">

                            <div class="form-group">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_transaksi" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" name="waktu_transaksi" 
                                       value="<?php echo date('H:i'); ?>">
                            </div>

                            <?php if ($type === 'jual'): ?>
                                <div class="form-group">
                                    <label class="form-label">Pilih HP <span class="text-danger">*</span></label>
                                    <select class="form-select" name="stok_id" id="stok_select" required 
                                            onchange="updateHargaModal()">
                                        <option value="">-- Pilih HP --</option>
                                        <?php 
                                        $stok_list->data_seek(0);
                                        while ($stok = $stok_list->fetch_assoc()): 
                                        ?>
                                            <option value="<?php echo $stok['id']; ?>" 
                                                    data-harga="<?php echo $stok['harga_modal']; ?>">
                                                <?php echo htmlspecialchars($stok['label']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Harga Modal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="harga_modal" 
                                           step="100" readonly style="background-color: #f0f0f0;">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="harga_jual" 
                                           id="harga_jual" placeholder="Masukkan harga jual" 
                                           step="100" required onchange="hitungKeuntungan()">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Keuntungan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="keuntungan" 
                                               readonly style="background-color: #f0f0f0; font-weight: bold;">
                                        <span class="input-group-text">
                                            <span id="margin" style="font-weight: bold; color: #10b981;">0%</span>
                                        </span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="form-group">
                                    <label class="form-label">Nominal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="nominal" 
                                           placeholder="Masukkan nominal" step="100" required autofocus>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="keterangan" rows="3" 
                                          placeholder="Contoh: HP Sony Xperia beli dari pak Tomo, kondisi layar retak" 
                                          required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Simpan <?php echo getTypeLabel($type); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- LIST TRANSAKSI -->
            <div class="col-12 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Daftar <?php echo getTypeLabel($type); ?>
                    </div>
                    <div class="card-body">
                        <?php if ($transaksi_list->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>HP/Keterangan</th>
                                            <?php if ($type === 'jual'): ?>
                                                <th>Modal</th>
                                                <th>Jual</th>
                                                <th>Untung</th>
                                            <?php else: ?>
                                                <th>Nominal</th>
                                            <?php endif; ?>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($trans = $transaksi_list->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y', strtotime($trans['tanggal_transaksi'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php 
                                                        if ($trans['nama_hp']) {
                                                            echo htmlspecialchars($trans['merk'] . ' ' . $trans['nama_hp']);
                                                        } else {
                                                            echo htmlspecialchars(substr($trans['keterangan'], 0, 20));
                                                        }
                                                        ?>
                                                    </small>
                                                </td>
                                                <?php if ($type === 'jual'): ?>
                                                    <td>
                                                        <small><?php echo formatRupiah($trans['nominal']); ?></small>
                                                    </td>
                                                    <td>
                                                        <small class="fw-bold text-success">
                                                            <?php echo formatRupiah($trans['harga_jual']); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small class="fw-bold text-success">
                                                            <?php 
                                                            $untung = $trans['harga_jual'] - $trans['nominal'];
                                                            echo formatRupiah($untung);
                                                            ?>
                                                        </small>
                                                    </td>
                                                <?php else: ?>
                                                    <td>
                                                        <small class="fw-bold">
                                                            <?php echo formatRupiah($trans['nominal']); ?>
                                                        </small>
                                                    </td>
                                                <?php endif; ?>
                                                <td>
                                                    <form method="POST" action="../backend/transaksi_handler.php" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Yakin ingin menghapus?');">
                                                        <input type="hidden" name="action" value="delete_transaksi">
                                                        <input type="hidden" name="transaksi_id" value="<?php echo $trans['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="bi bi-info-circle"></i> 
                                Belum ada data <?php echo htmlspecialchars(getTypeLabel($type)); ?>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateHargaModal() {
            const select = document.getElementById('stok_select');
            const selected = select.options[select.selectedIndex];
            const hargaModal = selected.getAttribute('data-harga');
            document.getElementById('harga_modal').value = hargaModal || '';
            hitungKeuntungan();
        }

        function hitungKeuntungan() {
            const hargaModal = parseFloat(document.getElementById('harga_modal').value) || 0;
            const hargaJual = parseFloat(document.getElementById('harga_jual').value) || 0;
            
            if (hargaModal && hargaJual) {
                const keuntungan = hargaJual - hargaModal;
                const margin = (keuntungan / hargaModal * 100).toFixed(1);
                
                document.getElementById('keuntungan').value = 
                    new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(keuntungan);
                
                document.getElementById('margin').textContent = margin + '%';
                
                // Warna margin
                const marginElement = document.getElementById('margin').parentElement;
                if (keuntungan > 0) {
                    marginElement.style.color = '#10b981'; // hijau
                } else if (keuntungan < 0) {
                    marginElement.style.color = '#ef4444'; // merah
                } else {
                    marginElement.style.color = '#6b7280'; // abu
                }
            }
        }
    </script>
</body>
</html>
