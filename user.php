<?php
session_start();
include 'config.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = intval($_SESSION['user_id']);

// 查询当前登录用户信息
$user_sql = "SELECT username, email FROM user WHERE id = $user_id";
$user_result = $conn->query($user_sql);
if (!$user_result) {
    die("用户查询失败: " . $conn->error . "<br>SQL: $user_sql");
}
if ($user_result->num_rows == 0) {
    echo "用户不存在，请重新登录。";
    exit;
}
$user = $user_result->fetch_assoc();

// 查询用户订单（假设有 order 和 order_item 两张表，order 里有 id, order_no, user_id, total, status, created_at，order_item 里有 order_id, product_id, quantity，product 里有 id, name）
$order_sql = "SELECT * FROM `order` WHERE user_id = $user_id ORDER BY created_at DESC";
$order_result = $conn->query($order_sql);

$orders = [];
if ($order_result) {
    while ($order_row = $order_result->fetch_assoc()) {
        // 查找订单所有商品
        $items_sql = "SELECT oi.*, p.name AS product_name FROM order_item oi LEFT JOIN product p ON oi.product_id = p.id WHERE order_id = " . $order_row['id'];
        $items_result = $conn->query($items_sql);

        $items = [];
        if ($items_result) {
            while ($item_row = $items_result->fetch_assoc()) {
                $items[] = $item_row;
            }
        }
        $order_row['items'] = $items;
        $orders[] = $order_row;
    }
}

// 订单状态映射
$status_map = [
    'pending' => '待付款',
    'paid' => '已付款',
    'shipped' => '已发货',
    'completed' => '已完成',
    'cancelled' => '已取消'
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>个人中心 - 酷牌商城 KupaiShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 32px 24px 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px #0001;
            max-width: 480px;
        }
        .order-table th, .order-table td { vertical-align: middle; }
        .order-table .order-items-list { margin: 0; padding: 0; list-style: none; }
        .order-table .order-items-list li { margin-bottom: 3px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="KupaiShop.php">酷牌商城 KupaiShop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="切换导航">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
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
<div class="container mt-4 mb-5">
    <h2 class="mb-4">个人中心</h2>
    <div class="user-info-box mb-4">
        <div class="mb-2">用户名：<?php echo htmlspecialchars($user['username']); ?></div>
        <div class="mb-2">邮箱：<?php echo htmlspecialchars($user['email']); ?></div>
        <a href="edit-profile.php" class="btn btn-secondary btn-sm me-2">修改信息</a>
        <a href="logout.php" class="btn btn-danger btn-sm">退出登录</a>
    </div>
    <h4 class="mb-3">我的订单</h4>
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
                    <a href="order-detail.php?order_id=<?php echo $order['id']; ?>" class="btn btn-info btn-sm mb-1">查看详情</a>
                    <?php if ($order['status'] == 'pending'): ?>
                        <a href="pay.php?order_id=<?php echo $order['id']; ?>" class="btn btn-success btn-sm">去支付</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<footer class="bg-primary text-white text-center py-3">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>