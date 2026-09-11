<?php
require_once __DIR__ . '/dbconnection.php';

if (!function_exists('getUserByEmail')){
function getUserByEmail($conn, $email) {
    $sql = "SELECT UserID, Email, PasswordHash, Role FROM users WHERE Email = ?"; 
    $stmt = mysqli_prepare($conn, $sql); 
    
    if ($stmt) { 
        mysqli_stmt_bind_param($stmt, "s", $email); 
        mysqli_stmt_execute($stmt); 
        $result = mysqli_stmt_get_result($stmt); 
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt); 
        return $user;
    }
    
    return false;
}

 }
if (!function_exists('getTotalCustomersCount')){
function getTotalCustomersCount($conn) {
    $customers_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE UPPER(Role) = 'CUSTOMER'");
    if ($customers_query) {
        return mysqli_fetch_assoc($customers_query)['total'] ?? 0;
    }
    return 0;
}

 }

if (!function_exists('isEmailRegistered')){
function isEmailRegistered($conn, $email) {
    $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    $exists = $checkStmt->num_rows > 0;
    $checkStmt->close();
    return $exists;
}

 }


if (!function_exists('generateCustomUserId')){
function generateCustomUserId($conn) {
    $maxRes = $conn->query("SELECT MAX(UserID) AS max_id FROM users");
    $maxId = $maxRes->fetch_assoc()['max_id'] ?? 0;
    return "T" . str_pad($maxId + 1, 3, "0", STR_PAD_LEFT);
}

 }

if (!function_exists('insertUser')){
function insertUser($conn, $custom_id, $name, $email, $password_hash, $role, $status) {
    $stmt = $conn->prepare("INSERT INTO users (CustomUserID, FullName, Email, PasswordHash, Role, Status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $custom_id, $name, $email, $password_hash, $role, $status);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

 }
if (!function_exists('updateUserStatus')){
function updateUserStatus($conn, $formattedStatus, $userId) {
    $stmt = $conn->prepare("UPDATE users SET Status = ? WHERE CustomUserID = ?");
    $stmt->bind_param("ss", $formattedStatus, $userId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

 }
if (!function_exists('getAllUsers')){
function getAllUsers($conn) {
    $users = [];
    $result = $conn->query("SELECT CustomUserID AS id, FullName AS name, Role AS role, Email AS email, Status AS status FROM users ORDER BY UserID DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

 }
if (!function_exists('getUserById')){
function getUserById($conn, $user_id) {
    $stmt = $conn->prepare("SELECT FullName, Email, Phone, ShippingAddress, PasswordHash FROM users WHERE UserID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

 }
if (!function_exists('isEmailTakenByOtherUser')){
function isEmailTakenByOtherUser($conn, $email, $user_id) {
    $stmt = $conn->prepare("SELECT UserID FROM users WHERE Email = ? AND UserID != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

 }
if (!function_exists('updateUserProfile')){
function updateUserProfile($conn, $user_id, $name, $email, $phone, $address, $new_password_hash = null) {
    if ($new_password_hash) {
        $stmt = $conn->prepare("UPDATE users SET FullName = ?, Email = ?, Phone = ?, ShippingAddress = ?, PasswordHash = ? WHERE UserID = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $address, $new_password_hash, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET FullName = ?, Email = ?, Phone = ?, ShippingAddress = ? WHERE UserID = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
    }
    
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

 }


if (!function_exists('registerUser')){
function registerUser($conn, $custom_id, $name, $email, $phone, $password_hash, $role) {
    $stmt = $conn->prepare("INSERT INTO users (CustomUserID, FullName, Email, Phone, PasswordHash, Role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $custom_id, $name, $email, $phone, $password_hash, $role);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

 }




if (!function_exists('updateUserPassword')){
function updateUserPassword($conn, $user_id, $password_hash) {
    $stmt = $conn->prepare("UPDATE users SET PasswordHash = ? WHERE UserID = ?");
    $stmt->bind_param("si", $password_hash, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

 }
?>