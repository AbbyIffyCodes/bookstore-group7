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

if (isset($_GET['action']) && $_GET['action'] === 'search_stock') {
    header('Content-Type: application/json');
    $query = $_GET['query'] ?? '';
    $stock_list = searchStockItems($query);
    echo json_encode($stock_list);
    exit();
}

$stock_list = getAllStockItems();

include_once '../views/employee_transaction.php';
?>