<?php
session_start();
require_once 'dbconnection.php';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $email    = trim($_POST['EmailAdd'] ?? '');
    $password = $_POST['Pass'] ?? '';

   
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    
    if (empty($errors)) {
        
$sql = "SELECT UserID, Email, PasswordHash, Role FROM users WHERE Email = ?"; 


$stmt = mysqli_prepare($conn, $sql); 


if ($stmt) { 
    
    mysqli_stmt_bind_param($stmt, "s", $email); 
    
    
    mysqli_stmt_execute($stmt); 
    
    
    $result = mysqli_stmt_get_result($stmt); 

    
    if ($user = mysqli_fetch_assoc($result)) { 
        
        if ($password === $user['PasswordHash']) { 
            
            $role = strtolower($user['Role']); 
            
            
            $_SESSION['user_id'] = $user['UserID']; 
            
            
            $_SESSION['user_role'] = $role; 

            
            if ($role === 'admin') { 
                header("Location: admin_dashboard.php"); 
                exit(); 
                
            
            } elseif ($role === 'employee') { 
                header("Location: employee_dashboard.php"); 
                exit(); 
                
            
            } elseif ($role === 'customer') { 
                header("Location: customer_dashboard.php"); 
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
    
    
    mysqli_stmt_close($stmt); 
    

} else { 
    $errors[] = "Database query failed."; 
}
    }
}
?>

<!DOCTYPE html>
<head>
	<title>Login - BookShop</title>
	<style>
		html, body {
			width: 100%;
			height: 100vh;
			margin: 0;
			padding: 0;
			background-color: #ffffff;
			
		}
		#page_wrapper {
			width: 100%;
			height: 100vh;
			border-collapse: collapse;
		}
		#login_table {
			border-collapse: collapse;
		}
		#left_box {
			width: 400px;
			height: 320px;
			background-color: #ececec;
			text-align: center;
			vertical-align: middle;
			padding: 20px;
		}
		#right_box {
			width: 320px;
			height: 320px;
			vertical-align: top;
			padding-left: 50px;
		}
		h2 {
			margin-top: 0;
			margin-bottom: 20px;
			font-size: 26px;
			font-weight: normal;
			color: #000000;
		}
		label {
			font-size: 13px;
			color: #333333;
		}
		input[type="email"], input[type="password"] {
			width: 100%;
			height: 40px;
			background-color: #e6e1e1;
			border: none;
			margin-top: 5px;
			margin-bottom: 15px;
			padding: 0 10px;
			box-sizing: border-box;
			font-size: 14px;
		}
		input[type="email"]:focus, input[type="password"]:focus {
			background-color: #d8d3d3;
			outline: none;
		}

        .error-text {
        	color: red;
        	font-size: 12px;
        	display: block;
        	margin-bottom: 10px;
        }

		#forgot_link {
			display: block;
			text-align: right;
			font-size: 12px;
			color: #333333;
			text-decoration: none;
			margin-top: -10px;
			margin-bottom: 25px;
		}
		button {
			width: 100%;
			height: 45px;
			background-color: #4c63ee;
			color: white;
			border: none;
			font-size: 16px;
			cursor: pointer;
			margin-bottom: 20px;
			border-radius: 4px;
		}
		button:hover {
			background-color: #3b50cb;
		}
		#signup_text {
			text-align: center;
			font-size: 13px;
			color: #333333;
		}
		#signup_text a {
			color: #4c63ee;
			text-decoration: none;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<?php if (!empty($errors)): ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin: 15px; background-color: #ffe6e6;">
            <h3>Please fix the following errors:</h3>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

	<table id="page_wrapper">
		<tr>
			<td align="center" valign="middle">

				<form action="" method="POST" onsubmit="return validate(this);" novalidate>
					<table id="login_table">
						<tr>
							<td id="left_box">
								<img src="ONLINE_BOOKSHOP_LOGO.jpg" alt="BookShop Logo" width="180">
							</td>
							<td id="right_box">
								<h2>Login</h2>

								<label>Email</label><br>
								<input type="email" name="EmailAdd" id="EmailAdd" value="<?php echo htmlspecialchars($email ?? ''); ?>" ><br>
								<span id="EmailAddErrMsg" class="error-text"></span>


								<label>Password</label><br>
								<input type="password" name="Pass" id="Pass" value="<?php echo htmlspecialchars($password ?? ''); ?>"  ><br>
								<span id="PassErrMsg" class="error-text"></span>
								<a href="forgot_password.php" id="forgot_link">Forgot Password?</a>

								<button type="submit">Login</button>

								<div id="signup_text">
									Don't have an account? <a href="signup.php">Sign up</a>
								</div>
							</td>
						</tr>
					</table>
				</form>

			</td>
		</tr>
	</table>

<script>
        
        function validate(p) {
            const email = p.EmailAdd.value.trim();
            const password = p.Pass.value.trim();

            const EmailAddErrMsg = document.getElementById("EmailAddErrMsg");
            const PassErrMsg = document.getElementById("PassErrMsg");

            EmailAddErrMsg.innerHTML = "";
            PassErrMsg.innerHTML = "";

            let flag = true;

            if (email === "") {
                EmailAddErrMsg.innerHTML = "Please fill up the email properly";
                flag = false;
            }
            if (password === "") {
                PassErrMsg.innerHTML = "Please fill up the password properly";
                flag = false;
            }

            return flag;
        }
    </script>


</body>
</html>