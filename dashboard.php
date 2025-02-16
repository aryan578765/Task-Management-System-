<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pseudo-code for pagination
$page = $_GET['page'] ?? 1;
$pageSize = 10;
$offset = ($page - 1) * $pageSize;
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :offset, :pageSize");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':pageSize', $pageSize, PDO::PARAM_INT);
$stmt->execute();
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <a href="add_task.php" class="btn btn-success mb-4">Add New Task</a>

        <input type="text" id="searchInput" placeholder="Search tasks...">

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= $task['status'] ?></td>
                        <td><?= $task['due_date'] ?></td>
                        <td>
                            <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        const filterTasks = (criteria) => {
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const title = row.querySelector('td:first-child').textContent.toLowerCase();
                const isVisible = title.includes(criteria.toLowerCase());
                row.style.display = isVisible ? '' : 'none';
            });
        };

        document.getElementById('searchInput').addEventListener('input', (event) => {
            filterTasks(event.target.value);
        });
    </script>
</body>
</html>