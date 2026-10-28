<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db5d";

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Cek koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM MyGuests";
// Execute the SQL query
$result = mysqli_query($conn, $sql);

// Process the SQL query
if (mysqli_num_rows($result) > 0) {
    // Tombol untuk tambah data guest dibungkus tag <a> agar bisa ditekan
    echo "<a href='form_guest.html'><button>Add My Guest</button></a><br><br>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<caption><strong> My Guest List</strong></caption>";
    echo "<tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Reg Date</th>
        <th>Action</th>
        </tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>".$row["id"]."</td>
            <td>".$row["firstname"]." ".$row["lastname"]."</td>
            <td>".$row["email"]."</td>
            <td>".$row["reg_date"]."</td>
            <td> <a href='form_editguest.php?id=".$row['id']."'>
                    <button>Edit</button>
                </a>
            <a href='hapusguest.php?id=".$row['id']."'
            onclick='return confirm(\"Apakah anda yakin ingin menghapus data ini\");'>
            <button>Delete</button>
            </a>
        </td>
        </tr>";
    }
    echo "</table>"; 
} else {
    echo "0 results - Data not found!";
}

mysqli_close($conn);
?>