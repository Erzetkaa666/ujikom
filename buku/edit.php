<?php
include '../koneksi.php';
$id=$_GET['id'];
$data=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM buku WHERE id_buku='$id'"));

if(isset($_POST['update'])){
mysqli_query($conn,"UPDATE buku SET
judul='$_POST[judul]',
pengarang='$_POST[pengarang]',
penerbit='$_POST[penerbit]',
tahun='$_POST[tahun]',
stok='$_POST[stok]'
WHERE id_buku='$id'");
header("location:buku.php");
}
?>
<form method="post">
Judul: <input name="judul" value="<?= $data['judul']; ?>"><br>
Pengarang: <input name="pengarang" value="<?= $data['pengarang']; ?>"><br>
Penerbit: <input name="penerbit" value="<?= $data['penerbit']; ?>"><br>
Tahun: <input name="tahun" value="<?= $data['tahun']; ?>"><br>
Stok: <input name="stok" value="<?= $data['stok']; ?>"><br>
<button name="update">Update</button>
</form>