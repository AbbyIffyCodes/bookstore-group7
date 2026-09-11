<?php
require_once __DIR__ . '/dbconnection.php';

function getCustomerDashboardStats($user_id) {
    global $conn;
    $stats = [
        'total' => 0,
        'pending' => 0,
        'delivered' => 0
    ];

    $stmt = $conn->prepare("SELECT 
        COUNT(*) AS total, 
        SUM(CASE WHEN LOWER(Status) = 'pending' THEN 1 ELSE 0 END) AS pending, 
        SUM(CASE WHEN LOWER(Status) = 'delivered' THEN 1 ELSE 0 END) AS delivered 
        FROM orders WHERE UserID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $stats['total'] = $row['total'] ?? 0;
        $stats['pending'] = $row['pending'] ?? 0;
        $stats['delivered'] = $row['delivered'] ?? 0;
    }
    $stmt->close();
    return $stats;
}

function getCustomerRecentOrders($user_id, $limit = 4) {
    global $conn;
    $recent_orders = [];
    $stmt = $conn->prepare("SELECT OrderNumber, TotalAmount, Status FROM orders WHERE UserID = ? ORDER BY OrderID DESC LIMIT ?");
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $recent_orders[] = $row;
    }
    $stmt->close();
    return $recent_orders;
}

function getAllCatalogBooks() {
    global $conn;
    $books = [];
    $query = "SELECT BookID, Title, Price, Category, Image FROM books";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = [
                'id'       => $row['BookID'],
                'title'    => $row['Title'],
                'price'    => $row['Price'],
                'category' => $row['Category'],
                'img'      => formatBookImage($row['Image'])
            ];
        }
    }
    return $books;
}
function formatBookImage($image_path) {
    if (empty($image_path)) {
        return '../public/images/default_cover.jpg';
    }
    
    if (strpos($image_path, '../public/') === 0) {
        return $image_path;
    }
    
    return '../public/images/' . basename($image_path);
}

function searchCatalogBooks($query, $category) {
    global $conn;
    $sql = "SELECT BookID, Title, Price, Category, Image FROM books WHERE Title LIKE ?";
    $searchTerm = "%" . $query . "%";

    if (!empty($category) && $category !== 'All') {
        $sql .= " AND Category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $category);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $searchTerm);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];

    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'id'       => $row['BookID'],
            'title'    => $row['Title'],
            'price'    => $row['Price'],
            'category' => $row['Category'],
            'img'      => formatBookImage($row['Image'])
        ];
    }
    $stmt->close();
    return $books;
}

function getBookForCart($book_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT BookID, Title, Price, Image FROM books WHERE BookID = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $book = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    $stmt->close();
    return $book;
}

function placeCustomerOrder($user_id, $cart_items, $delivery_fee) {
    global $conn;
    
    $conn->begin_transaction();

    try {
        $order_number = "ORD" . rand(1000, 9999);
        $order_type = "Online";
        $status = "Pending";

        $calc_subtotal = 0;
        foreach ($cart_items as $item) {
            $calc_subtotal += ($item['price'] * $item['qty']);
        }
        $calc_total = $calc_subtotal + $delivery_fee;

        $stmtOrder = $conn->prepare("INSERT INTO orders (OrderNumber, UserID, TotalAmount, OrderType, Status) VALUES (?, ?, ?, ?, ?)");
        $stmtOrder->bind_param("sidss", $order_number, $user_id, $calc_total, $order_type, $status);
        $stmtOrder->execute();
        $order_id = $conn->insert_id;
        $stmtOrder->close();

        $stmtItem = $conn->prepare("INSERT INTO orderitems (OrderID, BookID, Quantity, UnitPrice) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmtItem->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
            $stmtItem->execute();
        }
        $stmtItem->close();

        $conn->commit();
        return ['success' => true, 'order_number' => $order_number];

    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function getCustomerOrderHistory($user_id) {
    global $conn;
    $order_history = [];

    $query = "SELECT o.OrderID, o.OrderNumber, o.OrderDate, o.TotalAmount, o.Status, 
                     IFNULL(SUM(oi.Quantity), 0) AS TotalItems
              FROM orders o
              LEFT JOIN orderitems oi ON o.OrderID = oi.OrderID
              WHERE o.UserID = ?
              GROUP BY o.OrderID
              ORDER BY o.OrderID DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $order_history[] = [
            'order_id' => $row['OrderNumber'],
            'date'     => date('j F Y', strtotime($row['OrderDate'])),
            'items'    => $row['TotalItems'],
            'amount'   => $row['TotalAmount'],
            'status'   => $row['Status']
        ];
    }
    $stmt->close();
    return $order_history;
}
?>