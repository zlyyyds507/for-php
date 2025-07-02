<?php
session_start();
include 'config.php';

// 检查是否登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 获取购物车中的商品
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<div class='alert alert-warning'>购物车为空！</div>";
    exit;
}

// 查询商品信息
$ids = implode(',', array_map('intval', array_keys($cart)));
$sql = "SELECT * FROM product WHERE id IN ($ids)";
$result = $conn->query($sql);
$products = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $pid = $row['id'];
    $row['qty'] = $cart[$pid];
    $row['subtotal'] = $row['price'] * $row['qty'];
    $products[] = $row;
    $total += $row['subtotal'];
}

// 查询用户默认收货地址（如果有address表）
$address = null;
$address_sql = "SELECT * FROM address WHERE user_id={$_SESSION['user_id']} AND is_default=1 LIMIT 1";
$address_res = $conn->query($address_sql);
if ($address_res && $address_res->num_rows) {
    $address = $address_res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>结算页 - 酷牌商城</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>确认订单</h2>
    <div>
        <h5>收货地址</h5>
        <?php if ($address): ?>
            <div>
                <?php echo htmlspecialchars($address['name']); ?>，
                <?php echo htmlspecialchars($address['phone']); ?>，
                <?php echo htmlspecialchars($address['province'] . $address['city'] . $address['detail']); ?>
            </div>
        <?php else: ?>
            <div class="text-danger">未填写收货地址，请<a href="address.php">添加地址</a></div>
        <?php endif; ?>
    </div>
    <h5 class="mt-4">商品信息</h5>
    <table class="table">
        <thead><tr><th>商品</th><th>单价</th><th>数量</th><th>小计</th></tr></thead>
        <tbody>
        <?php foreach ($products as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>￥<?php echo number_format($item['price'],2); ?></td>
                <td><?php echo intval($item['qty']); ?></td>
                <td>￥<?php echo number_format($item['subtotal'],2); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h5 class="text-end">应付总额：<span class="text-danger">￥<?php echo number_format($total,2); ?></span></h5>
    <form action="pay.php" method="get">
        <!-- 实际项目应先生成订单，这里仅示范流程 -->
        <input type="hidden" name="order_id" value="模拟订单ID">
        <button class="btn btn-success btn-lg mt-2" type="submit">去支付</button>
    </form>
</div>
</body>
</html>