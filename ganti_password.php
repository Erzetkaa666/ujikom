<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

if (isset($_POST['ganti'])) {
    $baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

    mysqli_query($koneksi, "
        UPDATE users SET 
            password='$baru',
            wajib_ganti_password=0
        WHERE id_user='$id_user'
    ");

    session_destroy();
    header("Location: login.php?msg=Password berhasil diganti, silakan login.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ganti Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh">
<div class="container col-md-4">
    <div class="card shadow">
        <div class="card-header bg-danger text-white text-center">
            Wajib Ganti Password
        </div>
        <div class="card-body">
            <form method="POST">
                <label>Password Baru</label>
                <input type="password" name="password_baru" class="form-control mb-3" required>
                <button name="ganti" class="btn btn-primary w-100">Simpan Password</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>