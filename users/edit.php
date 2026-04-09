<?php
include '../koneksi.php';
$id=$_GET['id'];
$data=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM anggota WHERE id_anggota='$id'"));

if(isset($_POST['update'])){
mysqli_query($conn,"UPDATE anggota SET
nama='$_POST[nama]',
alamat='$_POST[alamat]',
no_hp='$_POST[no_hp]'
WHERE id_anggota='$id'");
header("location:anggota.php");
}
?>
<form method="post">
Nama: <input name="nama" value="<?= $data['nama']; ?>"><br>
Alamat: <input name="alamat" value="<?= $data['alamat']; ?>"><br>
No HP: <input name="no_hp" value="<?= $data['no_hp']; ?>"><br>
<button name="update">Update</button>
</form>