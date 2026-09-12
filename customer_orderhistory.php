<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History - BookShop</title>
    <style>
        html, body {
            width: 100%;
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
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

        .header-links {
            text-align: right;
            font-size: 16px;
        }

        .header-links a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
        }

        #main_content {
            vertical-align: top;
            padding: 40px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 25px;
            color: #4c63ee;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
        }

        #history_box {
            width: 100%;
            background-color: #f3ecee;
            padding: 40px;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .history-table th {
            font-size: 20px;
            font-weight: normal;
            padding: 15px 20px 30px 20px;
            color: #000000;
        }

        .history-table td {
            font-size: 18px;
            padding: 20px;
            color: #000000;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            width: 120px;
            padding: 8px 0;
            text-align: center;
            color: #ffffff;
            font-weight: normal;
            font-size: 14px;
            border-radius: 2px;
        }

        .status-delivered {
            background-color: #00e639;
        }

        .status-pending {
            background-color: #ff5c00;
        }

        .empty-history {
            text-align: center;
            font-size: 18px;
            color: #777;
            padding: 40px 0;
        }
    </style>
</head>
<body>

    <table id="page_wrapper">
        <tr id="top_bar">
            <td class="logo-section">
                <img src="../public/images/ONLINE_BOOKSHOP_LOGO.jpg" alt="BookShop Logo">
                <span>BookShop</span>
            </td>
            <td class="header-links">
                <a href="../controllers/customer_cart_controller.php">Cart(<?php echo $total_cart_items; ?>)</a>
                <a href="../controllers/customer_dashboard_controller.php">Customer</a>
            </td>
        </tr>

        <tr>
            <td id="main_content" colspan="2">
                <a href="../controllers/customer_dashboard_controller.php" class="back-link">&larr; Back to Dashboard</a>

                <div id="history_box">
                    <?php if (empty($order_history)): ?>
                        <div class="empty-history">No orders have been placed yet.</div>
                    <?php else: ?>
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th width="22%">Order ID</th>
                                    <th width="25%">Date</th>
                                    <th width="18%">Items</th>
                                    <th width="20%">Amount</th>
                                    <th width="15%" style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_history as $order): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['date']); ?></td>
                                        <td><?php echo htmlspecialchars($order['items']); ?></td>
                                        <td><?php echo htmlspecialchars($order['amount']); ?>TK</td>
                                        <td align="center">
                                            <?php 
                                                $statusClass = (strtolower($order['status']) === 'delivered') ? 'status-delivered' : 'status-pending';
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>