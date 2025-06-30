<?php
session_start();
$product_id = intval($_POST['product_id']);
$quantity = max(1, intval($_POST['quantity']));

// 立即购买：只买当前商品，先清空购物车
$_SESSION['cart'] = [];
$_SESSION['cart'][$product_id] = $quantity;
header("Location: checkout.php");
exit;
