<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/UserModel.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $EmailAdd = trim($_POST['EmailAdd'] ?? '');
    $Pass     = $_POST['Pass'] ?? '';
    $CPass    = $_POST['CPass'] ?? '';

    $errors = [];

    if (empty($EmailAdd)) {
        $errors[] = "Email address is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $EmailAdd)) {
        $errors[] = "Invalid email address format.";
    }

    if (empty($Pass)) {
        $errors[] = "New password is required.";
    } elseif (strlen($Pass) < 6) {
        $errors[] = "New password must be at least 6 characters long.";
    }

    if (empty($CPass)) {
        $errors[] = "Please confirm your new password.";
    } elseif ($Pass !== $CPass) {
        $errors[] = "New password and Confirm password do not match.";
    }

    if (empty($errors)) {
        $user = getUserByEmail($conn, $EmailAdd);

        if ($user) {
            $hashedPassword = password_hash($Pass, PASSWORD_DEFAULT);
            if (updateUserPassword($conn, $user['UserID'], $hashedPassword)) {
                $_SESSION['success_msg'] = "Password updated successfully!";
            } else {
                $_SESSION['errors'][] = "Failed to update password. Please try again.";
            }
        } else {
            $_SESSION['errors'][] = "No account found with that email address.";
        }
    } else {
        $_SESSION['errors'] = $errors;
    }

    $_SESSION['old_email'] = $EmailAdd;
    header("Location: forgot_password_controller.php");
    
}

include_once '../views/forgot_password.php'; 
exit();
?>