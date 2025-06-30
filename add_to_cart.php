<?php
session_start();
$product_id = intval($_POST['product_id']);
$quantity = max(1, intval($_POST['quantity']));

// 初始化购物车
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}
header("Location: cart.php");
exit;
