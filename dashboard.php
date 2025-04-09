<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Get the currently logged in user details
$currentUser = $collection->findOne(['email' => $_SESSION['user']]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Welcome to the Dashboard</h2>

    <div class="user-info">
        <p><strong>Email:</strong> <?= htmlspecialchars($currentUser['email'] ?? '-') ?></p>
    </div>
    <br>
    <p>You are logged in!</p>
    <a href="users_crud.php"><button>Manage Users</button></a>
    <a href="logout.php"><button style="background-color: #dc3545;">Logout</button></a>
</div>
</body>
</html>