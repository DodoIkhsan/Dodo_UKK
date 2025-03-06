<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['title'], $_POST['description'], $_POST['priority'], $_POST['deadline'])) {
        echo "Data tidak dikirim dengan benar";
        exit();
    }

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

    $query = "INSERT INTO todo (user_id, title, description, priority, deadline, status) VALUES ('$user_id', '$title', '$description', '$priority', '$deadline', 0)";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
    } else {
        echo "Kesalahan database: " . mysqli_error($conn);
    }
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM todo WHERE user_id = '$user_id' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <h1>To-Do List</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
        
        <form id="todoForm" action="" method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="priority">Priority:</label>
            <select id="priority" name="priority" required>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>

            <label for="deadline">Deadline:</label>
            <input type="date" id="deadline" name="deadline" required>

            <button type="submit">Add Task</button>
        </form>

        <input type="text" id="searchTask" placeholder="Cari tugas..." onkeyup="searchTasks()">

        <div id="todoList">
            <h2>Tasks</h2>
            <div class="box">
                <ul id="tasks">
                    <?php while ($row = mysqli_fetch_assoc($result)) { 
                        $priority = isset($row['priority']) ? $row['priority'] : 'low';
                        $priorityClass = "priority-{$priority}";

                        $priorityText = "";
                        if ($priority === "high") {
                            $priorityText = "<span class='priority-text' style='color: red;'>[High]</span>";
                        } elseif ($priority === "medium") {
                            $priorityText = "<span class='priority-text' style='color: orange;'>[Medium]</span>";
                        } else {
                            $priorityText = "<span class='priority-text' style='color: green;'>[Low]</span>";
                        }

                        $deadline = isset($row['deadline']) ? $row['deadline'] : 'No deadline';
                    ?>
                        <li class="<?= $priorityClass ?>">
                            <div class="task-item" data-title="<?= htmlspecialchars($row['title']) ?>" 
                                data-description="<?= htmlspecialchars($row['description']) ?>">
                                <input type="checkbox" class="task-checkbox" 
                                    onchange="updateTaskStatus(<?= $row['id'] ?>, this)" 
                                    <?= $row['status'] ? 'checked' : '' ?>>

                                <div class="task-content">
                                    <strong class="<?= $row['status'] ? 'completed-task' : '' ?>" id="task-<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?> <?= $priorityText ?></strong>
                                    <p><?= htmlspecialchars($row['description']) ?></p>
                                    <span class="deadline" style="color: blue;">
                                        Deadline: <?= htmlspecialchars($deadline) ?>
                                    </span>
                                </div>

                                <div class="task-actions">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="delete-btn" 
                                    onclick="return confirm('Yakin mau hapus?')">Hapus</a>
                                </div>
                            </div>
                        </li>

                        <h4>Subtask</h4>
                        <ul class="subtask-list">
                            <?php 
                            $subtasks = mysqli_query($conn, "SELECT * FROM subtasks WHERE task_id = " . $row['id']);
                            while ($subtask = mysqli_fetch_assoc($subtasks)) { 
                            ?>
                                <li class="subtask-item">Nama Subtask : <?= htmlspecialchars($subtask['subtask_title']); ?>
                                    <a href="delete_subtask.php?id=<?= $subtask['id'] ?>" class="delete-subtask-btn" onclick="return confirm('Hapus subtask ini?')">Hapus</a>
                                </li>
                            <?php } ?>
                        </ul>

                        <form action="add_subtask.php" method="POST" class="subtask-form">
                                <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                                <input type="text" name="subtask_title" placeholder="Add Subtask" required>
                                <button type="submit">Add Subtask</button>
                            </form>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    function searchTasks() {
        let input = document.getElementById('searchTask').value.toLowerCase();
        let tasks = document.querySelectorAll('.task-item');
        
        tasks.forEach(task => {
            let title = task.getAttribute('data-title') ? task.getAttribute('data-title').toLowerCase() : "";
            let description = task.getAttribute('data-description') ? task.getAttribute('data-description').toLowerCase() : "";
            
            if (title.includes(input) || description.includes(input)) {
                task.closest('li').style.display = "block"; // Menampilkan task
            } else {
                task.closest('li').style.display = "none"; // Menyembunyikan task
            }
        });
    }

    function updateTaskStatus(taskId, checkbox) {
        let status = checkbox.checked ? 1 : 0;
        
        // Kirim status ke server
        fetch('update_task_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${taskId}&status=${status}`
        }).then(response => response.text()).then(data => {
            console.log(data);

            // Update tampilan teks secara langsung
            let taskContent = document.getElementById(`task-${taskId}`);
            if (taskContent) {
                if (status) {
                    taskContent.classList.add('completed-task');
                } else {
                    taskContent.classList.remove('completed-task');
                }
            }
        }).catch(error => console.error("Error:", error));
    }

    // Attach search function to input
    document.getElementById('searchTask').addEventListener('keyup', searchTasks);

    // Make functions available globally
    window.searchTasks = searchTasks;
    window.updateTaskStatus = updateTaskStatus;
});

    </script>
</body>
</html>