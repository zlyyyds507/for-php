<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$order_id = intval($_GET['order_id']);
$user_id = intval($_SESSION['user_id']);

$sql = "UPDATE `orders` SET status='completed', confirm_time=NOW() WHERE id=$order_id AND user_id=$user_id AND status='shipped'";
$conn->query($sql);
header("Location: user.php");
exit;
?>
