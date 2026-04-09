<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Peminjaman hari ini milik user ini
$hari_ini = mysqli_query($koneksi, "
    SELECT p.*, b.judul 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_user = '$id_user'
    AND DATE(p.tgl_pinjam) = CURDATE()
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <span class="navbar-brand fw-bold">
                <i class="bi bi-book me-2"></i>Perpustakaan Siswa
            </span>
            <div class="navbar-nav ms-auto">
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5><i class="bi bi-calendar-day me-2"></i>Peminjaman Hari Ini</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($hari_ini) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Buku</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tenggat Kembali</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($p = mysqli_fetch_assoc($hari_ini)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($p['tgl_pinjam'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($p['tgl_kembali'])) ?></td>
                                                <td>
                                                    <span class="badge bg-warning"><?= htmlspecialchars($p['status']) ?></span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-book display-4 text-muted"></i>
                                <p class="text-muted mt-3">Belum ada peminjaman hari ini.</p>
                                <a href="peminjaman/pinjam_siswa.php" class="btn btn-primary">Pinjam Buku</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h6><i class="bi bi-person-circle me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="peminjaman/pinjam_siswa.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-plus-circle me-2"></i>Pinjam Buku Baru
                        </a>

                        <a href="peminjaman/riwayat.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman
                        </a>

                        <a href="profil.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-person-gear me-2"></i>Profil
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>