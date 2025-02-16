<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
$stmt->bindParam(':id', $id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);

if ($stmt->execute()) {
    $_SESSION['message'] = "Task deleted successfully!";
    header("Location: dashboard.php");
    exit();
} else {
    $_SESSION['error'] = "Error deleting task. Please try again.";
    header("Location: dashboard.php");
    exit();
}
?>