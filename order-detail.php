<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$order_id = intval($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// 查询订单（只允许本人查看该订单）
$sql = "SELECT o.*, a.name as recv_name, a.phone, a.province, a.city, a.detail 
        FROM orders o 
        LEFT JOIN address a ON o.address_id = a.id 
        WHERE o.id=$order_id AND o.user_id=$user_id";
$res = $conn->query($sql);
if (!$res || !$res->num_rows) {
    echo "<div class='alert alert-danger'>订单不存在或无权访问！</div>";
    exit;
}
$order = $res->fetch_assoc();

// 查询订单商品
$item_sql = "SELECT oi.*, p.name, p.image 
             FROM order_item oi 
             LEFT JOIN product p ON oi.product_id=p.id 
             WHERE oi.order_id=$order_id";
$item_res = $conn->query($item_sql);
$items = [];
while($row = $item_res->fetch_assoc()) $items[] = $row;

// 查询评价
$comment_sql = "SELECT * FROM comment WHERE order_id=$order_id AND user_id=$user_id";
$comment_res = $conn->query($comment_sql);
$comment = $comment_res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>订单详情</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>订单详情</h2>
    <div class="mb-3">
        <b>订单号：</b><?php echo $order['id']; ?>
        <b class="ms-4">下单时间：</b><?php echo $order['created_at']; ?>
        <b class="ms-4">订单状态：</b>
        <?php
            $map = [
                'pending'=>'待付款','paid'=>'已支付','shipped'=>'已发货',
                'completed'=>'已签收','cancelled'=>'已取消'
            ];
            echo $map[$order['status']] ?? $order['status'];
        ?>
    </div>
    <div class="mb-3">
        <b>收货人：</b><?php echo htmlspecialchars($order['recv_name']); ?>
        <b class="ms-4">电话：</b><?php echo htmlspecialchars($order['phone']); ?>
        <b class="ms-4">地址：</b><?php echo htmlspecialchars($order['province'].$order['city'].$order['detail']); ?>
    </div>
    <h5>商品列表</h5>
    <table class="table">
        <thead><tr><th>商品</th><th>图片</th><th>单价</th><th>数量</th><th>小计</th></tr></thead>
        <tbody>
        <?php foreach($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><img src="<?php echo $item['image']; ?>" style="height:50px;"></td>
            <td>￥<?php echo number_format($item['price'],2); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>￥<?php echo number_format($item['price']*$item['quantity'],2); ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-end mb-3">
        <b>订单总额：</b><span class="text-danger">￥<?php echo number_format($order['total'],2); ?></span>
    </div>

    <div>
        <a href="user.php" class="btn btn-secondary">返回我的订单</a>
        <?php if($order['status']=='pending'): ?>
            <a href="pay.php?order_id=<?php echo $order['id']; ?>" class="btn btn-success">去支付</a>
        <?php elseif($order['status']=='shipped'): ?>
            <a href="confirm_receive.php?order_id=<?php echo $order['id']; ?>" class="btn btn-warning">确认收货</a>
        <?php endif; ?>
        <?php if($order['status']=='completed' && !$comment): ?>
            <a href="comment.php?order_id=<?php echo $order['id']; ?>" class="btn btn-info">去评价</a>
        <?php endif; ?>
    </div>
    <?php if($comment): ?>
        <div class="mt-4 border rounded p-3 bg-light">
            <b>您的评价：</b>
            <div>评分：<?php echo $comment['score']; ?> 分</div>
            <div><?php echo nl2br(htmlspecialchars($comment['content'])); ?></div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>