<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $sql = "INSERT INTO expenses (amount, category, description, date)
            VALUES ('$amount', '$category', '$description', '$date')";

    $conn->query($sql);

    header("Location: index.php");
}
?>