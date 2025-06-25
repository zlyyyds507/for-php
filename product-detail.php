<?php
include 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM product WHERE id=$id LIMIT 1";
$result = $conn->query($sql);
$product = $result->fetch_assoc();
if(!$product) {
    echo "<h3>商品未找到！</h3>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - 商品详情</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link" href="user.php">个人中心</a></li>
            </ul>
            <a class="btn btn-outline-light me-2" href="login.php">登录</a>
            <a class="btn btn-light" href="register.php">注册</a>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-5">
            <img src="<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <h4 class="text-danger">￥<?php echo $product['price']; ?></h4>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p>库存：<?php echo $product['stock']; ?> 件</p>
            <!-- 这里可以加购买/加入购物车功能 -->
        </div>
    </div>
</div>
<footer class="bg-primary text-white text-center py-3 mt-4">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>