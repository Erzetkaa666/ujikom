<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['reset'])) {
    $id = (int) $_GET['reset'];
    $pass = password_hash('123456', PASSWORD_DEFAULT);

    mysqli_query($koneksi, "
        UPDATE users SET 
        password='$pass',
        wajib_ganti_password=1
        WHERE id_user='$id' AND role='siswa'
    ");

    $msg = "Password berhasil direset ke default.";
}

$siswa = mysqli_query($koneksi, "SELECT * FROM users WHERE role='siswa'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Reset Password Siswa</h3>

    <?php if(isset($msg)): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php $no=1; while($s=mysqli_fetch_assoc($siswa)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $s['username'] ?></td>
            <td>
                <?= $s['wajib_ganti_password'] ? 'Belum diganti' : 'Aman' ?>
            </td>
            <td>
                <a href="?reset=<?= $s['id_user'] ?>" 
                   onclick="return confirm('Reset password siswa ini?')"
                   class="btn btn-danger btn-sm">Reset</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="alert alert-info">
        Password default setelah reset: <b>123456</b>
    </div>
</div>
</body>
</html>