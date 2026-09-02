<?php
session_start();
require_once 'dbconnection.php';


if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'customer') {
    header("Location: login.php");
    exit();
}


if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "CUSTOMER";


$total_orders = 0;
$pending_orders = 0;
$delivered_orders = 0;
$recent_orders = [];

if ($user_id > 0) {

    $stmtCount = $conn->prepare("SELECT 
        COUNT(*) AS total, 
        SUM(CASE WHEN LOWER(Status) = 'pending' THEN 1 ELSE 0 END) AS pending, 
        SUM(CASE WHEN LOWER(Status) = 'delivered' THEN 1 ELSE 0 END) AS delivered 
        FROM orders WHERE UserID = ?");
    $stmtCount->bind_param("i", $user_id);
    $stmtCount->execute();
    $resCount = $stmtCount->get_result();
    if ($row = $resCount->fetch_assoc()) {
        $total_orders = $row['total'] ?? 0;
        $pending_orders = $row['pending'] ?? 0;
        $delivered_orders = $row['delivered'] ?? 0;
    }
    $stmtCount->close();

    
    $stmtRecent = $conn->prepare("SELECT OrderNumber, TotalAmount, Status FROM orders WHERE UserID = ? ORDER BY OrderID DESC LIMIT 4");
    $stmtRecent->bind_param("i", $user_id);
    $stmtRecent->execute();
    $resRecent = $stmtRecent->get_result();
    while ($rRow = $resRecent->fetch_assoc()) {
        $recent_orders[] = $rRow;
    }
    $stmtRecent->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard - BookShop</title>
    <style>
        html, body {
            width: 100%;
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f5f9;
        }

        #page_wrapper {
            width: 100%;
            height: 100vh;
            border-collapse: collapse;
        }

        #top_bar {
            height: 70px;
            background-color: #4c63ee;
            color: #ffffff;
            padding: 0 30px;
        }

        .logo-section img {
            height: 40px;
            vertical-align: middle;
            margin-right: 15px;
            background: #fff;
            padding: 2px;
            border-radius: 4px;
        }

        .logo-section span {
            font-size: 24px;
            font-weight: bold;
            vertical-align: middle;
        }

        .user-greeting {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        #sidebar {
            width: 220px;
            background-color: #4c63ee;
            vertical-align: top;
            padding: 20px;
            border-right: 2px solid #ffffff;
        }

        .nav-btn {
            display: block;
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            box-sizing: border-box;
            border-radius: 4px;
        }

        .nav-btn.active {
            background-color: #b0b0b0;
            color: #333333;
        }

        .logout-btn {
            margin-top: 150px;
            background-color: #dcdcdc;
            color: #333333;
            border: none;
            width: 100%;
            height: 40px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #c8c8c8;
        }

        #content_area {
            vertical-align: top;
            padding: 30px;
            background-color: #ffffff;
        }

        .stat-card {
            background-color: #4c63ee;
            color: white;
            padding: 20px;
            border-radius: 8px;
            width: 28%;
            display: inline-block;
            box-sizing: border-box;
            margin-right: 2%;
            vertical-align: top;
        }

        .stat-card h4 {
            margin: 0 0 15px 0;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 22px;
            font-weight: bold;
        }

        .orders-card {
            background-color: #4c63ee;
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-top: 30px;
            width: 90%;
        }

        .orders-card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .order-row {
            background-color: #5112df;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-weight: bold;
            font-size: 14px;
        }

        .order-row table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-row td {
            color: #ffffff;
        }
    </style>
</head>
<body>

    <table id="page_wrapper">
        <tr id="top_bar">
            <td class="logo-section">
                <img src="ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo">
                <span>BookShop</span>
            </td>
            <td class="user-greeting">
                HI, <?php echo htmlspecialchars(strtoupper($user_name)); ?>
            </td>
        </tr>

        <tr>
            <td id="sidebar">
                <a href="customer_dashboard.php" class="nav-btn active">DASHBOARD</a>
                <a href="customer_cart.php" class="nav-btn">CART</a>
                <a href="customer_shop.php" class="nav-btn">SHOP</a>
                <a href="profile_settings.php" class="nav-btn">SETTINGS</a>
                <a href="customer_orderhistory.php" class="nav-btn">HISTORY</a>

                <form method="POST" action="" onsubmit="return confirmLogout();">
                    <button type="submit" name="logout" class="logout-btn">LOG OUT</button>
                </form>
            </td>

            <td id="content_area">
                <div>
                    <div class="stat-card">
                        <h4>TOTAL ORDERS</h4>
                        <div class="value"><?php echo $total_orders; ?></div>
                    </div>
                    <div class="stat-card">
                        <h4>ACTIVE DELIVERIES</h4>
                        <div class="value"><?php echo $pending_orders; ?></div>
                    </div>
                    <div class="stat-card">
                        <h4>DELIVERED ORDERS</h4>
                        <div class="value"><?php echo $delivered_orders; ?></div>
                    </div>
                </div>

                <div class="orders-card">
                    <h3>RECENT ORDERS</h3>

                    <?php if (empty($recent_orders)): ?>
                        <div class="order-row">No orders placed yet.</div>
                    <?php else: ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="order-row">
                                <table role="presentation">
                                    <tr>
                                        <td width="33%">#<?php echo htmlspecialchars($order['OrderNumber']); ?></td>
                                        <td width="33%" align="center">BDT <?php echo htmlspecialchars($order['TotalAmount']); ?></td>
                                        <td width="33%" align="right"><?php echo strtoupper(htmlspecialchars($order['Status'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }
    </script>
</body>
</html>