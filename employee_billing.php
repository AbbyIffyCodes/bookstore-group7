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

if (!isset($_SESSION['billing_cart'])) {
    $_SESSION['billing_cart'] = [];
}

$errors = [];
$success_msg = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_item') {
        $title = trim($_POST['title'] ?? '');
        $qty   = trim($_POST['qty'] ?? '');

        if (empty($title)) {
            $errors[] = "Book title is required.";
        }
        if ($qty === "" || !is_numeric($qty) || (int)$qty <= 0) {
            $errors[] = "Quantity must be at least 1.";
        }

        if (empty($errors)) {
            
            $stmt = $conn->prepare("SELECT BookID, Title, Price, Stock FROM books WHERE Title = ? LIMIT 1");
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $book = $res->fetch_assoc()) {
                $requested_qty = (int)$qty;
                if ($book['Stock'] < $requested_qty) {
                    $errors[] = "Insufficient stock! Only " . $book['Stock'] . " available for '" . $book['Title'] . "'.";
                } else {
                    $_SESSION['billing_cart'][] = [
                        "book_id" => $book['BookID'],
                        "title"   => strtoupper($book['Title']),
                        "price"   => (float)$book['Price'],
                        "qty"     => $requested_qty
                    ];
                    $success_msg = "Item added to bill!";
                }
            } else {
                $errors[] = "Book titled '" . htmlspecialchars($title) . "' not found in database.";
            }
            $stmt->close();
        }
    } elseif ($action === 'complete_bill') {
        if (empty($_SESSION['billing_cart'])) {
            $errors[] = "Cannot complete an empty bill.";
        } else {
           
            $conn->begin_transaction();

            try {
                $subtotal = 0;
                foreach ($_SESSION['billing_cart'] as $item) {
                    $subtotal += ($item['price'] * $item['qty']);
                }

                $order_number = "ORD-" . time();
                $user_id = $_SESSION['user_id'] ?? NULL;
                $order_type = "In Store";
                $status = "Completed";

                $stmt_order = $conn->prepare("INSERT INTO orders (OrderNumber, UserID, TotalAmount, OrderType, Status) VALUES (?, ?, ?, ?, ?)");
                $stmt_order->bind_param("sidss", $order_number, $user_id, $subtotal, $order_type, $status);
                $stmt_order->execute();
                $order_id = $stmt_order->insert_id;
                $stmt_order->close();

                
                $stmt_item = $conn->prepare("INSERT INTO orderitems (OrderID, BookID, Quantity, UnitPrice) VALUES (?, ?, ?, ?)");
                $stmt_stock = $conn->prepare("UPDATE books SET Stock = Stock - ? WHERE BookID = ?");

                foreach ($_SESSION['billing_cart'] as $item) {
                    
                    $stmt_item->bind_param("iiid", $order_id, $item['book_id'], $item['qty'], $item['price']);
                    $stmt_item->execute();

              
                    $stmt_stock->bind_param("ii", $item['qty'], $item['book_id']);
                    $stmt_stock->execute();
                }

                $stmt_item->close();
                $stmt_stock->close();

                $conn->commit();
                $_SESSION['billing_cart'] = [];
                $success_msg = "Transaction completed and order recorded successfully! (Order #" . $order_number . ")";
            } catch (Exception $e) {
                $conn->rollback();
                $errors[] = "Transaction failed: " . $e->getMessage();
            }
        }
    }
}


$total_items = 0;
$subtotal = 0;
foreach ($_SESSION['billing_cart'] as $item) {
    $total_items += $item['qty'];
    $subtotal += ($item['price'] * $item['qty']);
}
$discount = 0;
$total = $subtotal - $discount;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Billing Counter - BookShop</title>
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
            padding: 12px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        #items_table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        #items_table th {
            background-color: #5d83e1;
            color: white;
            padding: 12px;
        }
        #items_table td {
            background-color: #5911eb;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }
        #summary_table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        #summary_table td {
            padding: 8px 0;
            font-weight: bold;
        }
        #actions_table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .alert-error {
            background-color: #ffcccc;
            color: #990000;
            border: 1px solid #990000;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .js-error {
            color: red;
            font-size: 12px;
            display: block;
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
                <a href="employee_billing.php" id="active_menu">BILLING</a>
                <a href="employee_transaction.php">STOCK</a>
                <a href="profile_settings.php">SETTINGS</a>
                <br><br>
                <div style="padding: 0 20px;">
                    <button type="button" class="logout-btn" onclick="confirmLogout()">LOG OUT</button>
                </div>
            </td>
            <td id="content">
                <?php if (!empty($errors)): ?>
                    <div class="alert-error">
                        <ul style="margin:0; padding-left:20px;">
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success_msg)): ?>
                    <div class="alert-success"><b><?php echo htmlspecialchars($success_msg); ?></b></div>
                <?php endif; ?>

                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td valign="top" width="65%" style="padding-right: 15px;">
                            <fieldset>
                                <legend>Search / Add Book</legend>
                                <form method="POST" action="employee_billing.php" id="addItemForm" onsubmit="return validateAddItem();">
                                    <input type="hidden" name="action" value="add_item">
                                    <input type="text" id="itemTitle" name="title" placeholder="Exact Book Title..." style="width: 60%; padding: 8px;">
                                    <input type="number" id="itemQty" name="qty" placeholder="Qty" value="1" min="1" style="width: 20%; padding: 8px;">
                                    <button type="submit" class="blue-btn" style="width: 15%; padding: 8px; display: inline-block;">ADD</button>
                                    <span id="err_msg" class="js-error"></span>
                                </form>
                            </fieldset>

                            <fieldset>
                                <legend>Selected Items</legend>
                                <table id="items_table">
                                    <tr>
                                        <th>BOOK</th>
                                        <th>PRICE</th>
                                        <th>QTY</th>
                                        <th>TOTAL</th>
                                    </tr>
                                    <?php if (!empty($_SESSION['billing_cart'])): ?>
                                        <?php foreach ($_SESSION['billing_cart'] as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                                            <td><?php echo htmlspecialchars($item['price']); ?>TK</td>
                                            <td><?php echo htmlspecialchars($item['qty']); ?></td>
                                            <td><?php echo htmlspecialchars($item['price'] * $item['qty']); ?>TK</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No items added yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </fieldset>
                        </td>

                        <td valign="top" width="35%">
                            <fieldset>
                                <legend>Bill Summary</legend>
                                <table id="summary_table">
                                    <tr>
                                        <td>Items</td>
                                        <td align="right"><?php echo $total_items; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td align="right"><?php echo $subtotal; ?>TK</td>
                                    </tr>
                                    <tr>
                                        <td>Discount</td>
                                        <td align="right"><?php echo $discount; ?>TK</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><hr></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total</b></td>
                                        <td align="right"><b><?php echo $total; ?>TK</b></td>
                                    </tr>
                                </table>
                            </fieldset>

                            <table id="actions_table">
                                <tr>
                                    <td>
                                        <form method="POST" action="employee_billing.php">
                                            <input type="hidden" name="action" value="complete_bill">
                                            <button type="submit" class="blue-btn">COMPLETE</button>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "employee_billing.php?action=logout";
            }
        }

        function validateAddItem() {
            const title = document.getElementById('itemTitle').value.trim();
            const qty = document.getElementById('itemQty').value.trim();
            const errSpan = document.getElementById('err_msg');

            if (title === "" || qty === "") {
                errSpan.innerText = "Please provide book title and quantity.";
                return false;
            }
            if (parseInt(qty) <= 0) {
                errSpan.innerText = "Quantity must be greater than zero.";
                return false;
            }
            errSpan.innerText = "";
            return true;
        }
    </script>
</body>
</html>