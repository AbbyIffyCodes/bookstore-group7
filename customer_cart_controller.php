<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/CustomerModel.php';

if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'customer') {
    header("Location: login_controller.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$errors = [];
$success_msg = "";
$delivery_fee = 60;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['action']) && $_POST['action'] === 'update_cart') {
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $b_id => $qty) {
                $b_id = intval($b_id);
                $qty = intval($qty);

                if ($qty <= 0) {
                    unset($_SESSION['cart'][$b_id]);
                } else if (isset($_SESSION['cart'][$b_id])) {
                    $_SESSION['cart'][$b_id]['qty'] = $qty;
                }
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'checkout') {
        if (empty($_SESSION['cart'])) {
            $errors[] = "Your cart is completely empty. Please add books from the shop before checking out.";
        }

        if (empty($errors)) {
            $user_id = $_SESSION['user_id'] ?? null;
            $result = placeCustomerOrder($user_id, $_SESSION['cart'], $delivery_fee);

            if ($result['success']) {
                $_SESSION['cart'] = [];
                $success_msg = "Order #" . $result['order_number'] . " placed successfully! Thank you for purchasing.";
            } else {
                $errors[] = "Failed to place order: " . $result['error'];
            }
        }
    }
}

$subtotal = 0;
$total_items = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['qty']);
    $total_items += $item['qty'];
}

$grand_total = ($subtotal > 0) ? ($subtotal + $delivery_fee) : 0;

include_once '../views/customer_cart.php';
?>