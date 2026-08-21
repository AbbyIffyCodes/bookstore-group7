<?php
session_start();


if (!isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

$user_role = strtolower($_SESSION['user_role']); 

switch ($user_role) {
    case 'admin':
        $dashboard_url = 'admin_dashboard.php';
        break;
    case 'employee':
        $dashboard_url = 'employee_dashboard.php';
        break;
    case 'customer':
    default:
        $dashboard_url = 'customer_dashboard.php';
        break;
}

$errors = [];
$success_msg = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $FName    = trim($_POST['FName'] ?? '');
    $EmailAdd = trim($_POST['EmailAdd'] ?? '');
    $PHnumber = trim($_POST['PHnumber'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $CurPass  = $_POST['CurPass'] ?? '';
    $Pass     = $_POST['Pass'] ?? '';
    $CPass    = $_POST['CPass'] ?? '';

    
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

    
    if (!empty($CurPass) || !empty($Pass) || !empty($CPass)) {
        if (empty($CurPass)) {
            $errors[] = "Current password is required to set a new password.";
        }
        if (strlen($Pass) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        }
        if ($Pass !== $CPass) {
            $errors[] = "New password and Confirm password do not match.";
        }
    }

    
    if (empty($errors)) {
        
        $success_msg = "Profile updated successfully!";
    }
}
?>
<!DOCTYPE html>
<head>
	<title>Settings - BookShop</title>
	<style>
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
			background-color: lightblue;
		}
		#header_table {
			width: 100%;
			height: 50px;
			background-color: blue;
			color: white;
			padding: 0 20px;
		}
		#main_layout {
			width: 100%;
			height: calc(100vh - 50px);
			border-collapse: collapse;
		}
		#sidebar {
			width: 220px;
			vertical-align: top;
			background-color: #4c63ee;
			padding-top: 10px;
		}
		#sidebar a {
			display: block;
			color: white;
			text-decoration: none;
			font-weight: bold;
			padding: 12px 20px;
		}
		#sidebar a:hover {
            background-color: #3b4ecc;
        }
		#active_menu {
			background-color: grey;
		}
		#content {
			vertical-align: top;
			padding: 20px;
		}
		fieldset {
			border-color: blue;
			background-color: white;
			margin-bottom: 20px;
			padding: 15px;
		}
		.logout-btn {
			background-color: grey;
			color: white;
			padding: 10px 20px;
			border: none;
			cursor: pointer;
			font-weight: bold;
			width: 100%;
		}
		.blue-btn {
			background-color: blue;
			color: white;
			border: none;
			padding: 10px 20px;
			cursor: pointer;
			font-weight: bold;
		}
		input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, textarea:focus {
			background-color: grey;
			color: white;
		}
		.error-box {
            background-color: #ffcccc;
            color: #990000;
            border: 1px solid #990000;
            padding: 10px;
            margin-bottom: 15px;
        }
        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 15px;
        }
        .js-error {
            color: red;
            font-size: 12px;
            display: block;
            margin-top: 2px;
        }
	</style>
</head>
<body>

	<table id="header_table">
		<tr>
			<td>
				<img src="ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo" height="30" align="middle">
				<b>BOOKSHOP MANAGEMENT</b>
			</td>
			<td align="right"><b>SETTINGS</b></td>
		</tr>
	</table>

	<table id="main_layout">
		<tr>
			<td id="sidebar">
				<a href="<?php echo htmlspecialchars($dashboard_url); ?>">BACK TO DASHBOARD</a>
				<a href="profile_settings.php" id="active_menu">SETTINGS</a>
				
				<br><br>
				<div style="padding: 0 20px;">
					<a href="login.php" style="padding: 0;"><button type="button" class="logout-btn">LOG OUT</button></a>
				</div>
			</td>
			<td id="content">
				<?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                
                <?php if (!empty($success_msg)): ?>
                    <div class="success-box">
                        <?php echo htmlspecialchars($success_msg); ?>
                    </div>
                <?php endif; ?>
				<form id="profileForm" action="profile_settings.php" method="POST" onsubmit="return validateForm();">
					<fieldset>
						<legend>Personal Information</legend>
						<label>Name :</label><br>
						<input type="text" name="FName" id="FName" value="<?php echo htmlspecialchars($_POST['FName'] ?? ''); ?>"><br><br>
						<span id="err_FName" class="js-error"></span><br>

						<label>Email :</label><br>
						<input type="email" name="EmailAdd" id="EmailAdd" value="<?php echo htmlspecialchars($_POST['EmailAdd'] ?? ''); ?>"><br><br>
						<span id="err_EmailAdd" class="js-error"></span><br>

						<label>Contact :</label><br>
						<input type="text" name="PHnumber" id="PHnumber" value="<?php echo htmlspecialchars($_POST['PHnumber'] ?? ''); ?>"><br><br>
						<span id="err_PHnumber" class="js-error"></span><br>

						<label>Shipping Address :</label><br>
						<textarea name="address" id="address" rows="3" cols="40"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
						<span id="err_address" class="js-error"></span>
					</fieldset>

					<fieldset>
						<legend>Security Settings</legend>
						<label>Current Password :</label><br>
						<input type="password" name="CurPass" id="CurPass"><br><br>
						<span id="err_CurPass" class="js-error"></span><br>

						<label>New Password :</label><br>
						<input type="password" name="Pass" id="Pass"><br><br>
						<span id="err_Pass" class="js-error"></span><br>

						<label>Confirm Password :</label><br>
						<input type="password" name="CPass" id="CPass"><br><br>
						<span id="err_CPass" class="js-error"></span>
					</fieldset>

					<button type="submit" class="blue-btn">UPDATE PROFILE</button>
				</form>
			</td>
		</tr>
	</table>
<script>
    function validateForm() {
        let isValid = true;

       
        document.querySelectorAll('.js-error').forEach(el => el.innerText = '');

        const fName = document.getElementById('FName').value.trim();
        const email = document.getElementById('EmailAdd').value.trim();
        const phone = document.getElementById('PHnumber').value.trim();
        const address = document.getElementById('address').value.trim();
        const curPass = document.getElementById('CurPass').value;
        const pass = document.getElementById('Pass').value;
        const cPass = document.getElementById('CPass').value;

        if (fName === "") {
            document.getElementById('err_FName').innerText = "Name is required.";
            isValid = false;
        } else if (!/^[a-zA-Z\s]+$/.test(fName)) {
            document.getElementById('err_FName').innerText = "Name can only contain letters and spaces.";
            isValid = false;
        }

       
        
        if (email === "") {
            document.getElementById('err_EmailAdd').innerText = "Email is required.";
            isValid = false;
        } 

        
        if (phone === "") {
            document.getElementById('err_PHnumber').innerText = "Contact number is required.";
            isValid = false;
        } 

       
        if (address === "") {
            document.getElementById('err_address').innerText = "Shipping address is required.";
            isValid = false;
        }

        
        if (curPass !== "" || pass !== "" || cPass !== "") {
            if (curPass === "") {
                document.getElementById('err_CurPass').innerText = "Enter current password.";
                isValid = false;
            }
            if (pass.length < 6) {
                document.getElementById('err_Pass').innerText = "New password must be at least 6 characters.";
                isValid = false;
            }
            if (pass !== cPass) {
                document.getElementById('err_CPass').innerText = "Passwords do not match.";
                isValid = false;
            }
        }

        return isValid;
    }
    </script>
</body>
</html>