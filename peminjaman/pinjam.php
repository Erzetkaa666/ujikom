<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================== PROSES KEMBALIKAN ================== */
if (isset($_GET['kembali'])) {

    $id = (int) $_GET['kembali'];

    // Ambil data pinjam + pastikan masih dipinjam
    $ambil = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT id_buku, status 
        FROM peminjaman 
        WHERE id_pinjam='$id'
    "));

    if ($ambil && $ambil['status'] == 'dipinjam') {

        mysqli_begin_transaction($koneksi);

        // Update status
        mysqli_query($koneksi, "
            UPDATE peminjaman 
            SET 
                status='kembali',
                tgl_kembali_asli = NOW()
            WHERE id_pinjam='$id'
        ");

        // Kembalikan stok
        mysqli_query($koneksi, "
            UPDATE buku 
            SET stok = stok + 1 
            WHERE id_buku='{$ambil['id_buku']}'
        ");

        mysqli_commit($koneksi);
    }

    header("Location: pinjam.php");
    exit;
}

/* ================== DATA PINJAM AKTIF ================== */
$peminjaman = mysqli_query($koneksi, "
    SELECT p.*, u.username, b.judul
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id_user
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.status = 'dipinjam'
    ORDER BY p.tgl_pinjam DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-arrow-repeat me-2"></i>Kelola Peminjaman Aktif
            </h5>
        </div>

        <div class="card-body">

            <?php if(mysqli_num_rows($peminjaman) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="60">No</th>
                            <th>Siswa</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat</th>
                            <th>Status</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($p = mysqli_fetch_assoc($peminjaman)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['username']) ?></td>
                            <td><?= htmlspecialchars($p['judul']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['tgl_pinjam'])) ?></td>
                            <td>
                                <span class="badge <?= (date('Y-m-d') > $p['tgl_kembali']) ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                    <?= date('d/m/Y', strtotime($p['tgl_kembali'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= htmlspecialchars($p['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="pinjam.php?kembali=<?= $p['id_pinjam'] ?>"
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Yakin buku sudah dikembalikan?')">
                                   <i class="bi bi-check-circle"></i> Kembalikan
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-3">Tidak ada peminjaman aktif.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>