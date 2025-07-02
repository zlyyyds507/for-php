<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];

// 获取当前订单使用的地址
$address = null;
if (isset($_SESSION['order_address_id'])) {
    $aid = intval($_SESSION['order_address_id']);
    $sql = "SELECT * FROM address WHERE id=$aid AND user_id=$user_id";
    $result = $conn->query($sql);
    $address = $result->fetch_assoc();
}
if (!$address) {
    $sql = "SELECT * FROM address WHERE user_id=$user_id AND is_default=1 LIMIT 1";
    $result = $conn->query($sql);
    $address = $result->fetch_assoc();
}

// 获取购物车商品
$cart = $_SESSION['cart'] ?? [];
$cart_products = [];
$total = 0;
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $sql = "SELECT * FROM product WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $pid = $row['id'];
        $row['qty'] = $cart[$pid];
        $row['subtotal'] = $row['price'] * $row['qty'];
        $cart_products[] = $row;
        $total += $row['subtotal'];
    }
}

// 下单逻辑
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    if (!$address) {
        $err = "请先选择收货地址！";
    } elseif (empty($cart_products)) {
        $err = "购物车为空，无法下单！";
    } else {
        // 1. 新增订单主表
        $address_id = $address['id'];
        $total_amount = $total;
        $now = date('Y-m-d H:i:s');
        $status = '待发货';
        $conn->query("INSERT INTO `orders` (user_id, address_id, total_amount, status, created_at) VALUES ($user_id, $address_id, $total_amount, '$status', '$now')");
        $order_id = $conn->insert_id;

        // 2. 新增订单商品表
        foreach ($cart_products as $item) {
            $pid = $item['id'];
            $qty = $item['qty'];
            $price = $item['price'];
            $conn->query("INSERT INTO order_item (order_id, product_id, quantity, price) VALUES ($order_id, $pid, $qty, $price)");
        }

        // 3. 清空购物车与本次地址
        unset($_SESSION['cart']);
        unset($_SESSION['order_address_id']);

        // 4. 跳转到订单成功页
        header("Location: order_success.php?order_id=$order_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>确认订单</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4" style="max-width:900px;">
    <h2>确认订单</h2>
    <h5>收货地址</h5>
    <?php if ($address): ?>
        <div class="mb-2">
            <?php
                echo htmlspecialchars($address['name']) . '，';
                echo htmlspecialchars($address['phone']) . '，';
                echo htmlspecialchars($address['province'].$address['city'].$address['detail']);
            ?>
            <a href="address.php" class="btn btn-link btn-sm">更换地址</a>
        </div>
    <?php else: ?>
        <div class="text-danger mb-2">
            未填写收货地址，请<a href="address.php">添加地址</a>
        </div>
    <?php endif; ?>

    <h5>商品信息</h5>
    <table class="table mt-2">
        <tr>
            <th>商品</th>
            <th>单价</th>
            <th>数量</th>
            <th>小计</th>
        </tr>
        <?php foreach ($cart_products as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td>￥<?php echo number_format($item['price'],2); ?></td>
            <td><?php echo $item['qty']; ?></td>
            <td>￥<?php echo number_format($item['subtotal'],2); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="text-end"><b>应付总额：</b></td>
            <td class="text-danger"><b>￥<?php echo number_format($total,2); ?></b></td>
        </tr>
    </table>

    <?php if (!empty($err)): ?>
        <div class="alert alert-danger"><?php echo $err; ?></div>
    <?php endif; ?>

    <form method="post">
        <button class="btn btn-success btn-lg" name="pay" type="submit" <?php if(!$address || !$cart_products): ?>disabled<?php endif; ?>>去支付</button>
    </form>
</div>
</body>
</html>