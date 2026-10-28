<?php
// Hubungkan ke database untuk mengambil data berdasarkan NIM
require_once "koneksi.php";

// baca data 1 record yang dipilih untuk diedit berdasarkan nim
$sql = "SELECT * FROM nilaimhsw WHERE nim = '$_GET[id]'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $nim = $row["nim"];
        $nama_lengkap = $row["nama_lengkap"];
        $nilai_akhir = $row["nilai_akhir"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Edit Nilai Mahasiswa</title>
</head>
<body>
    <h2>Form Edit Nilai Mahasiswa</h2>
    <form action="edit_nilai.php" method="POST">
        <label for="nim">NIM:</label><br>
        <input type="text" name="nim" value="<?php echo $nim; ?>" readonly><br><br>

        <label for="nama_lengkap">Nama Lengkap:</label><br>
        <input type="text" name="nama_lengkap" value="<?php echo $nama_lengkap; ?>"><br><br>

        <label for="nilai_akhir">Nilai Akhir:</label><br>
        <input type="number" step="any" name="nilai_akhir" value="<?php echo $nilai_akhir; ?>"><br><br>

        <input type="reset" value="Cancel">
        <input type="submit" value="Update">
    </form>
</body>
</html>

<?php
mysqli_close($conn);
?>