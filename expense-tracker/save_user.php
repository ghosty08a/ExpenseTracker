<?php
include 'db.php';

$username = $_POST['username'];
$password = md5($_POST['password']); // simple for school

// check if user exists
$check = $conn->query("SELECT * FROM users WHERE username='$username'");

if ($check->num_rows > 0) {
    echo "Username already taken!";
} else {
    $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$password')");
    header("Location: login.php");
}
?>