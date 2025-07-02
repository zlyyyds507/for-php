<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

// 查询用户基本信息和余额
$user_res = $conn->query("SELECT username, email, balance FROM user WHERE id = $user_id");
$user = $user_res->fetch_assoc();

// 订单状态映射
$status_map = [
    'pending' => '待付款',
    'paid' => '已支付',
    'shipped' => '已发货',
    'completed' => '已签收',
    'cancelled' => '已取消'
];

// 根据筛选条件获取订单
$status_filter = $_GET['status'] ?? '';
$where = "user_id = $user_id";
if ($status_filter == 'comment') {
    // 未评价的已完成订单
    $orders = [];
    $sql = "SELECT o.* FROM orders o 
            WHERE o.user_id=$user_id AND o.status='completed' 
            AND NOT EXISTS (SELECT 1 FROM comment c WHERE c.order_id=o.id AND c.user_id=$user_id)
            ORDER BY o.created_at DESC";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) $orders[] = $row;
} else if ($status_filter) {
    $orders = [];
    $sql = "SELECT * FROM orders WHERE user_id = $user_id AND status = '$status_filter' ORDER BY created_at DESC";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) $orders[] = $row;
} else {
    $orders = [];
    $sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) $orders[] = $row;
}

// 获取订单商品详情
foreach ($orders as &$order) {
    $item_sql = "SELECT oi.*, p.name AS product_name FROM order_item oi LEFT JOIN product p ON oi.product_id = p.id WHERE oi.order_id = {$order['id']}";
    $item_res = $conn->query($item_sql);
    $order['items'] = [];
    while ($item = $item_res->fetch_assoc()) $order['items'][] = $item;
}
unset($order);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>个人中心 - 酷牌商城</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-table ul { margin:0; padding:0; list-style:none; }
        .order-table ul li { margin-bottom:3px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="KupaiShop.php">酷牌商城 KupaiShop</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="KupaiShop.php">首页</a></li>
                <li class="nav-item"><a class="nav-link" href="product-list.php">全部商品</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">购物车</a></li>
                <li class="nav-item"><a class="nav-link active" href="user.php">个人中心</a></li>
            </ul>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="navbar-text me-2">欢迎，<?php echo htmlspecialchars($user['username']); ?></span>
                <a class="btn btn-outline-light" href="logout.php">退出</a>
            <?php else: ?>
                <a class="btn btn-outline-light me-2" href="login.php">登录</a>
                <a class="btn btn-light" href="register.php">注册</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>个人中心</h2>
    <div class="mb-3">
        <b>用户名：</b><?php echo htmlspecialchars($user['username']); ?><br>
        <b>邮箱：</b><?php echo htmlspecialchars($user['email']); ?><br>
        <b>账户余额：</b>
        <span class="text-success">￥<?php echo number_format($user['balance'],2); ?></span>
        <span class="text-muted ms-2">(余额由管理员发放或充值)</span>
        <a href="edit-profile.php" class="btn btn-warning btn-sm ms-2">修改信息</a>
    </div>

    <div class="mb-3">
        <a href="address.php" class="btn btn-info btn-sm">收货地址管理</a>
    </div>

    <div class="mb-4">
        <a href="user.php" class="btn btn-outline-primary btn-sm<?php if(!$status_filter)echo' active';?>">全部订单</a>
        <a href="user.php?status=pending" class="btn btn-outline-primary btn-sm<?php if($status_filter=='pending')echo' active';?>">待付款</a>
        <a href="user.php?status=paid" class="btn btn-outline-primary btn-sm<?php if($status_filter=='paid')echo' active';?>">已支付</a>
        <a href="user.php?status=shipped" class="btn btn-outline-primary btn-sm<?php if($status_filter=='shipped')echo' active';?>">已发货</a>
        <a href="user.php?status=completed" class="btn btn-outline-primary btn-sm<?php if($status_filter=='completed')echo' active';?>">已签收</a>
        <a href="user.php?status=comment" class="btn btn-outline-primary btn-sm<?php if($status_filter=='comment')echo' active';?>">待评价</a>
        <a href="user.php?status=cancelled" class="btn btn-outline-primary btn-sm<?php if($status_filter=='cancelled')echo' active';?>">已取消</a>
    </div>

    <h4>我的订单</h4>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">暂无订单记录。</div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-bordered align-middle order-table">
        <thead class="table-light">
            <tr>
                <th>订单编号</th>
                <th>商品</th>
                <th>数量</th>
                <th>总价</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['id']); ?></td>
                <td>
                    <ul class="order-items-list">
                    <?php foreach($order['items'] as $item): ?>
                        <li><?php echo htmlspecialchars($item['product_name']); ?></li>
                    <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <ul class="order-items-list">
                    <?php foreach($order['items'] as $item): ?>
                        <li><?php echo intval($item['quantity']); ?></li>
                    <?php endforeach; ?>
                    </ul>
                </td>
                <td>￥<?php echo number_format($order['total'], 2); ?></td>
                <td>
                    <?php
                        $status = $order['status'];
                        echo $status_map[$status] ?? htmlspecialchars($status);
                    ?>
                </td>
                <td>
                    <a href="order-detail.php?order_id=<?php echo $order['id']; ?>" class="btn btn-info btn-sm mb-1">详情</a>
                    <?php if ($order['status'] == 'pending'): ?>
                        <a href="pay.php?order_id=<?php echo $order['id']; ?>" class="btn btn-success btn-sm">去支付</a>
                    <?php endif; ?>
                    <?php if ($order['status'] == 'shipped'): ?>
                        <a href="confirm_receive.php?order_id=<?php echo $order['id']; ?>" class="btn btn-success btn-sm">确认收货</a>
                    <?php endif; ?>
                    <?php if ($order['status'] == 'completed'): ?>
                        <a href="comment.php?order_id=<?php echo $order['id']; ?>" class="btn btn-warning btn-sm">去评价</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<footer class="bg-primary text-white text-center py-3">&copy; <?php echo date('Y'); ?> 酷牌商城</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>