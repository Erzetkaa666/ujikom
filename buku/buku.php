<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role']!='admin'){
    header("Location: ../login.php");
    exit;
}

// Hapus buku
if(isset($_GET['hapus'])){
    mysqli_query($koneksi,"DELETE FROM buku WHERE id_buku='$_GET[hapus]'");
    header("Location: buku.php");
    exit;
}

$buku = mysqli_query($koneksi,"SELECT * FROM buku ORDER BY judul");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>📚 Data Buku</h3>
        <a href="tambah.php" class="btn btn-primary">+ Tambah Buku</a>
    </div>

    <table class="table table-bordered table-hover shadow">
        <tr class="table-dark">
            <th>No</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Tahun</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th width="150">Aksi</th>
        </tr>
        <?php $no=1; while($b=mysqli_fetch_assoc($buku)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $b['judul'] ?></td>
            <td><?= $b['pengarang'] ?></td>
            <td><?= $b['tahun_terbit'] ?></td>
            <td><?= $b['kategori'] ?></td>
            <td><?= $b['stok'] ?></td>
            <td>
                <a href="edit.php?id=<?= $b['id_buku'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?hapus=<?= $b['id_buku'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Hapus buku ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>