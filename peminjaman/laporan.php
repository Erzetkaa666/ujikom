<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================= FILTER ================= */
$where = "1=1";

$tgl_dari   = $_GET['dari']   ?? '';
$tgl_sampai = $_GET['sampai'] ?? '';
$status     = $_GET['status'] ?? '';

if ($tgl_dari && $tgl_sampai) {
    $where .= " AND DATE(p.tgl_pinjam) BETWEEN '$tgl_dari' AND '$tgl_sampai'";
}

if ($status && $status != 'semua') {
    $where .= " AND p.status = '$status'";
}

/* ================= QUERY DATA ================= */
$query = mysqli_query($koneksi, "
    SELECT p.*, u.username, b.judul
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id_user
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE $where
    ORDER BY p.tgl_pinjam DESC
");

/* ================= STATISTIK ================= */
$total_data = mysqli_num_rows($query);

$total_dipinjam = mysqli_fetch_row(mysqli_query($koneksi, "
    SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'
"))[0];

$total_kembali = mysqli_fetch_row(mysqli_query($koneksi, "
    SELECT COUNT(*) FROM peminjaman WHERE status='kembali'
"))[0];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Peminjaman Buku
        </h4>
        <button onclick="window.print()" class="btn btn-dark btn-sm">
            <i class="bi bi-printer"></i> Print
        </button>
    </div>

    <!-- FILTER -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label>Tanggal Dari</label>
                    <input type="date" name="dari" value="<?= $tgl_dari ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Sampai</label>
                    <input type="date" name="sampai" value="<?= $tgl_sampai ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="semua">Semua</option>
                        <option value="dipinjam" <?= $status=='dipinjam'?'selected':'' ?>>Dipinjam</option>
                        <option value="kembali" <?= $status=='kembali'?'selected':'' ?>>Dikembalikan</option>
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow border-start border-primary border-4">
                <div class="card-body">
                    <h5><?= $total_data ?></h5>
                    <small>Total Data Ditampilkan</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-start border-warning border-4">
                <div class="card-body">
                    <h5><?= $total_dipinjam ?></h5>
                    <small>Sedang Dipinjam</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-start border-success border-4">
                <div class="card-body">
                    <h5><?= $total_kembali ?></h5>
                    <small>Sudah Dikembalikan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL LAPORAN -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th>Siswa</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat</th>
                            <th>Tgl Kembali Asli</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($total_data > 0): ?>
                        <?php $no=1; while($d=mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($d['username']) ?></td>
                            <td><?= htmlspecialchars($d['judul']) ?></td>
                            <td><?= date('d/m/Y', strtotime($d['tgl_pinjam'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($d['tgl_kembali'])) ?></td>
                            <td>
                                <?= $d['tgl_kembali_asli'] 
                                    ? date('d/m/Y H:i', strtotime($d['tgl_kembali_asli'])) 
                                    : '-' ?>
                            </td>
                            <td>
                                <span class="badge <?= $d['status']=='dipinjam'?'bg-warning text-dark':'bg-success' ?>">
                                    <?= $d['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center p-4 text-muted">
                                Tidak ada data laporan.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>