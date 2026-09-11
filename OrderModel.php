<?php
require_once __DIR__ . '/dbconnection.php';
function getTotalSalesSum($conn) {
    $sales_query = mysqli_query($conn, "SELECT SUM(TotalAmount) AS total FROM orders");
    if ($sales_query) {
        return mysqli_fetch_assoc($sales_query)['total'] ?? 0;
    }
    return 0;
}

function getRecentOrders($conn, $limit = 5) {
    $recent_orders = [];
    $orders_query = mysqli_query($conn, "SELECT OrderID, TotalAmount, OrderType FROM orders ORDER BY OrderDate DESC LIMIT " . intval($limit));
    if ($orders_query) {
        while ($row = mysqli_fetch_assoc($orders_query)) {
            $recent_orders[] = $row;
        }
    }
    return $recent_orders;
}
?>