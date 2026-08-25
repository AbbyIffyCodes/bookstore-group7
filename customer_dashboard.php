<?php
session_start();

// Session Access Control Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Handle Logout Action
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Initialize session order history if not already set
if (!isset($_SESSION['order_history'])) {
    $_SESSION['order_history'] = [
        ['order_id' => '#ORD1001', 'date' => '9 March 2026', 'items' => 4, 'amount' => 2600, 'status' => 'Delivered'],
        ['order_id' => '#ORD1002', 'date' => '26 May 2026', 'items' => 1, 'amount' => 740, 'status' => 'Delivered'],
        ['order_id' => '#ORD1003', 'date' => '8 June 2026', 'items' => 2, 'amount' => 1360, 'status' => 'Delivered'],
        ['order_id' => '#ORD1004', 'date' => '31 July 2026', 'items' => 3, 'amount' => 2140, 'status' => 'Pending']
    ];
}

// Calculate Dynamic Metrics for Customer Dashboard
$total_orders = 0;
$pending_orders = 0;
$delivered_orders = 0;

if (isset($_SESSION['order_history']) && is_array($_SESSION['order_history'])) {
    $total_orders = count($_SESSION['order_history']);
    foreach ($_SESSION['order_history'] as $order) {
        if (strtolower($order['status']) === 'pending') {
            $pending_orders++;
        } elseif (strtolower($order['status']) === 'delivered') {
            $delivered_orders++;
        }
    }
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "TAHMID";
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

        /* Top Bar Layout */
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

        /* Sidebar Navigation */
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

        /* Main Content Container */
        #content_area {
            vertical-align: top;
            padding: 30px;
            background-color: #ffffff;
        }

        /* Stat Cards Layout */
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

        /* Recent Orders Section */
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
        <!-- Top Header Navigation -->
        <tr id="top_bar">
            <td class="logo-section">
                <img src="ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo">
                <span>BookShop</span>
            </td>
            <td class="user-greeting">
                HI, <?php echo htmlspecialchars($user_name); ?>
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

            <!-- Main Content Area -->
            <td id="content_area">
                <!-- Dynamic Summary Metrics Cards -->
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

                <!-- Dynamic Recent Orders Display Box -->
                <div class="orders-card">
                    <h3>RECENT ORDERS</h3>

                    <?php if (empty($_SESSION['order_history'])): ?>
                        <div class="order-row">No orders placed yet.</div>
                    <?php else: ?>
                        <?php 
                        // Slice array to display only the top 4 recent orders
                        $recent_orders = array_slice(array_reverse($_SESSION['order_history']), 0, 4);
                        foreach ($recent_orders as $order): 
                        ?>
                            <div class="order-row">
                                <table role="presentation">
                                    <tr>
                                        <td width="33%"><?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td width="33%" align="center">BDT <?php echo htmlspecialchars($order['amount']); ?></td>
                                        <td width="33%" align="right"><?php echo strtoupper(htmlspecialchars($order['status'])); ?></td>
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