<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>购物车 - 酷牌商城 KupaiShop</title>
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
                <li class="nav-item"><a class="nav-link active" href="cart.html">购物车</a></li>
                <li class="nav-item"><a class="nav-link" href="user.php">个人中心</a></li>
            </ul>
            <a class="btn btn-outline-light me-2" href="login.php">登录</a>
            <a class="btn btn-light" href="register.php">注册</a>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <h2>我的购物车</h2>
    <table class="table table-bordered align-middle mt-3">
        <thead class="table-light">
            <tr>
                <th>商品图片</th>
                <th>商品名称</th>
                <th>单价</th>
                <th>数量</th>
                <th>小计</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><img src="assets/images/product1.jpg" style="height:60px;" alt="商品1"></td>
                <td>商品1</td>
                <td>￥99.00</td>
                <td>
                    <input type="number" value="1" min="1" max="20" style="width:60px;">
                </td>
                <td>￥99.00</td>
                <td><button class="btn btn-danger btn-sm">删除</button></td>
            </tr>
            <tr>
                <td><img src="assets/images/product2.jpg" style="height:60px;" alt="商品2"></td>
                <td>商品2</td>
                <td>￥159.00</td>
                <td>
                    <input type="number" value="2" min="1" max="10" style="width:60px;">
                </td>
                <td>￥318.00</td>
                <td><button class="btn btn-danger btn-sm">删除</button></td>
            </tr>
        </tbody>
    </table>
    <div class="text-end">
        <h5>总计：<span class="text-danger">￥417.00</span></h5>
        <a href="#" class="btn btn-success btn-lg mt-2">去结算</a>
    </div>
</div>
<footer class="bg-primary text-white text-center py-3 mt-4">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>