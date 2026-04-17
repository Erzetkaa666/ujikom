<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role']!='admin'){
    header("Location: ../login.php");
    exit;
}

if(isset($_POST['simpan'])){
    mysqli_query($koneksi,"INSERT INTO buku 
        (judul, pengarang, tahun_terbit, kategori, stok)
        VALUES
        ('$_POST[judul]','$_POST[pengarang]','$_POST[tahun_terbit]','$_POST[kategori]','$_POST[stok]')
    ");

    header("Location: buku.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">➕ Tambah Buku Baru</h5>
        </div>

        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pengarang</label>
                    <input type="text" name="pengarang" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="buku.php" class="btn btn-secondary">Kembali</a>
                    <button name="simpan" class="btn btn-primary">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>