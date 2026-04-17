<?php
session_start();
include '../koneksi.php';

// ANTI CACHE (anti back setelah logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* ================= FILTER ================= */
$cari   = $_GET['cari'] ?? '';
$status = $_GET['status'] ?? '';
$dari   = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$where = "WHERE p.id_user='$id_user'";

if ($cari) {
    $cari = mysqli_real_escape_string($koneksi, $cari);
    $where .= " AND (b.judul LIKE '%$cari%' OR b.pengarang LIKE '%$cari%')";
}

if ($status == 'dipinjam') {
    $where .= " AND p.status='dipinjam'";
} elseif ($status == 'kembali') {
    $where .= " AND p.status='kembali'";
} elseif ($status == 'terlambat') {
    $where .= " AND p.status='dipinjam' AND p.tgl_kembali < CURDATE()";
}

if ($dari && $sampai) {
    $where .= " AND DATE(p.tgl_pinjam) BETWEEN '$dari' AND '$sampai'";
}

$query = "
    SELECT p.*, b.judul, b.pengarang
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    $where
    ORDER BY p.tgl_pinjam DESC
";

$riwayat = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Detail Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="../siswa_dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card shadow mb-3">
        <div class="card-header bg-primary text-white">Filter Riwayat</div>
        <div class="card-body">
            <form class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="cari" class="form-control" placeholder="Cari judul / pengarang" value="<?= htmlspecialchars($cari) ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" <?= $status=='dipinjam'?'selected':'' ?>>Dipinjam</option>
                        <option value="kembali" <?= $status=='kembali'?'selected':'' ?>>Kembali</option>
                        <option value="terlambat" <?= $status=='terlambat'?'selected':'' ?>>Terlambat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
                </div>
                <div class="col-md-3 d-grid">
                    <button class="btn btn-success">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            Riwayat Detail Peminjaman
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Tgl Pinjam</th>
                        <th>Tenggat</th>
                        <th>Tgl Kembali Asli</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(mysqli_num_rows($riwayat)==0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Data tidak ditemukan</td></tr>
                <?php else: ?>
                    <?php $no=1; while($r=mysqli_fetch_assoc($riwayat)): 
                        $terlambat = ($r['status']=='dipinjam' && $r['tgl_kembali'] < date('Y-m-d'));
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['judul']) ?></td>
                        <td><?= htmlspecialchars($r['pengarang']) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['tgl_kembali'])) ?></td>
                        <td>
                            <?= $r['tgl_kembali_asli'] 
                                ? date('d/m/Y H:i', strtotime($r['tgl_kembali_asli'])) 
                                : '-' ?>
                        </td>
                        <td>
                            <?php if($r['status']=='kembali'): ?>
                                <span class="badge bg-success">Dikembalikan</span>
                            <?php elseif($terlambat): ?>
                                <span class="badge bg-danger">Terlambat</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>