<?php
include 'includes/utilities.php';
checkSessionAndRedirect();

session_start();
session_regenerate_id();
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (:user_id, :title, :description, :due_date)");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':due_date', $due_date);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Task added successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding task. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn:hover {
            background-color: #004085; /* Darker shade for hover */
            transition: background-color 0.3s ease-in-out;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add New Task</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form id="addTaskForm" method="POST" action="add_task.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="title" name="title" placeholder="Task Title" required>
                <label for="title">Task Title</label>
            </div>
            <div class="form-floating mb-3">
                <textarea class="form-control" id="description" name="description" placeholder="Description"></textarea>
                <label for="description">Description</label>
            </div>
            <div class="form-floating mb-3">
                <input type="date" class="form-control" id="due_date" name="due_date" placeholder="Due Date" required>
                <label for="due_date">Due Date</label>
            </div>
            <button type="submit" class="btn btn-success w-100 mt-3">Add Task</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <!-- table content -->
        </table>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        form.addEventListener('submit', (event) => {
            const title = document.getElementById('title').value;
            const dueDate = document.getElementById('due_date').value;
            let valid = true;

            if (!title.trim()) {
                alert('Title is required.');
                valid = false;
            }

            if (!dueDate) {
                alert('Due date is required.');
                valid = false;
            }

            if (!valid) {
                event.preventDefault(); // Prevent form submission
            }
        });
    });

    const showNotification = (message) => {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.className = 'alert alert-success';
        document.body.prepend(notification);
        setTimeout(() => notification.remove(), 3000);
    };

    document.getElementById('addTaskForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('add_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Task added successfully!');
                // Optionally clear the form or update the UI
            } else {
                showNotification('Failed to add task.');
            }
        })
        .catch(error => console.error('Error:', error));
    });
    </script>
</body>
</html>