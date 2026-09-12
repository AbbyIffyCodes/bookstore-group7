<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/CustomerModel.php';

if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'customer') {
    header("Location: login_controller.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login_controller.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['user_name'] ?? "CUSTOMER";

$stats = ['total' => 0, 'pending' => 0, 'delivered' => 0];
$recent_orders = [];

if ($user_id > 0) {
    $stats = getCustomerDashboardStats($user_id);
    $recent_orders = getCustomerRecentOrders($user_id, 4);
}

$total_orders = $stats['total'];
$pending_orders = $stats['pending'];
$delivered_orders = $stats['delivered'];

include_once '../views/customer_dashboard.php';
?>