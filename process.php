<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    file_put_contents("debug.txt", print_r($_POST, true));

    if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['priority']) || !isset($_POST['deadline'])) {
        echo json_encode(["status" => "error", "message" => "Data tidak dikirim dengan benar"]);
        exit();
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = trim($_POST['priority']);
    $deadline = $_POST['deadline'];

    // Validasi input
    if (empty($title) || empty($description) || empty($priority) || empty($deadline)) {
        echo json_encode(["status" => "error", "message" => "Semua field harus diisi"]);
        exit();
    }

    // Mencegah SQL Injection
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $priority = mysqli_real_escape_string($conn, $priority);
    $deadline = mysqli_real_escape_string($conn, $deadline);

    // Pastikan prioritas yang dikirim adalah valid
    $allowed_priorities = ['high', 'medium', 'low'];
    if (!in_array($priority, $allowed_priorities)) {
        echo json_encode(["status" => "error", "message" => "Prioritas tidak valid"]);
        exit();
    }

    // Masukkan ke database
    $query = "INSERT INTO todo (title, description, priority, deadline) VALUES ('$title', '$description', '$priority', '$deadline')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success", "message" => "Task berhasil ditambahkan"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Kesalahan database: " . mysqli_error($conn)]);
    }
}
?>
