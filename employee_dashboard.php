<?php
session_start();


if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employee') {
    header("Location: login.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$employee_name = isset($_SESSION['username']) ? strtoupper($_SESSION['username']) : 'MAYESHA';
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
                        <td>PENDING CONFIRMATION<br><br><h2>5</h2></td>
                        <td>ACTIVE DELIVERIES<br><br><h2>1</h2></td>
                        <td>OUT OF STOCK ITEMS<br><br><h2>3</h2></td>
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
                        <tr>
                            <td>#O101</td>
                            <td>BDT 420</td>
                            <td>IN STORE</td>
                        </tr>
                        <tr>
                            <td>#O102</td>
                            <td>BDT 620</td>
                            <td>ONLINE</td>
                        </tr>
                        <tr>
                            <td>#O103</td>
                            <td>BDT 220</td>
                            <td>IN STORE</td>
                        </tr>
                        <tr>
                            <td>#O104</td>
                            <td>BDT 1000</td>
                            <td>ONLINE</td>
                        </tr>
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
