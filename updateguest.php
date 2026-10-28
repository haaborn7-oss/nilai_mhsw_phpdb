<?php
// koneksi
require_once __DIR__. "/koneksi.php";

// Pastikan data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];

    // SQL to update a record
    $sql = "UPDATE MyGuests 
            SET firstname = '$fname',
                lastname = '$lname',
                email = '$email'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, redirect kembali ke halaman bacadata2.php
        header('Location: bacadata2.php');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>