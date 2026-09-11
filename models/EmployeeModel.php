<?php
require_once __DIR__ . '/dbconnection.php';

function getPendingOrdersCount() {
    global $conn;
    $pending_count = 0;
    $res = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE Status = 'Pending'");
    if ($res && $row = $res->fetch_assoc()) {
        $pending_count = $row['total'];
    }
    return $pending_count;
}

function getActiveDeliveriesCount() {
    global $conn;
    $deliveries_count = 0;
    $res = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE Status = 'Processing' OR Status = 'Shipped'");
    if ($res && $row = $res->fetch_assoc()) {
        $deliveries_count = $row['total'];
    }
    return $deliveries_count;
}

function getOutOfStockCount() {
    global $conn;
    $out_of_stock_count = 0;
    $res = $conn->query("SELECT COUNT(*) AS total FROM books WHERE Stock <= 0");
    if ($res && $row = $res->fetch_assoc()) {
        $out_of_stock_count = $row['total'];
    }
    return $out_of_stock_count;
}

function getRecentOrders($limit = 5) {
    global $conn;
    $recent_orders = [];
    $stmt = $conn->prepare("SELECT OrderID, OrderNumber, TotalAmount, OrderType FROM orders ORDER BY OrderDate DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $recent_orders[] = $row;
        }
    }
    $stmt->close();
    return $recent_orders;
}

function getBookByTitle($title) {
    global $conn;
    $stmt = $conn->prepare("SELECT BookID, Title, Price, Stock FROM books WHERE Title = ? LIMIT 1");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $res = $stmt->get_result();
    $book = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    $stmt->close();
    return $book;
}

function processBillingTransaction($cartItems, $userId) {
    global $conn;
    $conn->begin_transaction();

    try {
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += ($item['price'] * $item['qty']);
        }

        $order_number = "ORD-" . time();
        $order_type = "In Store";
        $status = "Completed";

        $stmt_order = $conn->prepare("INSERT INTO orders (OrderNumber, UserID, TotalAmount, OrderType, Status) VALUES (?, ?, ?, ?, ?)");
        $stmt_order->bind_param("sidss", $order_number, $userId, $subtotal, $order_type, $status);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();

        $stmt_item = $conn->prepare("INSERT INTO orderitems (OrderID, BookID, Quantity, UnitPrice) VALUES (?, ?, ?, ?)");
        $stmt_stock = $conn->prepare("UPDATE books SET Stock = Stock - ? WHERE BookID = ?");

        foreach ($cartItems as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['book_id'], $item['qty'], $item['price']);
            $stmt_item->execute();

            $stmt_stock->bind_param("ii", $item['qty'], $item['book_id']);
            $stmt_stock->execute();
        }

        $stmt_item->close();
        $stmt_stock->close();

        $conn->commit();
        return ["success" => true, "order_number" => $order_number];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "error" => $e->getMessage()];
    }
}

function getAllStockItems() {
    global $conn;
    $stock_list = [];
    $query = "SELECT BookID, CustomBookID, Title, Category, Stock, Price FROM books ORDER BY BookID ASC";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stock_list[] = $row;
        }
    }
    return $stock_list;
}

function searchStockItems($query) {
    global $conn;
    $searchTerm = "%" . $query . "%";
    $sql = "SELECT BookID, CustomBookID, Title, Category, Stock, Price 
            FROM books 
            WHERE Title LIKE ? OR Category LIKE ? OR CustomBookID LIKE ? OR CAST(BookID AS CHAR) LIKE ?
            ORDER BY BookID ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $stock_list = [];
    while ($row = $result->fetch_assoc()) {
        $stock_list[] = $row;
    }
    $stmt->close();
    return $stock_list;
}
?>