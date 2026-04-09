<?php
include '../koneksi.php';
if(isset($_POST['simpan'])){
mysqli_query($conn,"INSERT INTO buku VALUES('',
'$_POST[judul]','$_POST[pengarang]','$_POST[penerbit]',
'$_POST[tahun]','$_POST[stok]')");
header("location:buku.php");
}
?>
<form method="post">
Judul: <input name="judul"><br>
Pengarang: <input name="pengarang"><br>
Penerbit: <input name="penerbit"><br>
Tahun: <input name="tahun"><br>
Stok: <input name="stok"><br>
<button name="simpan">Simpan</button>
</form>