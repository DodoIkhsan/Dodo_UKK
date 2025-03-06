<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST['task_id'];
    $subtask_title = trim($_POST['subtask_title']);

    if (empty($subtask_title)) {
        echo "Subtask tidak boleh kosong";
        exit();
    }

    $subtask_title = mysqli_real_escape_string($conn, $subtask_title);
    
    $query = "INSERT INTO subtasks (task_id, subtask_title) VALUES ('$task_id', '$subtask_title')";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
