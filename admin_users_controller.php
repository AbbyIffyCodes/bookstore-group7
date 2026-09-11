<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/UserModel.php';

if (!isset($_SESSION['user_role']) || strtoupper($_SESSION['user_role']) !== 'ADMIN') {
    header("Location: login_controller.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === "add_user") {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? '');
        $errors = [];

        if (empty($name)) {
            $errors[] = "User name is required.";
        } elseif (strlen($name) < 2) {
            $errors[] = "Name must be at least 2 characters.";
        }

        if (empty($email)) {
            $errors[] = "Email address is required.";
        } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $errors[] = "Invalid email format.";
        } else {
            if (isEmailRegistered($conn, $email)) {
                $errors[] = "Email is already registered.";
            }
        }

        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }

        $allowed_roles = ["ADMIN", "EMPLOYEE", "CUSTOMER"];
        if (empty($role) || !in_array($role, $allowed_roles)) {
            $errors[] = "Please select a valid role.";
        }

        if (empty($errors)) {
            $custom_id = generateCustomUserId($conn);
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $status = "Active";

            if (insertUser($conn, $custom_id, $name, $email, $password_hash, $role, $status)) {
                $_SESSION['success_msg'] = "User successfully added!";
            } else {
                $_SESSION['errors'][] = "Database error: " . $conn->error;
            }
        } else {
            $_SESSION['errors'] = $errors;
        }

        header("Location: admin_users_controller.php");
        exit();

    } elseif ($action === "toggle_status") {
        header('Content-Type: application/json');    
        $userId = $_POST['user_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if ($userId && in_array(strtolower($status), ['active', 'inactive'])) {
            $formattedStatus = ucfirst(strtolower($status));
            
            if (updateUserStatus($conn, $formattedStatus, $userId)) {
                echo json_encode([
                    "success" => true,
                    "new_status" => strtoupper($formattedStatus),
                    "next_status" => (strtoupper($formattedStatus) === 'ACTIVE') ? 'inactive' : 'active',
                    "next_button_text" => (strtoupper($formattedStatus) === 'ACTIVE') ? 'Deactivate' : 'Activate'
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Database update failed."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid parameters."]);
        }
        exit();
    }
}

$_SESSION['users'] = getAllUsers($conn);

include_once '../views/admin_users.php'; 
exit();
?>