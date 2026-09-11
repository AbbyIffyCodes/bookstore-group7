<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/BookModel.php';

if (!isset($_SESSION['user_role']) || strtoupper($_SESSION['user_role']) !== 'ADMIN') {
    header("Location:login_controller.php");
    exit();
}

$category_map = [
    'Science'     => 'S',
    'History'     => 'H',
    'Business'    => 'B',
    'Programming' => 'P',
    'Self-Help'   => 'SH',
    'Fiction'     => 'F'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $post_errors = [];

    if ($action === "save_book") {
        $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $stock = trim($_POST['stock'] ?? '');
        $price = trim($_POST['price'] ?? '');

        if ($title === '') { 
            $post_errors[] = "Book title is required."; 
        }

        if ($category === '' || !array_key_exists($category, $category_map)) {
            $post_errors[] = "Please select a valid category.";
        }

        if ($stock === '' || !ctype_digit($stock) || (int)$stock < 0) { 
            $post_errors[] = "Stock must be a non-negative integer."; 
        }

        if ($price === '' || !is_numeric($price) || (float)$price <= 0) { 
            $post_errors[] = "Price must be a positive number."; 
        }

        $has_file = isset($_FILES['book_image']) && $_FILES['book_image']['error'] === UPLOAD_ERR_OK;
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/webp'];
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

        if ($has_file) {
            $file_tmp = $_FILES['book_image']['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file_tmp);
            finfo_close($finfo);

            if (!in_array($file_ext, $allowed_exts, true) || !in_array($mime_type, $allowed_mime_types, true)) {
                $post_errors[] = "Invalid image file type. Only JPG, PNG, and WEBP images are allowed.";
            }
        }

        if (empty($post_errors)) {
            $formatted_title = mb_strtoupper($title, 'UTF-8');
            $stock_val = (int)$stock;
            $price_val = (float)$price;
            $upload_dir = "../public/images/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (!empty($book_id)) {
                if ($has_file) {
                    $new_filename = "TX" . $category_map[$category] . $book_id . "_" . time() . "." . $file_ext;
                    $target_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['book_image']['tmp_name'], $target_path)) {
                        $old_img = getBookImageById($conn, $book_id);
                        if (!empty($old_img) && file_exists($old_img)) {
                            @unlink($old_img);
                        }
                        $status = updateBookWithImage($conn, $formatted_title, $category, $stock_val, $price_val, $target_path, $book_id);
                    }
                } else {
                    $status = updateBookWithoutImage($conn, $formatted_title, $category, $stock_val, $price_val, $book_id);
                }

                if (isset($status) && $status) {
                    $_SESSION['success_msg'] = "Book #" . $book_id . " updated successfully!";
                } else {
                    $_SESSION['errors'][] = "Database update error: " . mysqli_error($conn);
                }

            } else {
                $prefix = "TX" . $category_map[$category];
                $custom_book_id = generateCustomBookId($conn, $prefix);
                $new_id = insertBook($conn, $custom_book_id, $formatted_title, $category, $stock_val, $price_val);

                if ($new_id) {
                    if ($has_file) {
                        $new_filename = $custom_book_id . "_" . time() . "." . $file_ext;
                        $target_path = $upload_dir . $new_filename;

                        if (move_uploaded_file($_FILES['book_image']['tmp_name'], $target_path)) {
                            updateBookImage($conn, $target_path, $new_id);
                        }
                    }
                    $_SESSION['success_msg'] = "New book added successfully with ID #" . $new_id . "!";
                } else {
                    $_SESSION['errors'][] = "Database insert error: " . mysqli_error($conn);
                }
            }
        } else {
            $_SESSION['errors'] = $post_errors;
        }

    } elseif ($action === "delete_book") {
        $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
        if ($book_id) {
            $old_img = getBookImageById($conn, $book_id);
            if (!empty($old_img) && file_exists($old_img)) {
                @unlink($old_img);
            }

            if (deleteBook($conn, $book_id)) {
                $_SESSION['success_msg'] = "Book #" . $book_id . " deleted successfully!";
            } else {
                $_SESSION['errors'][] = "Failed to delete book from database.";
            }
        }
    }

    header("Location: admin_inventory_controller.php");
    exit();
}

$_SESSION['inventory'] = getAllBooks($conn);

include_once '../views/admin_inventory.php'; 
exit();
?>