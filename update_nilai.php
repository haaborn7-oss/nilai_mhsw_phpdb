
<?php
require_once __DIR__ . "/koneksi.php";

// Pastikan data dikirim melalui POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: adminlte.php");
    exit();
}

// Ambil data dari form
$nim = trim($_POST['nim'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$input_nilai = trim($_POST['nilai_akhir'] ?? '');

// Validasi data
if (
    !preg_match('/^[0-9]{10}$/', $nim) ||
    $nama_lengkap === '' ||
    preg_match('/[0-9]/', $nama_lengkap) ||
    $input_nilai === '' ||
    !is_numeric(str_replace(',', '.', $input_nilai))
) {
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("Data tidak valid. Periksa nama dan nilai."));
    exit();
}

$nilai_akhir = (float) str_replace(',', '.', $input_nilai);

if ($nilai_akhir < 0 || $nilai_akhir > 100) {
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("Nilai harus antara 0 dan 100."));
    exit();
}

// Tentukan nilai huruf dan predikat
if ($nilai_akhir <= 34.50) {
    $nilai_huruf = "E";
    $predikat = "Tidak Lulus";
} elseif ($nilai_akhir <= 50.50) {
    $nilai_huruf = "D";
    $predikat = "Kurang";
} elseif ($nilai_akhir <= 65.50) {
    $nilai_huruf = "C";
    $predikat = "Cukup";
} elseif ($nilai_akhir <= 80.50) {
    $nilai_huruf = "B";
    $predikat = "Memuaskan";
} else {
    $nilai_huruf = "A";
    $predikat = "Sangat Memuaskan";
}

// Periksa apakah NIM terdaftar
$cek = $conn->prepare("SELECT nim FROM nilaimhsw WHERE nim = ?");

if (!$cek) {
    die("Gagal menyiapkan query: " . $conn->error);
}

$cek->bind_param("s", $nim);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    $cek->close();
    $conn->close();

    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("Data mahasiswa tidak ditemukan."));
    exit();
}

$cek->close();

// Update data mahasiswa
$stmt = $conn->prepare(
    "UPDATE nilaimhsw
     SET nama_lengkap = ?, nilai_akhir = ?,
         nilai_huruf = ?, predikat = ?
     WHERE nim = ?"
);

if (!$stmt) {
    die("Gagal menyiapkan query: " . $conn->error);
}

$stmt->bind_param(
    "sdsss",
    $nama_lengkap,
    $nilai_akhir,
    $nilai_huruf,
    $predikat,
    $nim
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header(
        "Location: adminlte.php?tipe=success&msg=" .
        urlencode("Data nilai berhasil diperbarui.")
    );
    exit();
}

$error = $stmt->error;
$stmt->close();
$conn->close();

die("Data gagal diperbarui: " . htmlspecialchars($error));
?>
