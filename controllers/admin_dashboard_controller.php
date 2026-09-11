<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/BookModel.php';
require_once '../models/UserModel.php';
require_once '../models/OrderModel.php';

if (!isset($_SESSION['user_role']) || strtoupper($_SESSION['user_role']) !== 'ADMIN') {
    header("Location: login_controller.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login_controller.php");
    exit();
}

$_SESSION['total_books'] = getTotalBooksCount($conn);
$_SESSION['total_customers'] = getTotalCustomersCount($conn);
$_SESSION['total_sales'] = getTotalSalesSum($conn);
$_SESSION['recent_orders'] = getRecentOrders($conn, 5);

include_once '../views/admin_dashboard.php'; 
exit();
?>