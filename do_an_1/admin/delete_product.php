<?php
session_start();
include('../db.php');

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: product_list.php");
exit;
?>
