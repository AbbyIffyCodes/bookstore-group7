<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = $_SESSION['errors'] ?? [];
$field_errors = $_SESSION['field_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];

unset($_SESSION['errors'], $_SESSION['field_errors'], $_SESSION['old_input']);

$fname = $old_input['fname'] ?? '';
$email = $old_input['email'] ?? '';
$phone = $old_input['phone'] ?? '';

$fnameErr = $field_errors['fnameErr'] ?? '';
$emailErr = $field_errors['emailErr'] ?? '';
$phoneErr = $field_errors['phoneErr'] ?? '';
$passErr  = $field_errors['passErr'] ?? '';
$cpassErr = $field_errors['cpassErr'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Sign Up - BookShop</title>
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
		#signup_table {
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
		input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {
			width: 100%;
			height: 40px;
			background-color: #e6e1e1;
			border: none;
			margin-top: 5px;
			margin-bottom: 5px;
			padding: 0 10px;
			box-sizing: border-box;
			font-size: 14px;
		}
		input[type="text"]:focus, input[type="email"]:focus, input[type="tel"]:focus, input[type="password"]:focus {
			background-color: #d8d3d3;
			outline: none;
		}
		.error-text {
            color: red;
            font-size: 12px;
            display: block;
            margin-bottom: 8px;
            min-height: 15px;
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
	</style>
</head>
<body>
    <?php if (!empty($errors)): ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin: 15px; background-color: #ffe6e6;">
            <h3 style="margin-top: 0;">Please fix the following errors:</h3>
            <ul style="margin-bottom: 0;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>	

	<table id="page_wrapper">
		<tr>
			<td align="center" valign="middle">

				<form action="../controllers/signup_controller.php" method="POST" onsubmit="return validate(this);" novalidate>
					<table id="signup_table">
						<tr>
							<td id="form_box">
								<h2>Sign Up</h2>

								<label>Full Name</label><br>
								<input type="text" name="FName" id="FName" value="<?php echo htmlspecialchars($fname); ?>"><br>
								<span id="FNameErrMsg" class="error-text"><?php echo htmlspecialchars($fnameErr); ?></span>

								<label>Email</label><br>
								<input type="email" name="EmailAdd" id="EmailAdd" value="<?php echo htmlspecialchars($email); ?>"><br>
								<span id="EmailAddErrMsg" class="error-text"><?php echo htmlspecialchars($emailErr); ?></span>

								<label>Phone</label><br>
								<input type="tel" name="PHnumber" id="PHnumber" value="<?php echo htmlspecialchars($phone); ?>"><br>
								<span id="PHnumberErrMsg" class="error-text"><?php echo htmlspecialchars($phoneErr); ?></span>

								<label>Password</label><br>
								<input type="password" name="Pass" id="Pass"><br>
								<span id="PassErrMsg" class="error-text"><?php echo htmlspecialchars($passErr); ?></span>

								<label>Confirm Password</label><br>
								<input type="password" name="CPass" id="CPass"><br>
								<span id="CPassErrMsg" class="error-text"><?php echo htmlspecialchars($cpassErr); ?></span>

								<button type="submit">Register</button>

								<div id="login_text">
									Already have an account? <a href="login.php">Login</a>
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
            const fname = p.FName.value.trim();
            const email = p.EmailAdd.value.trim();
            const phone = p.PHnumber.value.trim();
            const password = p.Pass.value.trim();
            const cpassword = p.CPass.value.trim();

            const FNameErrMsg = document.getElementById("FNameErrMsg");
            const EmailAddErrMsg = document.getElementById("EmailAddErrMsg");
            const PHnumberErrMsg = document.getElementById("PHnumberErrMsg");
            const PassErrMsg = document.getElementById("PassErrMsg");
            const CPassErrMsg = document.getElementById("CPassErrMsg");

            FNameErrMsg.innerText = "";
            EmailAddErrMsg.innerText = "";
            PHnumberErrMsg.innerText = "";
            PassErrMsg.innerText = "";
            CPassErrMsg.innerText = "";

            let flag = true;

            if (fname === "") {
                FNameErrMsg.innerText = "Please enter your full name.";
                flag = false;
            }

            if (email === "") {
                EmailAddErrMsg.innerText = "Please enter your email.";
                flag = false;
            }

            if (phone === "") {
                PHnumberErrMsg.innerText = "Please enter your phone number.";
                flag = false;
            }

            if (password === "") {
                PassErrMsg.innerText = "Please enter a password.";
                flag = false;
            }

            if (cpassword === "") {
                CPassErrMsg.innerText = "Please confirm your password.";
                flag = false;
            } else if (password !== cpassword) {
                CPassErrMsg.innerText = "Passwords do not match.";
                flag = false;
            }

            return flag;
        }
    </script>
</body>
</html>