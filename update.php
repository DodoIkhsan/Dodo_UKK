<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];

    if (empty($title) || empty($description) || empty($priority)) {
        echo "Title, Description, dan Priority tidak boleh kosong!";
        exit;
    }

    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $priority = mysqli_real_escape_string($conn, $priority);

    $query = "UPDATE todo SET title='$title', description='$description', priority='$priority' WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($conn);
    }
}
?>
