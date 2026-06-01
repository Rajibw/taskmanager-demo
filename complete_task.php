<?php
include(__DIR__ . '/../config/db.php');

$id = $_GET['id'];

$conn->query("UPDATE tasks SET status='completed' WHERE id=$id");

header("Location: dashboard.php");
?>