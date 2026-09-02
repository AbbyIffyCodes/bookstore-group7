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


$stock_list = [];
$query = "SELECT BookID, CustomBookID, Title, Category, Stock, Price FROM books ORDER BY BookID ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stock_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Stock Checker - BookShop</title>
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
            padding: 15px;
        }
        .logout-btn {
            background-color: grey;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        .blue-btn {
            background-color: blue;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            font-weight: bold;
        }
        #inventory_table {
            width: 100%;
            border-collapse: collapse;
        }
        #inventory_table th {
            background-color: #5d83e1;
            color: white;
            padding: 12px;
        }
        #inventory_table td {
            background-color: #5911eb;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }
        .stock-out {
            background-color: #d9534f !important;
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
            <td align="right"><b>EMPLOYEE PANEL</b></td>
        </tr>
    </table>

    <table id="main_layout">
        <tr>
            <td id="sidebar">
                <a href="employee_dashboard.php">DASHBOARD</a>
                <a href="employee_billing.php">BILLING</a>
                <a href="employee_transaction.php" id="active_menu">STOCK</a>
                <a href="profile_settings.php">SETTINGS</a>
                <br><br>
                <div style="padding: 0 20px;">
                    <button type="button" class="logout-btn" onclick="confirmLogout()">LOG OUT</button>
                </div>
            </td>
            <td id="content">
                <fieldset>
                    <legend>STOCK CHECKER</legend>
                    
                    <div style="margin-bottom: 15px;">
                        <input type="text" id="searchInput" onkeyup="filterStockTable()" placeholder="SEARCH BOOK OR CATEGORY..." style="padding: 6px; width: 250px;">
                        <button type="button" class="blue-btn" onclick="filterStockTable()">SEARCH</button>
                    </div>

                    <table id="inventory_table" border="1">
                        <thead>
                            <tr>
                                <th>BOOK ID</th>
                                <th>TITLE</th>
                                <th>CATEGORY</th>
                                <th>PRICE</th>
                                <th>STOCK QTY</th>
                            </tr>
                        </thead>
                        <tbody id="stockTableBody">
                            <?php if (!empty($stock_list)): ?>
                                <?php foreach ($stock_list as $item): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($item['CustomBookID'] ?? $item['BookID']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Category']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Price']); ?> TK</td>
                                    <td class="<?php echo ($item['Stock'] <= 0) ? 'stock-out' : ''; ?>">
                                        <?php echo htmlspecialchars($item['Stock']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No stock records found in database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "employee_transaction.php?action=logout";
            }
        }

        function filterStockTable() {
            const input = document.getElementById('searchInput').value.toUpperCase();
            const tbody = document.getElementById('stockTableBody');
            const rows = tbody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const text = rows[i].textContent || rows[i].innerText;
                if (text.toUpperCase().indexOf(input) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>