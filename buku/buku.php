<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Tambah buku
if (isset($_POST['tambah'])) {
    mysqli_query($koneksi, "INSERT INTO buku (judul,pengarang,stok) VALUES (
        '$_POST[judul]','$_POST[pengarang]','$_POST[stok]'
    )");
    header("Location: buku.php");
}

// Hapus buku
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$_GET[hapus]'");
    header("Location: buku.php");
}

$buku = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY judul");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3 class="mb-3">📚 Data Buku</h3>

    <form method="post" class="row g-2 mb-4">
        <div class="col"><input name="judul" class="form-control" placeholder="Judul" required></div>
        <div class="col"><input name="pengarang" class="form-control" placeholder="Pengarang" required></div>
        <div class="col"><input name="stok" type="number" class="form-control" placeholder="Stok" required></div>
        <div class="col-auto"><button name="tambah" class="btn btn-primary">Tambah</button></div>
    </form>

    <table class="table table-bordered">
        <tr><th>Judul</th><th>Pengarang</th><th>Stok</th><th>Aksi</th></tr>
        <?php while($b = mysqli_fetch_assoc($buku)): ?>
        <tr>
            <td><?= $b['judul'] ?></td>
            <td><?= $b['pengarang'] ?></td>
            <td><?= $b['stok'] ?></td>
            <td>
                <a href="?hapus=<?= $b['id_buku'] ?>" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>