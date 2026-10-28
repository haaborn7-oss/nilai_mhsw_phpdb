
<?php
require_once __DIR__ . "/koneksi.php";

// Memastikan data dikirim melalui POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: adminlte.php");
    exit();
}

// Mengambil data dari form
$nim = trim($_POST["nim"] ?? "");
$nama_lengkap = trim($_POST["nama_lengkap"] ?? "");
$input_nilai = trim($_POST["nilai_akhir"] ?? "");

// Validasi NIM: tepat 10 angka
if (!preg_match('/^[0-9]{10}$/', $nim)) {
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("NIM harus terdiri dari tepat 10 angka."));
    exit();
}

// Validasi nama
if ($nama_lengkap === "" || preg_match('/[0-9]/', $nama_lengkap)) {
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("Nama lengkap tidak boleh kosong atau mengandung angka."));
    exit();
}

// Validasi nilai
if ($input_nilai === "" || !is_numeric(str_replace(",", ".", $input_nilai))) {
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("Nilai akhir harus berupa angka."));
    exit();
}

$nilai_akhir = (float) str_replace(",", ".", $input_nilai);

if ($nilai_akhir < 0 || $nilai_akhir > 100) {
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("Nilai harus antara 0 dan 100."));
    exit();
}

// Menentukan nilai huruf dan predikat
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

// Cek apakah NIM sudah digunakan
$cek = $conn->prepare("SELECT nim FROM nilaimhsw WHERE nim = ?");

if (!$cek) {
    die("Query pengecekan gagal: " . $conn->error);
}

$cek->bind_param("s", $nim);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    $cek->close();
    $conn->close();

    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("NIM sudah terdaftar."));
    exit();
}

$cek->close();

// Menambahkan data baru
$sql = "INSERT INTO nilaimhsw
        (nim, nama_lengkap, nilai_akhir, nilai_huruf, predikat, entry_date)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query penambahan gagal: " . $conn->error);
}

$stmt->bind_param(
    "ssdss",
    $nim,
    $nama_lengkap,
    $nilai_akhir,
    $nilai_huruf,
    $predikat
);

// Menyimpan data
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header(
        "Location: adminlte.php?tipe=success&msg=" .
        urlencode("Data nilai berhasil ditambahkan.")
    );
    exit();
}

$error = $stmt->error;
$stmt->close();
$conn->close();

die("Data gagal ditambahkan: " . htmlspecialchars($error));
?>
