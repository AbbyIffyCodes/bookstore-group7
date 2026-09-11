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

if (isset($_GET['action']) && $_GET['action'] === 'search_books') {
    header('Content-Type: application/json');
    $category = $_GET['category'] ?? '';
    $query = $_GET['query'] ?? '';

    $books = searchCatalogBooks($query, $category);
    echo json_encode($books);
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);

    if ($book_id <= 0) {
        $message = "Invalid book selection.";
    } else {
        $selected_book = getBookForCart($book_id);

        if ($selected_book) {
            if (isset($_SESSION['cart'][$book_id])) {
                $_SESSION['cart'][$book_id]['qty'] += 1;
            } else {
                $_SESSION['cart'][$book_id] = [
                    'id'    => $selected_book['BookID'],
                    'title' => $selected_book['Title'],
                    'price' => $selected_book['Price'],
                    'image' => !empty($selected_book['Image']) ? $selected_book['Image'] : '../public/images/default_cover.jpg',
                    'qty'   => 1
                ];
            }
            $message = "Added '" . htmlspecialchars($selected_book['Title']) . "' to your cart!";
        } else {
            $message = "Book not found.";
        }
    }
}

$books = getAllCatalogBooks();

$total_cart_items = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_items += $item['qty'];
}

include_once '../views/customer_shop.php';
?>