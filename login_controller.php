<?php

session_start();
require_once '../models/dbconnection.php';
require_once '../models/UserModel.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['EmailAdd'] ?? '');
    $password = $_POST['Pass'] ?? '';

    
    $_SESSION['old_email'] = $email;
    $_SESSION['old_password'] = $password;

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $user = getUserByEmail($conn, $email);

        if ($user) { 
            if (password_verify($password, $user['PasswordHash'])) { 
                $role = strtolower($user['Role']); 
                
                $_SESSION['user_id'] = $user['UserID']; 
                $_SESSION['user_role'] = $role; 

             
                unset($_SESSION['login_errors']);
                unset($_SESSION['old_email']);
                unset($_SESSION['old_password']);

                if ($role === 'admin') { 
                    header("Location: admin_dashboard_controller.php"); 
                    exit(); 
                } elseif ($role === 'employee') { 
                    header("Location:employee_dashboard_controller.php"); 
                    exit(); 
                } elseif ($role === 'customer') { 
                    header("Location:customer_dashboard_controller.php"); 
                    exit(); 
                } else { 
                    $errors[] = "Unauthorized access role."; 
                }
            } else { 
                $errors[] = "Invalid email or password."; 
            }
        } else { 
            $errors[] = "Invalid email or password."; 
        }
    }

   
    $_SESSION['login_errors'] = $errors;


    
}
include_once '../views/login.php'; 
?>