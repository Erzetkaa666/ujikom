<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role, username");

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$_GET[hapus]'");
    header("Location: user.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3 class="mb-3">👥 Manajemen User</h3>

    <table class="table table-bordered">
        <tr><th>Username</th><th>Nama</th><th>Role</th><th>No HP</th><th>Aksi</th></tr>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td><?= $u['username'] ?></td>
            <td><?= $u['nama'] ?></td>
            <td><?= $u['role'] ?></td>
            <td><?= $u['no_hp'] ?></td>
            <td>
                <?php if($u['role']!='admin'): ?>
                <a href="?hapus=<?= $u['id_user'] ?>" class="btn btn-danger btn-sm">Hapus</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>