<?php
require "koneksi.php";

// baca data dari kiriman form: form_guest.html
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];

// sql to insert record
$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('$fname', '$lname', '$email')";

if (mysqli_query($conn, $sql)) {
    // Jika berhasil, langsung dialihkan kembali ke bacadata2.php
    header('Location: bacadata2.php');
    exit();
} else {
    echo "Error inserting record: " . mysqli_error($conn);
}

mysqli_close($conn);
?>