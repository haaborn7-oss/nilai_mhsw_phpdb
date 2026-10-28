<?php
require "koneksi.php";

// sql to create table nilaimhsw
$sql = "CREATE TABLE nilaimhsw (
    nim VARCHAR(10) PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    nilai_akhir DECIMAL(5,2) NOT NULL,
    nilai_huruf VARCHAR(5),
    predikat VARCHAR(50),
   entry_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>nilaimhsw</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>