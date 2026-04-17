<?php
session_start();
include '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Hapus hanya siswa
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id' AND role='siswa'");
    header("Location: user.php");
}

$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role, username");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>👥 Data User</h3>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        <?php $no=1; while($u=mysqli_fetch_assoc($users)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $u['username'] ?></td>
            <td><?= $u['role'] ?></td>
            <td>
                <?php if($u['role']=='siswa'): ?>
                    <a href="?hapus=<?= $u['id_user'] ?>" 
                       onclick="return confirm('Hapus siswa ini?')"
                       class="btn btn-danger btn-sm">Hapus</a>
                <?php else: ?>
                    <span class="text-muted">Tidak bisa dihapus</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>