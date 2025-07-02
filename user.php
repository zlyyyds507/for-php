<?php
session_start();
include 'config.php';
include 'status_map.php'; // 加入状态映射文件

// 检查登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = intval($_SESSION['user_id']);

// 查询用户信息
$user_sql = "SELECT username, email FROM user WHERE id = $user_id";
$user = $conn->query($user_sql)->fetch_assoc();

// 订单状态筛选
$status_filter = $_GET['status'] ?? '';
$order_sql = "SELECT * FROM `order` WHERE user_id = $user_id";
if ($status_filter) {
    $order_sql .= " AND status = '". $conn->real_escape_string($status_filter) ."'";
}
$order_sql .= " ORDER BY created_at DESC";
$order_result = $conn->query($order_sql);

$orders = [];
while ($order_row = $order_result->fetch_assoc()) {
    // 查询订单商品
    $items = [];
    $items_sql = "SELECT oi.*, p.name AS product_name 
                  FROM order_item oi 
                  LEFT JOIN product p ON oi.product_id = p.id 
                  WHERE order_id = " . $order_row['id'];
    $items_result = $conn->query($items_sql);
    while ($item_row = $items_result->fetch_assoc()) {
        $items[] = $item_row;
    }
    $order_row['items'] = $items;
    $orders[] = $order_row;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>个人中心 - 酷牌商城</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-info-box { background: #f8f9fa; border-radius: 10px; padding: 32px 24px 20px 24px; margin-bottom: 24px; box-shadow: 0 2px 8px #0001; max-width: 480px; }
        .order-table th, .order-table td { vertical-align: middle; }
        .order-table .order-items-list { margin: 0; padding: 0; list-style: none; }
        .order-table .order-items-list li { margin-bottom: 3px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="KupaiShop.php">酷牌商城</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="KupaiShop.php">首页</a></li>
                <li class="nav-item"><a class="nav-link" href="product-list.php">全部商品</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">购物车</a></li>
                <li class="nav-item"><a class="nav-link active" href="user.php">个人中心</a></li>
            </ul>
            <span class="navbar-text me-2">欢迎，<?php echo htmlspecialchars($user['username']); ?></span>
            <a class="btn btn-outline-light" href="logout.php">退出</a>
        </div>
    </div>
</nav>
<div class="container mt-4 mb-5">
    <h2>个人中心</h2>
    <!-- 用户信息 -->
    <div class="user-info-box mb-4">
        <div class="mb-2">用户名：<?php echo htmlspecialchars($user['username']); ?></div>
        <div class="mb-2">邮箱：<?php echo htmlspecialchars($user['email']); ?></div>
        <a href="edit-profile.php" class="btn btn-secondary btn-sm me-2">修改信息</a>
        <a href="address.php" class="btn btn-info btn-sm me-2">收货地址管理</a>
        <a href="logout.php" class="btn btn-danger btn-sm">退出登录</a>
    </div>
    <!-- 订单状态tab -->
    <div class="mb-3">
      <a href="user.php" class="btn btn-outline-primary btn-sm<?php if(!$status_filter)echo' active';?>">全部</a>
      <a href="user.php?status=pending" class="btn btn-outline-primary btn-sm<?php if($status_filter=='pending')echo' active';?>">待付款</a>
      <a href="user.php?status=paid" class="btn btn-outline-primary btn-sm<?php if($status_filter=='paid')echo' active';?>">待发货</a>
      <a href="user.php?status=shipped" class="btn btn-outline-primary btn-sm<?php if($status_filter=='shipped')echo' active';?>">待收货</a>
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
                <td><?php echo htmlspecialchars($order['order_no']); ?></td>
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
                        echo isset($status_map[$status]) ? $status_map[$status] : htmlspecialchars($status);
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
<footer class="bg-primary text-white text-center py-3">&copy; 2025 酷牌商城</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>