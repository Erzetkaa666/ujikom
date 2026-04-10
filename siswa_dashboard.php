<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* ================= DATA PROFIL ================= */
$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT * FROM users WHERE id_user='$id_user'
"));

/* ================= STATISTIK ================= */
$total_pinjam = mysqli_fetch_row(mysqli_query($koneksi, "
    SELECT COUNT(*) FROM peminjaman WHERE id_user='$id_user'
"))[0];

$masih_pinjam = mysqli_fetch_row(mysqli_query($koneksi, "
    SELECT COUNT(*) FROM peminjaman 
    WHERE id_user='$id_user' AND status='dipinjam'
"))[0];

$sudah_kembali = mysqli_fetch_row(mysqli_query($koneksi, "
    SELECT COUNT(*) FROM peminjaman 
    WHERE id_user='$id_user' AND status='kembali'
"))[0];

/* ================= PEMINJAMAN HARI INI ================= */
$hari_ini = mysqli_query($koneksi, "
    SELECT p.*, b.judul 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_user = '$id_user'
    AND DATE(p.tgl_pinjam) = CURDATE()
");

/* ================= RIWAYAT SEMUA ================= */
$riwayat = mysqli_query($koneksi, "
    SELECT p.*, b.judul
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_user='$id_user'
    ORDER BY p.tgl_pinjam DESC
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

<nav class="navbar navbar-dark bg-primary sticky-top shadow">
    <div class="container">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-mortarboard me-2"></i>Dashboard Siswa
        </span>
        <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">

    <!-- STATISTIK -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow border-start border-primary border-4">
                <div class="card-body">
                    <h4><?= $total_pinjam ?></h4>
                    <small>Total Peminjaman</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-start border-warning border-4">
                <div class="card-body">
                    <h4><?= $masih_pinjam ?></h4>
                    <small>Masih Dipinjam</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-start border-success border-4">
                <div class="card-body">
                    <h4><?= $sudah_kembali ?></h4>
                    <small>Sudah Dikembalikan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- KIRI -->
        <div class="col-md-8">

            <!-- PEMINJAMAN HARI INI -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-calendar-day me-2"></i>Peminjaman Hari Ini
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($hari_ini) > 0): ?>
                        <table class="table table-bordered">
                            <tr class="table-dark">
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                            </tr>
                            <?php while ($p = mysqli_fetch_assoc($hari_ini)): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td><?= date('d/m/Y', strtotime($p['tgl_pinjam'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($p['tgl_kembali'])) ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?= $p['status'] ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            Belum ada peminjaman hari ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIWAYAT SEMUA -->
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-bordered mb-0">
                        <tr class="table-secondary">
                            <th>No</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat</th>
                            <th>Status</th>
                        </tr>
                        <?php $no=1; while($r=mysqli_fetch_assoc($riwayat)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($r['judul']) ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tgl_kembali'])) ?></td>
                            <td>
                                <span class="badge <?= $r['status']=='dipinjam'?'bg-warning text-dark':'bg-success' ?>">
                                    <?= $r['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>

        </div>

        <!-- KANAN -->
        <div class="col-md-4">

            <!-- PROFIL -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-person-circle me-2"></i>Profil Siswa
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong><br><?= htmlspecialchars($profil['username']) ?></p>
                    <p><strong>Role:</strong><br><?= htmlspecialchars($profil['role']) ?></p>
                    <a href="profil.php" class="btn btn-outline-primary btn-sm w-100">
                        Edit Profil
                    </a>
                </div>
            </div>

            <!-- AKSI CEPAT -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-lightning-charge me-2"></i>Aksi Cepat
                </div>
                <div class="list-group list-group-flush">
                    <a href="peminjaman/pinjam_siswa.php" class="list-group-item list-group-item-action">
                        Pinjam Buku
                    </a>
                    <a href="peminjaman/riwayat.php" class="list-group-item list-group-item-action">
                        Lihat Riwayat Detail
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
</body>
</html>