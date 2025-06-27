<?php
include 'config.php';
$msg = '';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
    if ($username === '' || $password === '') {
        $msg = "用户名和密码不能为空！";
    } elseif ($captcha === '') {
        $msg = "验证码不能为空！";
    } elseif (!isset($_SESSION['captcha']) || strtolower($captcha) !== strtolower($_SESSION['captcha'])) {
        $msg = "验证码错误！";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($uid, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                // 登录成功，设置session
                $_SESSION['user_id'] = $uid;
                $_SESSION['username'] = $username;
                header("Location: KupaiShop.php");
                exit;
            } else {
                $msg = "密码错误！";
            }
        } else {
            $msg = "用户不存在！";
        }
        $stmt->close();
    }
    // 防止同一个验证码被多次提交
    unset($_SESSION['captcha']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>登录 - 酷牌商城 KupaiShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="KupaiShop.php">酷牌商城 KupaiShop</a>
    </div>
</nav>
<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4">登录</h2>
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
            <label for="captcha" class="form-label">验证码</label>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control me-2" id="captcha" name="captcha" maxlength="4" required autocomplete="off">
                <img src="captcha.php" onclick="this.src='captcha.php?'+Math.random();" title="看不清？点击换一张" style="cursor:pointer;height:36px;">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">登录</button>
    </form>
    <div class="mt-3 text-center">
        还没有账号？<a href="register.php">立即注册</a>
    </div>
</div>
<footer class="bg-primary text-white text-center py-3 mt-4">
    &copy; 2025 酷牌商城 KupaiShop. 保留所有权利。
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>