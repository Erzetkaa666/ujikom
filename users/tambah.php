<?php
include '../koneksi.php';
if(isset($_POST['simpan'])){
mysqli_query($conn,"INSERT INTO anggota VALUES('',
'$_POST[nama]','$_POST[alamat]','$_POST[no_hp]')");
header("location:anggota.php");
}
?>
<form method="post">
Nama: <input name="nama"><br>
Alamat: <input name="alamat"><br>
No HP: <input name="no_hp"><br>
<button name="simpan">Simpan</button>
</form>