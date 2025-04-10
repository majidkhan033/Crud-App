<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Fetch all users
$users = $collection->find();

// Handle CRUD actions

// Add User
if (isset($_POST['add'])) {
    $collection->insertOne([
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'mobile' => $_POST['mobile'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
    ]);
    $_SESSION['msg'] = "User added!";
    header("Location: users_crud.php");
    exit;
}

//Update User

if (isset($_POST['update'])) {
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_POST['id'])],
        [
            '$set' => [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'mobile' => $_POST['mobile'],
                'email' => $_POST['email']
            ]
        ]
    );
    $_SESSION['msg'] = "User updated!";
    header("Location: users_crud.php");
    exit;
}

// Delete User

if (isset($_GET['delete'])) {
    $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($_GET['delete'])]);
    $_SESSION['msg'] = "User deleted!";
    header("Location: users_crud.php");
    exit;
}

//bulk delete 
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_users'])) {
    foreach ($_POST['selected_users'] as $userId) {
        $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
    }
    $_SESSION['msg'] = "Selected users deleted!";
    header("Location: users_crud.php");
    exit;
}


//Edit User

$editUser = null;
if (isset($_GET['edit'])) {
    $editUser = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($_GET['edit'])]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>User Management</h2>

        <?php if (!empty($_SESSION['msg'])): ?>
            <p style="color: green;"><?= $_SESSION['msg'];
            unset($_SESSION['msg']); ?></p>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <form method="POST">
            <input type="hidden" name="id" value="<?= $editUser['_id'] ?? '' ?>">
            <input type="text" name="first_name" required placeholder="First Name"
                value="<?= $editUser['first_name'] ?? '' ?>">
            <input type="text" name="last_name" required placeholder="Last Name"
                value="<?= $editUser['last_name'] ?? '' ?>">
            <input type="text" name="mobile" required placeholder="Mobile Number"
                value="<?= $editUser['mobile'] ?? '' ?>">
            <input type="email" name="email" required placeholder="Email" value="<?= $editUser['email'] ?? '' ?>">
            <?php if (!$editUser): ?>
                <input type="password" name="password" required placeholder="Password">
                <button name="add">Add User</button>
            <?php else: ?>
                <button name="update">Update User</button>
                <a href="users_crud.php"><button type="button">Cancel</button></a>
            <?php endif; ?>
        </form>

        <!-- Bulk Delete Form with Users Table -->
        <form method="POST" id="bulk-delete-form">
            <button type="submit" name="bulk_delete" class="delete-btn" style="float: right; margin-bottom: 10px;">
                Delete Selected
            </button>


            <!-- Users Table -->
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>User ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_users[]" value="<?= $user['_id'] ?>"></td>
                            <td><?= htmlspecialchars($user['first_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($user['last_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($user['mobile'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['_id'] ?></td>
                            <td class="actions">
                                <a href="users_crud.php?edit=<?= $user['_id'] ?>"><button type="button">Edit</button></a>
                                <a href="users_crud.php?delete=<?= $user['_id'] ?>"
                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                    <button type="button" class="delete-btn">Delete</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </form>

        <br>
        <a href="dashboard.php"><button>← Back to Dashboard</button></a>
    </div>


    <script>
        document.getElementById('select-all').addEventListener('click', function () {
            const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
            for (const checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        // Intercept form submit for confirmation
    document.getElementById('bulk-delete-form').addEventListener('submit', function (e) {
        const selected = document.querySelectorAll('input[name="selected_users[]"]:checked');
        if (selected.length === 0) {
            alert("Please select at least one user to delete.");
            e.preventDefault(); // Stop form from submitting
        } else {
            if (!confirm("Are you sure you want to delete the selected users?")) {
                e.preventDefault(); // Stop form from submitting
            }
        }
    });

    </script>

</body>

</html>