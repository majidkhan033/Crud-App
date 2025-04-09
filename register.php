<?php

session_start();
require 'db.php';

if (isset($_POST['signup'])) {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $existing = $collection->findOne(['email' => $email]);

    if ($existing){
        $error = "Email already registered!";
    }
    else {
        $collection->insertOne([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'mobile' => $mobile,
            'email' => $email, 
            'password' => $password
        ]);
        $_SESSION['msg'] = "User added!";
        $_SESSION['user'] = (string) $email;
        header("Location: login.php");
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
    <input type="text" name="first_name" required placeholder="First Name">
        <input type="text" name="last_name" required placeholder="Last Name">
        <input type="text" name="mobile" required placeholder="Mobile Number">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Password">
        <button name="signup">Sign Up</button>
    </form>

    <p class="acc-detail">Already have an account? <a href="index.php">Login</a></p>
</div>
</body>
</html>