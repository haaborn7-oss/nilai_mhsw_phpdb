<?php
// Hubungkan ke database untuk mengambil data berdasarkan ID
require_once "koneksi.php";

// baca data 1 record yang dipilih untuk diedit (tambahkan huruf 's' pada MyGuests)
$sql = "SELECT * FROM MyGuests WHERE id = $_GET[id]";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $id = $row["id"];
        $fname = $row["firstname"];
        $lname = $row["lastname"];
        $email = $row["email"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Edit Guest</title>
</head>
<body>
    <h2>Form Edit My Guest</h2>
    <form action="updateguest.php" method="POST">
        <label for="id">ID:</label><br>
        <input type="text" name="id" value="<?php echo $id; ?>" readonly><br><br>

        <label for="fname">First Name:</label><br>
        <input type="text" name="fname" value="<?php echo $fname; ?>"><br><br>

        <label for="lname">Last Name:</label><br>
        <input type="text" name="lname" value="<?php echo $lname; ?>"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" value="<?php echo $email; ?>"><br><br>

        <input type="reset" value="Cancel">
        <input type="submit" value="Update">
    </form>
</body>
</html>

<?php
mysqli_close($conn);
?>