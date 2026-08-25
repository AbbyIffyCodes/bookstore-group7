<?php
session_start();

// Session protection
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

// Handle Logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Session transaction inventory storage
if (!isset($_SESSION['stock_transactions'])) {
    $_SESSION['stock_transactions'] = [
        ["id" => "TXN1001", "type" => "IN STORE", "details" => "THE ALCHEMIST", "qty" => 1, "datetime" => "25 MAY 2026 ,11:34 AM", "user" => "EMPLOYEE"],
        ["id" => "TXN1002", "type" => "ONLINE", "details" => "ATOMIC HABITS", "qty" => 3, "datetime" => "25 MAY 2026 ,11:24 AM", "user" => "EMPLOYEE"],
        ["id" => "TXN1003", "type" => "IN STORE", "details" => "A SONG OF ICE AND FIRE", "qty" => 1, "datetime" => "25 MAY 2026 ,10:54 AM", "user" => "EMPLOYEE"],
        ["id" => "TXN1004", "type" => "ONLINE", "details" => "FIRE & BLOOD", "qty" => 1, "datetime" => "25 MAY 2026 ,10:34 AM", "user" => "EMPLOYEE"],
        ["id" => "TXN1005", "type" => "IN STORE", "details" => "RICH DAD POOR DAD", "qty" => 2, "datetime" => "25 MAY 2026 ,10:04 AM", "user" => "EMPLOYEE"],
        ["id" => "TXN1006", "type" => "ONLINE", "details" => "CLEAN CODE", "qty" => 1, "datetime" => "25 MAY 2026 ,10:00 AM", "user" => "EMPLOYEE"]
    ];
}

$transactions = $_SESSION['stock_transactions'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Transaction Log - BookShop</title>
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
                        <input type="text" id="searchInput" onkeyup="filterStockTable()" placeholder="SEARCH TRANSACTION LOG..." style="padding: 6px; width: 250px;">
                        <button type="button" class="blue-btn" onclick="filterStockTable()">SEARCH</button>
                    </div>

                    <table id="inventory_table" border="1">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TYPE</th>
                                <th>DETAILS</th>
                                <th>QTY</th>
                                <th>DATE & TIME</th>
                                <th>USER</th>
                            </tr>
                        </thead>
                        <tbody id="stockTableBody">
                            <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($txn['id']); ?></td>
                                <td><?php echo htmlspecialchars($txn['type']); ?></td>
                                <td><?php echo htmlspecialchars($txn['details']); ?></td>
                                <td><?php echo htmlspecialchars(str_pad($txn['qty'], 2, '0', STR_PAD_LEFT)); ?></td>
                                <td><?php echo htmlspecialchars($txn['datetime']); ?></td>
                                <td><?php echo htmlspecialchars($txn['user']); ?></td>
                            </tr>
                            <?php endforeach; ?>
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