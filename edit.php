<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['id']) || !isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['priority']) || !isset($_POST['deadline'])) {
        echo "Data tidak dikirim dengan benar";
        exit();
    }

    $id = trim($_POST['id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = trim($_POST['priority']);
    $deadline = trim($_POST['deadline']);

    if (empty($title) || empty($description) || empty($priority) || empty($deadline)) {
        echo "Semua field harus diisi";
        exit();
    }

    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $priority = mysqli_real_escape_string($conn, $priority);
    $deadline = mysqli_real_escape_string($conn, $deadline);

    $allowed_priorities = ['high', 'medium', 'low'];
    if (!in_array($priority, $allowed_priorities)) {
        echo "Prioritas tidak valid";
        exit();
    }

    $query = "UPDATE todoo SET title='$title', description='$description', priority='$priority', deadline='$deadline' WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        echo "Task berhasil diperbarui";
        header("Location: index.php");
    } else {
        echo "Kesalahan database: " . mysqli_error($conn);
    }
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit();
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM todo WHERE id=$id");
$task = mysqli_fetch_assoc($result);

if (!$task) {
    echo "Task tidak ditemukan.";
    exit();
}

header("Content-Type: text/html");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Task</h1>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $task['id']; ?>">
            
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($task['description']); ?></textarea>

            <label for="priority">Priority:</label>
            <select id="priority" name="priority" required>
                <option value="high" <?= $task['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                <option value="medium" <?= $task['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="low" <?= $task['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
            </select>

            <label for="deadline">Deadline:</label>
            <input type="date" id="deadline" name="deadline" value="<?= htmlspecialchars($task['deadline']); ?>" required>

            <button type="submit">Update Task</button>
        </form>
        <a href="index.php">Back</a>
    </div>
</body>
</html>
