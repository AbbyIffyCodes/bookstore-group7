<?php
require_once __DIR__ . '/dbconnection.php';

function getTotalBooksCount($conn) {
    $books_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM books");
    if ($books_query) {
        return mysqli_fetch_assoc($books_query)['total'] ?? 0;
    }
    return 0;
}

function getAllBooks($conn) {
    $inventory = [];
    $query = mysqli_query($conn, "SELECT BookID, CustomBookID, Title, Category, Stock, Price, Image FROM books ORDER BY BookID DESC");
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $inventory[] = $row;
        }
    }
    return $inventory;
}

function getBookImageById($conn, $book_id) {
    $stmt = mysqli_prepare($conn, "SELECT Image FROM books WHERE BookID = ?");
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    return $row['Image'] ?? null;
}

function updateBookWithImage($conn, $formatted_title, $category, $stock_val, $price_val, $target_path, $book_id) {
    $stmt = mysqli_prepare($conn, "UPDATE books SET Title = ?, Category = ?, Stock = ?, Price = ?, Image = ? WHERE BookID = ?");
    mysqli_stmt_bind_param($stmt, "ssidsi", $formatted_title, $category, $stock_val, $price_val, $target_path, $book_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function updateBookWithoutImage($conn, $formatted_title, $category, $stock_val, $price_val, $book_id) {
    $stmt = mysqli_prepare($conn, "UPDATE books SET Title = ?, Category = ?, Stock = ?, Price = ? WHERE BookID = ?");
    mysqli_stmt_bind_param($stmt, "ssidi", $formatted_title, $category, $stock_val, $price_val, $book_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function generateCustomBookId($conn, $prefix) {
    $like_pattern = $prefix . "%";
    $id_query = "SELECT MAX(CAST(SUBSTRING(CustomBookID, ?) AS UNSIGNED)) AS max_num FROM books WHERE CustomBookID LIKE ?";
    $id_stmt = mysqli_prepare($conn, $id_query);
    $substr_start = strlen($prefix) + 1;
    mysqli_stmt_bind_param($id_stmt, "is", $substr_start, $like_pattern);
    mysqli_stmt_execute($id_stmt);
    $id_res = mysqli_stmt_get_result($id_stmt);
    $id_row = mysqli_fetch_assoc($id_res);
    mysqli_stmt_close($id_stmt);
    
    $next_num = (!empty($id_row['max_num']) && $id_row['max_num'] >= 1000) ? ((int)$id_row['max_num'] + 1) : 1001;
    return $prefix . $next_num;
}

function insertBook($conn, $custom_book_id, $formatted_title, $category, $stock_val, $price_val) {
    $stmt = mysqli_prepare($conn, "INSERT INTO books (CustomBookID, Title, Category, Stock, Price) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssid", $custom_book_id, $formatted_title, $category, $stock_val, $price_val);
    if (mysqli_stmt_execute($stmt)) {
        $new_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $new_id;
    }
    mysqli_stmt_close($stmt);
    return false;
}

function updateBookImage($conn, $target_path, $new_id) {
    $update_img_stmt = mysqli_prepare($conn, "UPDATE books SET Image = ? WHERE BookID = ?");
    mysqli_stmt_bind_param($update_img_stmt, "si", $target_path, $new_id);
    $result = mysqli_stmt_execute($update_img_stmt);
    mysqli_stmt_close($update_img_stmt);
    return $result;
}

function deleteBook($conn, $book_id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM books WHERE BookID = ?");
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}
?>