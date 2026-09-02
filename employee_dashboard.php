<?php
session_start();
require_once 'dbconnection.php';


if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'employee') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$employee_name = isset($_SESSION['username']) ? strtoupper($_SESSION['username']) : 'EMPLOYEE';


$pending_count = 0;
$res_pending = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE Status = 'Pending'");
if ($res_pending && $row = $res_pending->fetch_assoc()) {
    $pending_count = $row['total'];
}


$deliveries_count = 0;
$res_deliv = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE Status = 'Processing' OR Status = 'Shipped'");
if ($res_deliv && $row = $res_deliv->fetch_assoc()) {
    $deliveries_count = $row['total'];
}


$out_of_stock_count = 0;
$res_stock = $conn->query("SELECT COUNT(*) AS total FROM books WHERE Stock <= 0");
if ($res_stock && $row = $res_stock->fetch_assoc()) {
    $out_of_stock_count = $row['total'];
}


$recent_orders = [];
$res_orders = $conn->query("SELECT OrderID, OrderNumber, TotalAmount, OrderType FROM orders ORDER BY OrderDate DESC LIMIT 5");
if ($res_orders && $res_orders->num_rows > 0) {
    while ($row = $res_orders->fetch_assoc()) {
        $recent_orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard - BookShop</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: lightblue;
        }
        #header_table {
            width: 100%;
            height: 50px;
            background-color: blue;
            color: white;
            padding: 0 20px;
        }
        #main_layout {
            width: 100%;
            height: calc(100vh - 50px);
            border-collapse: collapse;
        }
        #sidebar {
            width: 220px;
            vertical-align: top;
            background-color: #4c63ee;
            padding-top: 10px;
        }
        #sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 12px 20px;
        }
        #active_menu {
            background-color: grey;
        }
        #content {
            vertical-align: top;
            padding: 20px;
        }
        fieldset {
            border-color: blue;
            background-color: white;
            margin-bottom: 20px;
        }
        button {
            background-color: grey;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        #stats_table, #orders_table {
            width: 100%;
            border-collapse: collapse;
        }
        #stats_table td {
            background-color: #4c63ee;
            color: white;
            padding: 25px;
            text-align: center;
            font-weight: bold;
            width: 33.33%;
        }
        #orders_table th {
            background-color: #5d83e1;
            color: white;
            padding: 12px;
        }
        #orders_table td {
            background-color: #5911eb;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <table id="header_table">
        <tr>
            <td>
                <img src="ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo" height="30" align="middle">
                <b>BOOKSHOP</b>
            </td>
            <td align="right"><b>HI, <?php echo htmlspecialchars($employee_name); ?></b></td>
        </tr>
    </table>

    <table id="main_layout">
        <tr>
            <td id="sidebar">
                <a href="employee_dashboard.php" id="active_menu">DASHBOARD</a>
                <a href="employee_billing.php">BILLING</a>
                <a href="employee_transaction.php">STOCK</a>
                <a href="profile_settings.php">SETTINGS</a>
                <br><br>
                <div style="padding: 0 20px;">
                    <button type="button" onclick="confirmLogout()" style="width: 100%;">LOG OUT</button>
                </div>
            </td>
            <td id="content">
                <table id="stats_table" border="1">
                    <tr>
                        <td>PENDING CONFIRMATION<br><br><h2><?php echo $pending_count; ?></h2></td>
                        <td>ACTIVE DELIVERIES<br><br><h2><?php echo $deliveries_count; ?></h2></td>
                        <td>OUT OF STOCK ITEMS<br><br><h2><?php echo $out_of_stock_count; ?></h2></td>
                    </tr>
                </table>
                <br /><br />

                <fieldset>
                    <legend>Recent Orders</legend>
                    <table id="orders_table" border="1">
                        <tr>
                            <th>ORDER ID</th>
                            <th>AMOUNT</th>
                            <th>TYPE</th>
                        </tr>
                        <?php if (!empty($recent_orders)): ?>
                            <?php foreach ($recent_orders as $ord): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($ord['OrderNumber'] ?? $ord['OrderID']); ?></td>
                                <td>BDT <?php echo htmlspecialchars($ord['TotalAmount']); ?></td>
                                <td><?php echo htmlspecialchars(strtoupper($ord['OrderType'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "employee_dashboard.php?action=logout";
            }
        }
    </script>
</body>
</html>