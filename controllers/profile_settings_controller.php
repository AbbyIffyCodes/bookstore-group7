<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/UserModel.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login_controller.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = strtoupper($_SESSION['user_role']);

switch ($user_role) {
    case 'ADMIN':
        $dashboard_url = 'admin_dashboard_controller.php';
        break;
    case 'EMPLOYEE':
        $dashboard_url = 'employee_dashboard_controller.php';
        break;
    case 'CUSTOMER':
    default:
        $dashboard_url = 'customer_dashboard_controller.php';
        break;
}

$currentUser = getUserById($conn, $user_id);

if (!$currentUser) {
    session_destroy();
    header("Location: login_controller.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $FName    = trim($_POST['FName'] ?? '');
    $EmailAdd = trim($_POST['EmailAdd'] ?? '');
    $PHnumber = trim($_POST['PHnumber'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $CurPass  = $_POST['CurPass'] ?? '';
    $Pass     = $_POST['Pass'] ?? '';
    $CPass    = $_POST['CPass'] ?? '';

    $errors = [];

    if (empty($FName)) {
        $errors[] = "Full Name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $FName)) {
        $errors[] = "Full Name can only contain letters and spaces.";
    }

    if (empty($EmailAdd)) {
        $errors[] = "Email address is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $EmailAdd)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($PHnumber)) {
        $errors[] = "Contact number is required.";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $PHnumber)) {
        $errors[] = "Contact number must be between 10 and 15 digits.";
    }

    if (empty($address)) {
        $errors[] = "Shipping address is required.";
    }

    $changePassword = false;
    if (!empty($CurPass) || !empty($Pass) || !empty($CPass)) {
        if (empty($CurPass)) {
            $errors[] = "Current password is required to set a new password.";
        } elseif (!password_verify($CurPass, $currentUser['PasswordHash'])) {
            $errors[] = "Current password is incorrect.";
        }

        if (empty($Pass)) {
            $errors[] = "New password is required.";
        } elseif (strlen($Pass) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        }

        if ($Pass !== $CPass) {
            $errors[] = "New password and Confirm password do not match.";
        }

        if (empty($errors)) {
            $changePassword = true;
        }
    }

    if (empty($errors)) {
        if (isEmailTakenByOtherUser($conn, $EmailAdd, $user_id)) {
            $errors[] = "Email address is already in use by another account.";
        }
    }

    if (empty($errors)) {
        $hashedPassword = $changePassword ? password_hash($Pass, PASSWORD_DEFAULT) : null;
        
        if (updateUserProfile($conn, $user_id, $FName, $EmailAdd, $PHnumber, $address, $hashedPassword)) {
            $_SESSION['success_msg'] = "Profile updated successfully!";
        } else {
            $_SESSION['errors'][] = "Failed to update profile. Please try again.";
        }
    } else {
        $_SESSION['errors'] = $errors;
    }

    header("Location: profile_settings_controller.php");
    exit();
}

$_SESSION['profile_data'] = [
    'FName' => $currentUser['FullName'] ?? '',
    'EmailAdd' => $currentUser['Email'] ?? '',
    'PHnumber' => $currentUser['Phone'] ?? '',
    'address' => $currentUser['ShippingAddress'] ?? '',
    'dashboard_url' => $dashboard_url
];

include_once '../views/profile_settings.php'; 
exit();
?>