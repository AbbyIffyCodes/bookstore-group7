<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/EmployeeModel.php';

if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'employee') {
    header("Location: login_controller.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login_controller.php");
    exit();
}

$employee_name = isset($_SESSION['username']) ? strtoupper($_SESSION['username']) : 'EMPLOYEE';

$pending_count = getPendingOrdersCount();
$deliveries_count = getActiveDeliveriesCount();
$out_of_stock_count = getOutOfStockCount();
$recent_orders = getRecentOrders(5);

include_once '../views/employee_dashboard.php';
?>