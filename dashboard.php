<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// ================== STATS ==================
$total_buku   = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM buku"))[0];
$total_siswa  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE role='siswa'"))[0];

$pinjam_aktif = mysqli_fetch_row(mysqli_query($koneksi, "
    SELECT COUNT(*) FROM peminjaman 
    WHERE status='dipinjam'
"))[0];

$terlambat = mysqli_fetch_row(mysqli_query($koneksi, "
    SELECT COUNT(*) FROM peminjaman 
    WHERE status='dipinjam' AND tgl_kembali < CURDATE()
"))[0];

$data = mysqli_query($koneksi, "
    SELECT p.*, u.username, b.judul
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id_user
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.status='dipinjam'
    ORDER BY p.tgl_kembali ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-speedometer2 me-2"></i>Admin Perpustakaan
        </span>
        <a href="logout.php" class="btn btn-light btn-sm">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<div class="container mt-4">

    <!-- QUICK ACTION MENU -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="buku/buku.php" class="text-decoration-none">
                <div class="card shadow border-start border-primary border-5 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-book fs-1 text-primary"></i>
                        <h6 class="mt-2">Kelola Buku</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="users/user.php" class="text-decoration-none">
                <div class="card shadow border-start border-success border-5 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1 text-success"></i>
                        <h6 class="mt-2">Kelola User</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="peminjaman/pinjam.php" class="text-decoration-none">
                <div class="card shadow border-start border-warning border-5 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-repeat fs-1 text-warning"></i>
                        <h6 class="mt-2">Kelola Peminjaman</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="peminjaman/laporan.php" class="text-decoration-none">
                <div class="card shadow border-start border-dark border-5 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-bar-graph fs-1 text-dark"></i>
                        <h6 class="mt-2">Lihat Laporan</h6>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h3><?= $total_buku ?></h3>
                    <small>Total Buku</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h3><?= $total_siswa ?></h3>
                    <small>Total Siswa</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h3><?= $pinjam_aktif ?></h3>
                    <small>Sedang Dipinjam</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body text-center text-danger">
                    <h3><?= $terlambat ?></h3>
                    <small>Terlambat</small>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow">
        <div class="card-header fw-bold">
            Peminjaman Aktif 
            <a href="peminjaman/pinjam.php" class="btn btn-sm btn-primary float-end">
                Kelola
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Siswa</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tenggat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = 1;
                while($p = mysqli_fetch_assoc($data)): 
                    $late = ($p['tgl_kembali'] < date('Y-m-d'));
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($p['username']) ?></td>
                        <td><?= htmlspecialchars($p['judul']) ?></td>
                        <td><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
                        <td>
                            <span class="badge <?= $late ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                <?= date('d M Y', strtotime($p['tgl_kembali'])) ?>
                            </span>
                        </td>
                        <td><span class="badge bg-info text-dark">Dipinjam</span></td>
                    </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($data)==0): ?>
                    <tr>
                        <td colspan="5" class="text-center p-4 text-muted">
                            Tidak ada peminjaman aktif
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>