<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$order_id = intval($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// 校验订单已完成且归属本人
$sql = "SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id AND status='completed'";
$res = $conn->query($sql);
if (!$res || !$res->num_rows) {
    echo "<div class='alert alert-danger'>订单不存在、无权评价或未签收！</div>";
    exit;
}

// 提交评价
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $conn->real_escape_string($_POST['content']);
    $score = max(1, min(5, intval($_POST['score'])));
    // 防止重复评价
    $check = $conn->query("SELECT id FROM comment WHERE order_id=$order_id AND user_id=$user_id");
    if ($check->num_rows > 0) {
        echo "<div class='alert alert-warning'>请勿重复评价！</div>";
    } else {
        $row_items = $conn->query("SELECT * FROM order_item WHERE order_id=$order_id");
        while($item = $row_items->fetch_assoc()) {
            $conn->query("INSERT INTO comment (order_id, product_id, user_id, content, score, created_at) VALUES ($order_id, {$item['product_id']}, $user_id, '$content', $score, NOW())");
        }
        header("Location: order-detail.php?order_id=$order_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>订单评价</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4" style="max-width:500px;">
    <h3>订单评价</h3>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">评分（1-5分）：</label>
            <select name="score" class="form-select" required>
                <?php for($i=5;$i>=1;$i--): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?>分</option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">评价内容：</label>
            <textarea name="content" class="form-control" rows="4" required></textarea>
        </div>
        <button class="btn btn-success" type="submit">提交评价</button>
        <a href="order-detail.php?order_id=<?php echo $order_id; ?>" class="btn btn-secondary">返回</a>
    </form>
</div>
</body>
</html>