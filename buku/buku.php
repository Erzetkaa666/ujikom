<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role']!='admin') exit;

/* TAMBAH */
if(isset($_POST['tambah'])){
    mysqli_query($koneksi,"INSERT INTO buku
    (judul,pengarang,tahun_terbit,kategori,stok)
    VALUES
    ('$_POST[judul]','$_POST[pengarang]','$_POST[tahun]','$_POST[kategori]','$_POST[stok]')");
    header("Location: buku.php");
}

/* HAPUS */
if(isset($_GET['hapus'])){
    mysqli_query($koneksi,"DELETE FROM buku WHERE id_buku='$_GET[hapus]'");
    header("Location: buku.php");
}

$buku=mysqli_query($koneksi,"SELECT * FROM buku ORDER BY judul");
?>

<!DOCTYPE html>
<html>
<head>
<title>Kelola Buku</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">

<div class="card shadow mb-4">
<div class="card-header bg-primary text-white">Tambah Buku</div>
<div class="card-body">
<form method="post" class="row g-2">
<input name="judul" class="form-control col" placeholder="Judul" required>
<input name="pengarang" class="form-control col" placeholder="Pengarang" required>
<input name="tahun" type="number" class="form-control col" placeholder="Tahun" required>
<input name="kategori" class="form-control col" placeholder="Kategori (Novel,dll)" required>
<input name="stok" type="number" class="form-control col" placeholder="Stok" required>
<button name="tambah" class="btn btn-success col-auto">Tambah</button>
</form>
</div>
</div>

<div class="card shadow">
<table class="table table-bordered mb-0">
<tr class="table-dark">
<th>Judul</th><th>Pengarang</th><th>Tahun</th><th>Kategori</th><th>Stok</th><th>Aksi</th>
</tr>
<?php while($b=mysqli_fetch_assoc($buku)): ?>
<tr>
<td><?= $b['judul'] ?></td>
<td><?= $b['pengarang'] ?></td>
<td><?= $b['tahun_terbit'] ?></td>
<td><?= $b['kategori'] ?></td>
<td><?= $b['stok'] ?></td>
<td>
<a href="?hapus=<?= $b['id_buku'] ?>" class="btn btn-danger btn-sm">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>