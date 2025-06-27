<?php
session_start();
include 'config.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';

    if ($username === '' || $password === '' || $email === '') {
        $msg = "用户名、密码、邮箱都不能为空！";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "邮箱格式错误！";
    } elseif ($captcha === '') {
        $msg = "验证码不能为空！";
    } elseif (!isset($_SESSION['captcha']) || strtolower($captcha) !== strtolower($_SESSION['captcha'])) {
        $msg = "验证码错误！";
    } else {
        // 检查用户名或邮箱是否已存在
        $stmt = $conn->prepare("SELECT id FROM user WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $msg = "用户名或邮箱已被注册！";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hash, $email);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                header("Location: KupaiShop.php");
                exit;
            } else {
                $msg = "注册失败，请重试！";
            }
        }
        $stmt->close();
    }
    unset($_SESSION['captcha']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>注册 - 酷牌商城 KupaiShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="KupaiShop.php">酷牌商城 KupaiShop</a>
    </div>
</nav>
<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4">注册</h2>
    <?php if($msg): ?>
        <div class="alert alert-danger"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label for="username" class="form-label">用户名</label>
            <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">密码</label>
            <input type="password" class="form-control" id="password" name="password" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">邮箱</label>
            <input type="email" class="form-control" id="email" name="email" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="captcha" class="form-label">验证码</label>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control me-2" id="captcha" name="captcha" maxlength="4" required autocomplete="off">
                <img src="captcha.php" onclick="this.src='captcha.php?'+Math.random();" title="看不清？点击换一张" style="cursor:pointer;height:36px;">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">注册</button>
    </form>
    <div class="mt-3 text-center">
        已有账号？<a href="login.php">去登录</a>
    </div>
</div>
<footer class="bg-primary text-white text-center py-3 mt-4">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>