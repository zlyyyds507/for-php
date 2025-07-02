<?php
// 订单状态映射（英文 => 中文），可被多个业务页面 include
$status_map = [
    'pending'   => '待付款',
    'paid'      => '待发货',
    'shipped'   => '待收货',
    'completed' => '已签收',
    'comment'   => '待评价',
    'cancelled' => '已取消'
];
?>
