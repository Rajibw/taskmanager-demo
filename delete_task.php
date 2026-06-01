<?php
include(__DIR__ . '/../config/db.php');

$id = $_GET['id'];

$conn->query("DELETE FROM tasks WHERE id=$id");

header("Location: dashboard.php");
?>