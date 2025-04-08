<?php

session_start();
require 'db.php';

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $existing = $collection->findOne(['email => $email']);

    if ($existing){
        $error = "Email already registered!";
    }
    else {
        $collection->insertOne(['email' => $email, 'password' => $password]);
        $_SESSION['user'] = (string) $email;
        header("Location: dashboard.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Sign Up</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Password">
        <button name="signup">Sign Up</button>
    </form>

    <p>Already have an account? <a href="index.php">Login</a></p>
</div>
</body>
</html>