<?php
/**
 * pages/stok.php
 * Halaman manajemen stok HP
 */
require_once '../backend/config.php';
require_once '../backend/session.php';

require_login();

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get stok list
$stmt = $conn->prepare("
    SELECT * FROM stok 
    WHERE user_id = ? 
    ORDER BY tanggal_beli DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stok_list = $stmt->get_result();

// Format rupiah
function formatRupiah($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

// Get kondisi options
$kondisi_options = [
    'baru' => 'Baru (Box Sempurna)',
    'bekas_mulus' => 'Bekas Mulus',
    'bekas_layar_gores' => 'Bekas (Layar Gores)',
    'bekas_rusak_minor' => 'Bekas (Rusak Minor)',
    'bekas_layar_retak' => 'Bekas (Layar Retak)',
    'rusak_total' => 'Rusak Total'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok - Manajemen Stok HP</title>
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
                        <a class="nav-link" href="transaksi.php">
                            <i class="bi bi-arrow-left-right"></i> Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="stok.php">
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
                    <i class="bi bi-boxes"></i> Manajemen Stok HP
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

        <div class="row">
            <!-- FORM INPUT -->
            <div class="col-12 col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-plus"></i> Tambah Stok HP Baru
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../backend/transaksi_handler.php">
                            <input type="hidden" name="action" value="add_stok">

                            <div class="form-group">
                                <label class="form-label">Merk HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="merk" 
                                       placeholder="Contoh: Sony, Samsung, iPhone" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Model/Tipe <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="tipe_hp" 
                                       placeholder="Contoh: Xperia 5, Galaxy A50" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama HP (Fullname) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_hp" 
                                       placeholder="Contoh: Sony Xperia 5 II" required autofocus>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Harga Modal (Beli) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="harga_modal" 
                                       placeholder="Contoh: 3500000" step="100" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select class="form-select" name="kondisi" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <?php foreach ($kondisi_options as $key => $label): ?>
                                        <option value="<?php echo $key; ?>">
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Beli <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_beli" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Keterangan / Catatan</label>
                                <textarea class="form-control" name="keterangan" rows="3" 
                                          placeholder="Catatan penting tentang kondisi, kerusakan, dll"></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle"></i> Tambah Stok
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- LIST STOK -->
            <div class="col-12 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Daftar Stok HP
                    </div>
                    <div class="card-body">
                        <?php if ($stok_list->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama HP</th>
                                            <th>Harga Modal</th>
                                            <th>Kondisi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($stok = $stok_list->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <small class="fw-bold">
                                                        <?php echo htmlspecialchars($stok['merk'] . ' ' . $stok['tipe_hp']); ?>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($stok['nama_hp']); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="fw-bold">
                                                        <?php echo formatRupiah($stok['harga_modal']); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="badge badge-info">
                                                        <?php 
                                                        echo htmlspecialchars($kondisi_options[$stok['kondisi']] ?? $stok['kondisi']);
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $badge_color = [
                                                        'tersedia' => 'success',
                                                        'terjual' => 'secondary',
                                                        'rusak' => 'danger'
                                                    ];
                                                    $color = $badge_color[$stok['status']] ?? 'secondary';
                                                    $status_label = [
                                                        'tersedia' => 'Tersedia',
                                                        'terjual' => 'Terjual',
                                                        'rusak' => 'Rusak'
                                                    ];
                                                    ?>
                                                    <span class="badge badge-<?php echo $color; ?>">
                                                        <?php echo $status_label[$stok['status']] ?? 'Lainnya'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <form method="POST" action="../backend/transaksi_handler.php" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Yakin ingin menghapus?');">
                                                        <input type="hidden" name="action" value="delete_stok">
                                                        <input type="hidden" name="stok_id" value="<?php echo $stok['id']; ?>">
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
                                Belum ada stok HP. Mulai dengan menambahkan stok baru.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
