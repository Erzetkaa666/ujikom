<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role']!='admin'){
    header("Location: ../login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: buku.php");
    exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM buku WHERE id_buku='$id'"));

if(!$data){
    header("Location: buku.php");
    exit;
}

/* ================= UPDATE ================= */
if(isset($_POST['update'])){
    mysqli_query($koneksi,"UPDATE buku SET
        judul='$_POST[judul]',
        pengarang='$_POST[pengarang]',
        tahun_terbit='$_POST[tahun_terbit]',
        kategori='$_POST[kategori]',
        stok='$_POST[stok]'
        WHERE id_buku='$id'
    ");

    header("Location: buku.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">✏️ Edit Data Buku</h5>
        </div>

        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="judul" class="form-control"
                        value="<?= $data['judul'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pengarang</label>
                    <input type="text" name="pengarang" class="form-control"
                        value="<?= $data['pengarang'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" class="form-control"
                        value="<?= $data['tahun_terbit'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control"
                        value="<?= $data['kategori'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control"
                        value="<?= $data['stok'] ?>" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="buku.php" class="btn btn-secondary">Kembali</a>
                    <button name="update" class="btn btn-warning">Update Buku</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>