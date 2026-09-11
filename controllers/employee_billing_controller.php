<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/EmployeeModel.php';

if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'employee') {
    header("Location: login_controller.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login_controller.php");
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
            $book = getBookByTitle($title);

            if ($book) {
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
        }
    } elseif ($action === 'complete_bill') {
        if (empty($_SESSION['billing_cart'])) {
            $errors[] = "Cannot complete an empty bill.";
        } else {
            $user_id = $_SESSION['user_id'] ?? NULL;
            $result = processBillingTransaction($_SESSION['billing_cart'], $user_id);

            if ($result['success']) {
                $_SESSION['billing_cart'] = [];
                $success_msg = "Transaction completed and order recorded successfully! (Order #" . $result['order_number'] . ")";
            } else {
                $errors[] = "Transaction failed: " . $result['error'];
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

include_once '../views/employee_billing.php';
?>