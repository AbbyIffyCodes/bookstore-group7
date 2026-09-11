<?php
session_start();
require_once '../models/dbconnection.php';
require_once '../models/UserModel.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fname     = trim($_POST['FName'] ?? '');
    $email     = trim($_POST['EmailAdd'] ?? '');
    $phone     = trim($_POST['PHnumber'] ?? '');
    $password  = $_POST['Pass'] ?? '';
    $cpassword = $_POST['CPass'] ?? '';

    $errors = [];
    $field_errors = [
        'fnameErr' => '',
        'emailErr' => '',
        'phoneErr' => '',
        'passErr'  => '',
        'cpassErr' => ''
    ];

    if (empty($fname)) {
        $field_errors['fnameErr'] = "Full Name is required.";
        $errors[] = "Full Name is required.";
    }

    if (empty($email)) {
        $field_errors['emailErr'] = "Email is required.";
        $errors[] = "Email is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $field_errors['emailErr'] = "Invalid email format.";
        $errors[] = "Invalid email format.";
    }

    if (empty($phone)) {
        $field_errors['phoneErr'] = "Phone number is required.";
        $errors[] = "Phone number is required.";
    } elseif (!preg_match("/^[0-9]{7,15}$/", $phone)) {
        $field_errors['phoneErr'] = "Phone number must contain between 7 and 15 digits.";
        $errors[] = "Phone number must contain between 7 and 15 digits.";
    }

    if (empty($password)) {
        $field_errors['passErr'] = "Password is required.";
        $errors[] = "Password is required.";
    }

    if ($password !== $cpassword) {
        $field_errors['cpassErr'] = "Password and Confirm Password do not match.";
        $errors[] = "Password and Confirm Password do not match.";
    }

    if (empty($errors)) {
        if (isEmailRegistered($conn, $email)) {
            $field_errors['emailErr'] = "Email address is already registered.";
            $errors[] = "Email address is already registered.";
        } else {
            $role = 'CUSTOMER';
            $emailParts = explode('@', $email); 
            if (isset($emailParts[0])) {
                $usernameParts = explode('_', $emailParts[0]); 
                $lastPart = strtoupper(end($usernameParts)); 

                if ($lastPart === 'ADMIN') {
                    $role = 'ADMIN';
                } elseif ($lastPart === 'EMPLOYEE') {
                    $role = 'EMPLOYEE';
                } elseif ($lastPart === 'CUSTOMER') {
                    $role = 'CUSTOMER';
                }
            }

            $custom_id = generateCustomUserId($conn);
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            if (registerUser($conn, $custom_id, $fname, $email, $phone, $password_hash, $role)) {
                header("Location: login_controller.php?registered=success");
                exit();
            } else {
                $errors[] = "Failed to register user. Please try again.";
            }
        }
    }

    $_SESSION['errors'] = $errors;
    $_SESSION['field_errors'] = $field_errors;
    $_SESSION['old_input'] = [
        'fname' => $fname,
        'email' => $email,
        'phone' => $phone
    ];

    header("Location: signup_controller.php");
   
}

include_once '../views/signup.php';
exit();
?>