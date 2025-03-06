<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "db_Ujikom";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi error: " . mysqli_connect_error());
}
?>
