<?php
session_start();
require_once 'dbconnection.php';


if (!isset($_SESSION['user_role']) || strtoupper($_SESSION['user_role']) !== 'ADMIN') {
   
    header("Location: login.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$total_books=0;
$total_customers=0;
$total_sales=0;

$books_query =mysqli_query($conn,"SELECT COUNT(*) AS total FROM books");
if($books_query){
	$total_books=mysqli_fetch_assoc($books_query)['total'] ?? 0;
}

$customers_query = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE UPPER(Role) = 'CUSTOMER'");
if($customers_query){
	$total_customers =mysqli_fetch_assoc($customers_query)['total'] ?? 0;
}

$sales_query = mysqli_query($conn,"SELECT SUM(TotalAmount) AS total FROM orders");
if($sales_query){
	$total_sales=mysqli_fetch_assoc($sales_query)['total']?? 0;
}

$recent_orders =[];
$orders_query = mysqli_query($conn, "SELECT OrderID, TotalAmount, OrderType FROM orders ORDER BY OrderDate DESC LIMIT 5");
if($orders_query){
	while($row = mysqli_fetch_assoc($orders_query)){
		$recent_orders[] =$row;
	}
}

?>


<!DOCTYPE html>
<head>
	<title>Admin Dashboard - BookShop</title>
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
				<b>BOOKSHOP MANAGEMENT</b>
			</td>
			<td align="right"><b>ADMIN DASHBOARD</b></td>
		</tr>
	</table>

	<table id="main_layout">
		<tr>
			<td id="sidebar">
				<a href="admin_dashboard.php" id="active_menu">DASHBOARD</a>
				<a href="admin_inventory.php">BOOKS & INVENTORY</a>
				<a href="admin_users.php">USERS</a>
				<a href="profile_settings.php">SETTINGS</a>
				
				<br><br>
				<div style="padding: 0 20px;">
					<button type="button" onclick="confirmLogout()" style="width: 100%;">LOG OUT</button>
				</div>
			</td>
			<td id="content">
				<fieldset>
					<legend>System Overview</legend>
					<table id="stats_table" border="1">
						<tr>
							<td>TOTAL BOOKS<br><br><h2><?php echo number_format($total_books); ?></h2></td>
							<td>TOTAL CUSTOMERS<br><br><h2><?php echo number_format($total_customers); ?></h2></td>
							<td>TOTAL SALES<br><br><h2><?php echo number_format($total_sales); ?></h2></td>
						</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend>Recent Transactions</legend>
					<table id="orders_table" border="1">
						<tr>
							<th>ORDER ID</th>
							<th>AMOUNT</th>
							<th>TYPE</th>
						</tr>
					<?php if(!empty($recent_orders)): ?>
					<?php foreach ($recent_orders as $order): ?>
					<tr>
						<td>#O<?php echo htmlspecialchars($order['OrderID']); ?></td>
						<td>BDT <?php echo number_format($order['TotalAmount'],2); ?></td>
						<td><?php echo htmlspecialchars(strtoupper($order['OrderType'] ?? 'ONLINE')); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="3">No recent transactions found. </td>
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
                window.location.href = "admin_dashboard.php?action=logout";
            }
        }
    </script>


</body>
</html>