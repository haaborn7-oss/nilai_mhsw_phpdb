<?php
require_once __DIR__ . "/koneksi.php"; // Atau sesuaikan dengan file koneksi Anda

    // SQL to delete a record berdasarkan ID yang dipilih
    $sql = "DELETE FROM MyGuests WHERE id = $_GET[id]";

    if (mysqli_query($conn, $sql)) {
        // Jika penghapusan berhasil
        echo "<script>alert('Record deleted successfully');</script>";
    }
    // jika penghapusan gagal 
    else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

 // Jika tidak ada ID yang dikirim, langsung kembalikan ke halaman data
    header('Location: bacadata2.php');

// mysqli_close($conn);
?>