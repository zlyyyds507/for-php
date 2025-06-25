<?php
include 'config.php'; // 数据库连接
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>酷牌商城首页 - KupaiShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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
                <li class="nav-item"><a class="nav-link active" href="KupaiShop.php">首页</a></li>
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
    <!-- 轮播图 -->
    <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="幻灯片 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                    aria-label="幻灯片 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                    aria-label="幻灯片 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/斯卡纳详情.png" class="d-block w-100" alt="酷牌商城-轮播1" style="height:560px;object-fit:cover;">
            </div>
            <div class="carousel-item">
                <img src="assets/images/奈德丽详情.png" class="d-block w-100" alt="酷牌商城-轮播2" style="height:560px;object-fit:cover;">
            </div>
            <div class="carousel-item">
                <img src="assets/images/弗拉基米尔详情.png" class="d-block w-100" alt="酷牌商城-轮播3" style="height:560px;object-fit:cover;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">上一个</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">下一个</span>
        </button>
    </div>

    <!-- 欢迎语 -->
    <div class="jumbotron p-4 mb-4 bg-light rounded-3">
        <h1 class="display-5">欢迎来到 <span style="color:#0d6efd;">酷牌商城</span>！</h1>
        <p class="lead">精选好物，品质保证，优惠多多，尽在酷牌！</p>
    </div>

    <!-- 推荐商品（动态） -->
    <h2 class="mb-3">推荐商品</h2>
    <div class="row">
        <?php
        // 取最新4件商品作为推荐
        $sql = "SELECT * FROM product ORDER BY created_at DESC LIMIT 4";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()):
        ?>
        <div class="col-md-3 mb-4">
            <div class="card">
                <a href="product-detail.php?id=<?php echo $row['id']; ?>">
                    <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                    <p class="card-text">￥<?php echo $row['price']; ?></p>
                    <a href="product-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">查看详情</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- 更多商品链接 -->
    <div class="text-center mb-5">
        <a href="product-list.php" class="btn btn-outline-primary btn-lg">查看更多商品</a>
    </div>
</div>

<footer class="bg-primary text-white text-center py-3">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>