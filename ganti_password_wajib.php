<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$error = '';
$success = '';

if (isset($_POST['ganti'])) {
    $p1 = $_POST['p1'];
    $p2 = $_POST['p2'];

    if ($p1 != $p2) {
        $error = "Password tidak sama!";
    } else {
        $hash = password_hash($p1, PASSWORD_DEFAULT);

        mysqli_query($koneksi, "
            UPDATE users 
            SET password='$hash', wajib_ganti_password=0
            WHERE id_user='$id_user'
        ");

        session_destroy();
        header("Location: login.php?msg=gantiberhasil");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Ganti Password Wajib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height:100vh">

<div class="card shadow" style="width:400px">
    <div class="card-header bg-danger text-white text-center">
        ⚠️ Wajib Ganti Password
    </div>
    <div class="card-body">
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form method="post">
            <input type="password" name="p1" class="form-control mb-3" placeholder="Password Baru" required>
            <input type="password" name="p2" class="form-control mb-3" placeholder="Ulangi Password" required>
            <button name="ganti" class="btn btn-primary w-100">Simpan Password</button>
        </form>
    </div>
</div>

</body>
</html>