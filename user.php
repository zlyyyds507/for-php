<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>个人中心 - 酷牌商城 KupaiShop</title>
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
                <li class="nav-item"><a class="nav-link active" href="user.html">个人中心</a></li>
            </ul>
            <a class="btn btn-outline-light me-2" href="login.php">登录</a>
            <a class="btn btn-light" href="register.php">注册</a>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <h2>个人中心</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h5>用户名：小明</h5>
            <p>邮箱：xiaoming@example.com</p>
            <a href="#" class="btn btn-secondary btn-sm">修改信息</a>
            <a href="login.php" class="btn btn-danger btn-sm">退出登录</a>
        </div>
    </div>
    <h4>我的订单</h4>
    <table class="table table-bordered">
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
            <tr>
                <td>20250624001</td>
                <td>商品1</td>
                <td>2</td>
                <td>￥198.00</td>
                <td>已发货</td>
                <td><button class="btn btn-info btn-sm">查看详情</button></td>
            </tr>
            <tr>
                <td>20250624002</td>
                <td>商品2</td>
                <td>1</td>
                <td>￥159.00</td>
                <td>待付款</td>
                <td><button class="btn btn-info btn-sm">去支付</button></td>
            </tr>
        </tbody>
    </table>
</div>
<footer class="bg-primary text-white text-center py-3 mt-4">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>