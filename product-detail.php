<?php
session_start();
include 'config.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "非法商品ID";
    exit;
}

// 查询商品信息
$sql = "SELECT * FROM product WHERE id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    echo "找不到该商品";
    exit;
}
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - 商品详情 - 酷牌商城 KupaiShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-img {
            max-width: 100%;
            max-height: 440px;
            border-radius: 16px;
            background: #fff;
            display: block;
            margin: 0 auto;
        }
        .buy-cart-form input[type="number"] {
            width: 70px;
            display: inline-block;
        }
        .buy-cart-form .btn {
            min-width: 120px;
        }
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
                <li class="nav-item"><a class="nav-link" href="user.php">个人中心</a></li>
            </ul>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="navbar-text me-2">欢迎，<?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a class="btn btn-outline-light" href="logout.php">退出</a>
            <?php else: ?>
                <a class="btn btn-outline-light me-2" href="login.php">登录</a>
                <a class="btn btn-light" href="register.php">注册</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-md-6 text-center">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <h4 class="text-danger mb-3">￥<?php echo number_format($product['price'], 2); ?></h4>
            <p><?php echo nl2br(htmlspecialchars($product['description'] ?? '暂无描述')); ?></p>
            <div class="buy-cart-form mt-3">
                <form method="post" action="add_to_cart.php" class="d-inline">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <label for="cart-qty" class="me-1">数量:</label>
                    <input type="number" id="cart-qty" name="quantity" value="1" min="1" max="99">
                    <button type="submit" class="btn btn-primary ms-2">加入购物车</button>
                </form>
                <form method="post" action="buy_now.php" class="d-inline ms-2">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1" id="buy-now-qty">
                    <button type="submit" class="btn btn-success">立即购买</button>
                </form>
            </div>
            <script>
                // 保证立即购买和加入购物车数量一致
                document.getElementById('cart-qty').addEventListener('input', function() {
                    document.getElementById('buy-now-qty').value = this.value;
                });
            </script>
        </div>
    </div>
</div>
<footer class="bg-primary text-white text-center py-3">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>