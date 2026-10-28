<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db5d";

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Cek koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM nilaimhsw";
// Exsekusi sql
$result = mysqli_query($conn, $sql);

// memproses sql
if (mysqli_num_rows($result) > 0) {
    // Tombol untuk tambah data nilai dibungkus tag <a> agar bisa ditekan
    echo "<a href='form_nilai.html'><button>Tambah Data Nilai</button></a><br><br>";
    echo "<table border='1' style='border-collapse: collapse;' cellpadding='8' cellspacing='0'>";
    echo "<caption><strong>Rekap Nilai Mahasiswa</strong></caption>";
    echo "<tr>
        <th>NIM</th>
        <th>Nama Lengkap</th>
        <th>Nilai Akhir</th>
        <th>Nilai Huruf</th>
        <th>Predikat</th>
        <th>Entry Date</th>
        <th>Aksi</th>
        </tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>".$row["nim"]."</td>
            <td>".$row["nama_lengkap"]."</td>
            <td>".$row["nilai_akhir"]."</td>
            <td>".$row["nilai_huruf"]."</td>
            <td>".$row["predikat"]."</td>
            <td>".$row["entry_date"]."</td>
            <td> 
                <a href='form_editnilai.php?id=".$row['nim']."'>
                    <button>Edit</button>
                </a>
                <a href='hapus_nilai.php?id=".$row['nim']."'
                onclick='return confirm(\"Apakah anda yakin ingin menghapus data ini\");'>
                <button>Hapus</button>
                </a>
            </td>
            </tr>";
    }
    echo "</table>"; 
} else {
    echo "0 results - Data not found!";
}

mysqli_close($conn);
?>