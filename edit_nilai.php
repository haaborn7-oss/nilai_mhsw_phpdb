
<?php
require_once __DIR__ . "/koneksi.php";

// Memeriksa metode pengiriman data
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require __DIR__ . "/update_nilai.php";
    exit();
}

// Mengambil NIM dari URL
$nim = trim($_GET['nim'] ?? '');

if ($nim === '') {
    echo "<script>
        alert('NIM tidak ditemukan!');
        window.location = 'adminlte.php';
    </script>";
    exit();
}

// Mengambil data mahasiswa dari database
$sql = "SELECT * FROM nilaimhsw WHERE nim = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query gagal disiapkan: " . $conn->error);
}

$stmt->bind_param("s", $nim);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

$stmt->close();

// Memeriksa ketersediaan data
if (!$data) {
    echo "<script>
        alert('Data tidak ditemukan!');
        window.location = 'adminlte.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Nilai Mahasiswa</title>
</head>

<body>

    <h2>Edit Nilai Mahasiswa</h2>

    <form action="update_nilai.php" method="POST">

        <!-- Input NIM -->
        <label for="nim">NIM:</label><br>
        <input
            type="text"
            id="nim"
            name="nim"
            value="<?php echo htmlspecialchars($data['nim']); ?>"
            readonly
        >
        <br><br>

        <!-- Input nama lengkap -->
        <label for="nama_lengkap">Nama Lengkap:</label><br>
        <input
            type="text"
            id="nama_lengkap"
            name="nama_lengkap"
            value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>"
            required
        >
        <br><br>

        <!-- Input nilai akhir -->
        <label for="nilai_akhir">Nilai Akhir:</label><br>
        <input
            type="number"
            id="nilai_akhir"
            name="nilai_akhir"
            value="<?php echo htmlspecialchars($data['nilai_akhir']); ?>"
            min="0"
            max="100"
            step="0.01"
            required
        >
        <br><br>

        <!-- Tombol menyimpan perubahan -->
        <button type="submit">Update</button>

        <!-- Tombol kembali -->
        <button
            type="button"
            onclick="window.location.href='adminlte.php'"
        >
            Batal
        </button>

    </form>

</body>
</html>
