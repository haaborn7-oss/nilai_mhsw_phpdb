<?php
require "koneksi.php";

// sql to create table
$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('Nur', 'Annisa', 'Annisa@example.com')";

$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('John', 'Pang', 'John@example.com')";

if (mysqli_query($conn, $sql)) {
  echo "New record inserted successfully";
} else {
  echo "Error inserting table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
