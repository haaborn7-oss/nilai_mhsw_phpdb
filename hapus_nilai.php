
<?php
require_once __DIR__ . "/koneksi.php";

// Memastikan data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: adminlte.php");
    exit();
}

// Mengambil NIM dari form
$nim = trim($_POST['nim'] ?? '');

if ($nim === '') {
    die("NIM tidak ditemukan.");
}

// Menyiapkan perintah hapus data
$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM nilaimhsw WHERE nim = ?"
);

if (!$stmt) {
    die("Query gagal disiapkan: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $nim);

// Menjalankan proses penghapusan
if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header(
        "Location: adminlte.php?tipe=success&msg=" .
        urlencode("Data nilai berhasil dihapus.")
    );
    exit();
} else {
    echo "Data gagal dihapus: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
