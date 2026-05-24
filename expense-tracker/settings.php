<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* UPDATE USERNAME */
    if (!empty($_POST['username'])) {
        $newUser = $_POST['username'];
        $conn->query("UPDATE users SET username='$newUser' WHERE username='{$_SESSION['user']}'");
        $_SESSION['user'] = $newUser;
        $message = "Username updated!";
    }

    /* UPDATE PASSWORD */
    if (!empty($_POST['password']) && $_POST['password'] == $_POST['confirm']) {
        $pass = $_POST['password'];
        $conn->query("UPDATE users SET password='$pass' WHERE username='{$_SESSION['user']}'");
        $message = "Password updated!";
    }

    /* DELETE ALL */
    if (isset($_POST['delete_all'])) {
        $conn->query("DELETE FROM expenses");
        $message = "All expenses deleted!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Settings</title>
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class="app">

<aside class="sidebar">
    <h2 class="logo">💰 Tracker</h2>
    <div class="menu">
        <a href="index.php">🏠 Dashboard</a>
        <a href="reports.php">📊 Reports</a>
        <a href="settings.php" class="active">⚙️ Settings</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
</aside>

<main class="main">

<h2 class="page-title">⚙️ Settings</h2>
<p class="page-subtitle">Manage your account preferences</p>

<div class="form-box">
<form method="POST">
    <input type="text" name="username" placeholder="New Username">

    <input type="password" name="password" placeholder="New Password">
    <input type="password" name="confirm" placeholder="Confirm Password">

    <button type="submit">Save Changes</button>
</form>
</div>

<div class="form-box">
<form method="POST">
    <button name="delete_all" style="background:red;">
        🗑 Delete All Expenses
    </button>
</form>
</div>

<p style="color:lightgreen; margin-top:10px;">
<?= $message ?>
</p>

</main>
</div>

</body>
</html>