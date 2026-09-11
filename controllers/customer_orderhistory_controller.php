<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/CustomerModel.php';

if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'customer') {
    header("Location: login_controller.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$order_history = [];

if ($user_id > 0) {
    $order_history = getCustomerOrderHistory($user_id);
}

$total_cart_items = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_cart_items += $item['qty'];
    }
}

include_once '../views/customer_orderhistory.php';
?>