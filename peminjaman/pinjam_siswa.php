<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

/* ================= PROSES PINJAM ================= */
if (isset($_POST['pinjam'])) {
    $id_buku = (int) $_POST['id_buku'];
    $tgl_kembali = $_POST['tgl_kembali'];

    mysqli_begin_transaction($koneksi);
    try {
        $buku = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT stok FROM buku WHERE id_buku='$id_buku' FOR UPDATE"
        ));

        if ($buku['stok'] <= 0) throw new Exception("Stok habis");

        mysqli_query($koneksi, "
            INSERT INTO peminjaman (id_user,id_buku,tgl_pinjam,tgl_kembali,status)
            VALUES ('$id_user','$id_buku',CURDATE(),'$tgl_kembali','dipinjam')
        ");

        mysqli_query($koneksi, "
            UPDATE buku SET stok=stok-1 WHERE id_buku='$id_buku'
        ");

        mysqli_commit($koneksi);
        $success = "Buku berhasil dipinjam!";
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $error = $e->getMessage();
    }
}

/* ================= SEARCH ================= */
$cari = $_GET['cari'] ?? '';
$query = "
SELECT * FROM buku
WHERE 
    judul LIKE '%$cari%' OR
    pengarang LIKE '%$cari%' OR
    kategori LIKE '%$cari%' OR
    tahun_terbit LIKE '%$cari%'
ORDER BY judul
";
$buku = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Katalog Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
<nav class="navbar navbar-dark bg-primary shadow">
    <div class="container">
        <a href="../siswa_dashboard.php" class="navbar-brand">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <span class="text-white fw-bold">Katalog Buku Perpustakaan</span>
    </div>
</nav>

<div class="container mt-4">

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- SEARCH -->
    <form class="mb-3">
        <div class="input-group">
            <input type="text" name="cari" class="form-control"
                   placeholder="Cari judul, pengarang, kategori, tahun..."
                   value="<?= htmlspecialchars($cari) ?>">
            <button class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
            </button>
        </div>
    </form>

    <!-- TABEL BUKU -->
    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Tahun</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($b=mysqli_fetch_assoc($buku)): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['judul']) ?></td>
                        <td><?= htmlspecialchars($b['pengarang']) ?></td>
                        <td><?= $b['tahun_terbit'] ?></td>
                        <td><span class="badge bg-info"><?= $b['kategori'] ?></span></td>
                        <td>
                            <span class="badge <?= $b['stok']>0?'bg-success':'bg-danger' ?>">
                                <?= $b['stok'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if($b['stok']>0): ?>
                            <form method="post" class="d-flex gap-1">
                                <input type="hidden" name="id_buku" value="<?= $b['id_buku'] ?>">
                                <input type="date" name="tgl_kembali"
                                       min="<?= date('Y-m-d', strtotime('+7 days')) ?>"
                                       class="form-control form-control-sm" required>
                                <button name="pinjam" class="btn btn-success btn-sm">
                                    Pinjam
                                </button>
                            </form>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Habis</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>