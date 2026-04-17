<?php
session_start();
include 'koneksi.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* ================= PROSES KEMBALIKAN ================= */
if (isset($_POST['kembalikan'])) {
    $id_pinjam = (int) $_POST['id_pinjam'];

    mysqli_begin_transaction($koneksi);
    try {
        $pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT id_buku FROM peminjaman
            WHERE id_pinjam='$id_pinjam'
            AND id_user='$id_user'
            AND status='dipinjam'
            FOR UPDATE
        "));

        if (!$pinjam) throw new Exception("Tidak valid");

        mysqli_query($koneksi, "
            UPDATE peminjaman 
            SET status='dikembalikan',
                tgl_dikembalikan=CURDATE()
            WHERE id_pinjam='$id_pinjam'
        ");

        mysqli_query($koneksi, "
            UPDATE buku
            SET stok = stok + 1
            WHERE id_buku='{$pinjam['id_buku']}'
        ");

        mysqli_commit($koneksi);
        header("Location: siswa_dashboard.php");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
    }
}

/* ================= DATA PROFIL ================= */
$profil = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM users WHERE id_user='$id_user'
"));

/* ================= STATISTIK ================= */
$total_pinjam = mysqli_fetch_row(mysqli_query($koneksi,"
    SELECT COUNT(*) FROM peminjaman WHERE id_user='$id_user'
"))[0];

$masih_pinjam = mysqli_fetch_row(mysqli_query($koneksi,"
    SELECT COUNT(*) FROM peminjaman 
    WHERE id_user='$id_user' AND status='dipinjam'
"))[0];

$sudah_kembali = mysqli_fetch_row(mysqli_query($koneksi,"
    SELECT COUNT(*) FROM peminjaman 
    WHERE id_user='$id_user' AND status='dikembalikan'
"))[0];

/* ================= PEMINJAMAN HARI INI ================= */
$hari_ini = mysqli_query($koneksi, "
    SELECT p.*, b.judul 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_user = '$id_user'
    AND DATE(p.tgl_pinjam) = CURDATE()
");

/* ================= RIWAYAT ================= */
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
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-mortarboard-fill me-2"></i>Dashboard Siswa
        </span>
        <div>
            <span class="text-white me-3">Halo, <b><?= htmlspecialchars($profil['username']) ?></b></span>
            <a href="logout.php" class="btn btn-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <!-- STAT -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow border-start border-primary border-4">
                <div class="card-body text-center">
                    <h3><?= $total_pinjam ?></h3>
                    <small>Total Peminjaman</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-start border-warning border-4">
                <div class="card-body text-center">
                    <h3><?= $masih_pinjam ?></h3>
                    <small>Masih Dipinjam</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-start border-success border-4">
                <div class="card-body text-center">
                    <h3><?= $sudah_kembali ?></h3>
                    <small>Sudah Dikembalikan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- KIRI -->
        <div class="col-md-8">

            <!-- HARI INI -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white fw-bold">
                    Peminjaman Hari Ini
                </div>
                <div class="card-body p-0">
                <?php if(mysqli_num_rows($hari_ini)>0): ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($p=mysqli_fetch_assoc($hari_ini)): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
                                <td><?= date('d M Y', strtotime($p['tgl_kembali'])) ?></td>
                                <td><span class="badge bg-warning text-dark"><?= $p['status'] ?></span></td>
                                <td>
                                <?php if($p['status']=='dipinjam'): ?>
                                    <form method="post">
                                        <input type="hidden" name="id_pinjam" value="<?= $p['id_pinjam'] ?>">
                                        <button name="kembalikan" class="btn btn-success btn-sm">Kembalikan</button>
                                    </form>
                                <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">Belum ada peminjaman hari ini.</div>
                <?php endif; ?>
                </div>
            </div>

            <!-- RIWAYAT -->
            <div class="card shadow">
                <div class="card-header bg-dark text-white fw-bold">
                    Riwayat Peminjaman
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>No</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no=1; while($r=mysqli_fetch_assoc($riwayat)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($r['judul']) ?></td>
                                <td><?= date('d M Y', strtotime($r['tgl_pinjam'])) ?></td>
                                <td><?= date('d M Y', strtotime($r['tgl_kembali'])) ?></td>
                                <td>
                                    <span class="badge <?= $r['status']=='dipinjam'?'bg-warning text-dark':'bg-success' ?>">
                                        <?= $r['status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KANAN -->
        <div class="col-md-4">

            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white fw-bold">Profil</div>
                <div class="card-body">
                    <p><b>Username</b><br><?= htmlspecialchars($profil['username']) ?></p>
                    <p><b>Role</b><br><?= htmlspecialchars($profil['role']) ?></p>
                    <a href="profil.php" class="btn btn-outline-primary w-100 btn-sm">Edit Profil</a>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white fw-bold">Aksi Cepat</div>
                <div class="list-group list-group-flush">
                    <a href="peminjaman/pinjam_siswa.php" class="list-group-item list-group-item-action">Pinjam Buku</a>
                    <a href="peminjaman/riwayat.php" class="list-group-item list-group-item-action">Riwayat Detail</a>
                </div>
            </div>

        </div>

    </div>
</div>
</body>
</html>