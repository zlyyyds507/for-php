<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = intval($_GET['order_id'] ?? 0);
// 校验订单归属
$sql = "SELECT * FROM `order` WHERE id = $order_id AND user_id = {$_SESSION['user_id']}";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    echo "订单不存在或无权操作！";
    exit;
}
$order = $result->fetch_assoc();

// 仅待付款订单可支付
if ($order['status'] !== 'pending') {
    echo "订单状态异常，无法支付！";
    exit;
}

// 模拟支付过程（此处直接更改状态即可）
$conn->query("UPDATE `order` SET status='paid', pay_time=NOW() WHERE id=$order_id");

// 跳转支付成功页或回个人中心
header("Location: user.php?pay_success=1");
exit;
?>