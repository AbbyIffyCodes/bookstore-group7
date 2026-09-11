<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = $_SESSION['errors'] ?? [];
$success_msg = $_SESSION['success_msg'] ?? "";
$old_email = $_SESSION['old_email'] ?? "";

unset($_SESSION['errors'], $_SESSION['success_msg'], $_SESSION['old_email']);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Forgot Password - BookShop</title>
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
		#forgot_table {
			border-collapse: collapse;
		}
		#form_box {
			width: 360px;
			vertical-align: top;
			padding: 20px;
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
			margin-bottom: 12px;
			padding: 0 10px;
			box-sizing: border-box;
			font-size: 14px;
		}
		input[type="email"]:focus, input[type="password"]:focus {
			background-color: #d8d3d3;
			outline: none;
		}
		button {
			width: 100%;
			height: 45px;
			background-color: #4c63ee;
			color: white;
			border: none;
			font-size: 16px;
			cursor: pointer;
			margin-top: 10px;
			margin-bottom: 20px;
			border-radius: 4px;
		}
		button:hover {
			background-color: #3b50cb;
		}
		#login_text {
			text-align: center;
			font-size: 13px;
			color: #333333;
		}
		#login_text a {
			color: #4c63ee;
			text-decoration: none;
			font-weight: bold;
		}
		.error-box {
            background-color: #ffcccc;
            color: #990000;
            border: 1px solid #990000;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .error-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .js-error {
            color: red;
            font-size: 12px;
            display: block;
            margin-bottom: 10px;
        }
	</style>
</head>
<body>

	<table id="page_wrapper">
		<tr>
			<td align="center" valign="middle">

				<table id="forgot_table">
					<tr>
						<td id="form_box">
							<h2>Reset Password</h2>
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

							<form id="resetForm" action="../controllers/forgot_password_controller.php" method="POST" onsubmit="return validateForm();" novalidate>
								<label>Email</label><br>
								<input type="email" name="EmailAdd" id="EmailAdd" value="<?php echo htmlspecialchars($old_email); ?>"><br>
								<span id="err_EmailAdd" class="js-error"></span>

								<label>New Password</label><br>
								<input type="password" name="Pass" id="Pass"><br>
								<span id="err_Pass" class="js-error"></span>

								<label>Confirm New Password</label><br>
								<input type="password" name="CPass" id="CPass"><br>
								<span id="err_CPass" class="js-error"></span>

								<button type="submit">Submit</button>

								<div id="login_text">
									Remembered your password? <a href="login.php">Login</a>
								</div>
							</form>
						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>

<script>
    function validateForm() {
        let isValid = true;

        document.querySelectorAll('.js-error').forEach(el => el.innerText = '');

        const email = document.getElementById('EmailAdd').value.trim();
        const pass = document.getElementById('Pass').value;
        const cPass = document.getElementById('CPass').value;

        if (email === "") {
            document.getElementById('err_EmailAdd').innerText = "Email address is required.";
            isValid = false;
        } 

        if (pass === "") {
            document.getElementById('err_Pass').innerText = "New password is required.";
            isValid = false;
        } else if (pass.length < 6) {
            document.getElementById('err_Pass').innerText = "Password must be at least 6 characters.";
            isValid = false;
        }

        if (cPass === "") {
            document.getElementById('err_CPass').innerText = "Please confirm your password.";
            isValid = false;
        } else if (pass !== cPass) {
            document.getElementById('err_CPass').innerText = "Passwords do not match.";
            isValid = false;
        }

        return isValid;
    }
</script>
</body>
</html>