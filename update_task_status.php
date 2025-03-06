<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $status = intval($_POST['status']);

    $query = "UPDATE todo SET status = $status WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "Status task berhasil diperbarui";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
