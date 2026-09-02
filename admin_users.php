<?php
session_start();
require_once 'dbconnection.php';


if (!isset($_SESSION['user_role']) || strtoupper($_SESSION['user_role']) !== 'ADMIN') {
    
    header("Location: login.php");
    exit();
}

$errors = [];
$success_msg = "";

if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === "add_user") {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? '');

        if (empty($name)) {
            $errors[] = "User name is required.";
        } elseif (strlen($name) < 2) {
            $errors[] = "Name must be at least 2 characters.";
        }

        if (empty($email)) {
            $errors[] = "Email address is required.";
        } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $errors[] = "Invalid email format.";
        }
        else {
           
            $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows > 0) {
                $errors[] = "Email is already registered.";
            }
            $checkStmt->close();
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
            
            $countRes = $conn->query("SELECT COUNT(*) AS total FROM users");
            $totalCount = $countRes->fetch_assoc()['total'] + 1;
            $custom_id = "T" . str_pad($totalCount, 3, "0", STR_PAD_LEFT);

            
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $status = "Active";

            $stmt = $conn->prepare("INSERT INTO users (CustomUserID, FullName, Email, PasswordHash, Role, Status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $custom_id, $name, $email, $password_hash, $role, $status);

if ($stmt->execute()) {
                $_SESSION['success_msg'] = "User successfully added!";
                header("Location: admin_users.php");
                exit();
            } else {
                $errors[] = "Database error: " . $conn->error;
            }
            $stmt->close();
        }
    } elseif ($action === "toggle_status") {
        header('Content-Type: application/json');

        $userId = $_POST['user_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if ($userId && in_array(strtolower($status), ['active', 'inactive'])) {
            
            $formattedStatus = ucfirst(strtolower($status));

            $stmt = $conn->prepare("UPDATE users SET Status = ? WHERE CustomUserID = ?");
            $stmt->bind_param("ss", $formattedStatus, $userId);
            $success = $stmt->execute();
            $stmt->close();

            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        }
        exit;
    }
}


$users = [];
$result = $conn->query("SELECT CustomUserID AS id, FullName AS name, Role AS role, Email AS email, Status AS status FROM users ORDER BY UserID DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>

<html>

<head>
	<title>Manage Users & Roles - BookShop</title>
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
			padding: 6px 12px;
			cursor: pointer;
			font-weight: bold;
		}
		.action-btn {
            background-color: #333;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 11px;
            border-radius: 3px;
        }
		#data_table {
			width: 100%;
			border-collapse: collapse;
		}
		#data_table th {
			background-color: #5d83e1;
			color: white;
			padding: 12px;
		}
		#data_table td {
			background-color: #5911eb;
			color: white;
			padding: 12px;
			text-align: center;
			font-weight: bold;
		}
		.status-inactive {
            background-color: #8c2020 !important;
        }
        .alert-error {
            background-color: #ffcccc;
            color: #990000;
            border: 1px solid #990000;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .js-error {
            color: red;
            font-size: 12px;
            display: block;
            margin-top: 2px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 320px;
            border-radius: 5px;
        }
        .modal-content label {
            font-size: 13px;
            color: #333;
            display: block;
            margin-top: 10px;
        }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            box-sizing: border-box;
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
			<td align="right"><b>ADMIN PANEL</b></td>
		</tr>
	</table>

	<table id="main_layout">
		<tr>
			<td id="sidebar">
				<a href="admin_dashboard.php">DASHBOARD</a>
				<a href="admin_inventory.php">BOOKS & INVENTORY</a>
				<a href="admin_users.php" id="active_menu">USERS</a>
				<a href="profile_settings.php">SETTINGS</a>
				
				<br><br>
				<div style="padding: 0 20px;">
					<a href="login.php" style="padding: 0;"><button type="button" class="logout-btn">LOG OUT</button></a>
				</div>
			</td>
			<td id="content">
				<fieldset>
					<legend>User & Role Management</legend>

                    <?php if (!empty($errors)): ?>
                        <div class="alert-error">
                            <ul style="margin: 0; padding-left: 20px;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert-success">
                            <b><?php echo htmlspecialchars($success_msg); ?></b>
                        </div>
                    <?php endif; ?>
					
					<table width="100%" style="margin-bottom: 15px;">
						<tr>
							<td>
								<input type="text" id="searchInput" onkeyup="filterTable()" placeholder="SEARCH USERS" style="padding: 5px; width: 220px;">
								<button type="button" class="blue-btn" onclick="filterTable()">SEARCH</button>
							</td>
							<td align="right">
								<button type="button" class="blue-btn" onclick="openModal()">ADD USER</button>
							</td>
						</tr>
					</table>

					<table id="data_table" border="1">
					<thead>
						<tr>
							<th>ID</th>
							<th>NAME</th>
							<th>ROLE</th>
							<th>EMAIL</th>
							<th>STATUS</th>
							<th>ACTION</th>
						</tr>
					</thead>
					<tbody id="userTableBody">
						<?php foreach ($users as $user): ?>
						<tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td id="status-badge-<?php echo htmlspecialchars($user['id']); ?>" class="<?php echo (strtoupper($user['status']) === 'INACTIVE') ? 'status-inactive' : ''; ?>">
                                   <?php echo htmlspecialchars(strtoupper($user['status'])); ?>
                                    </td>
                                    <td>
                                <button type="button" class="action-btn" onclick="toggleUserStatus('<?php echo htmlspecialchars($user['id']); ?>', '<?php echo strtolower($user['status']); ?>', this)">
                                <?php echo (strtoupper($user['status']) === 'ACTIVE') ? 'Deactivate' : 'Activate'; ?>
                              </button>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>

    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-top:0;">Add New User</h3>
            <form id="addUserForm" action="admin_users.php" method="POST" onsubmit="return validateAddUserForm();" novalidate>
                <input type="hidden" name="action" value="add_user">

                <label>Name</label>
                <input type="text" name="name" id="userName">
                <span id="err_userName" class="js-error"></span>

                <label>Email</label>
                <input type="email" name="email" id="userEmail">
                <span id="err_userEmail" class="js-error"></span>

                <label>Password</label>
                <input type="password" name="password" id="userPassword">
                <span id="err_userPassword" class="js-error"></span>

                <label>Role</label>
                <select name="role" id="userRole">
                    <option value="">-- Select Role --</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="EMPLOYEE">EMPLOYEE</option>
                    <option value="CUSTOMER">CUSTOMER</option>
                </select>
                <span id="err_userRole" class="js-error"></span>

                <br><br>
                <button type="submit" class="blue-btn" style="width: 100%;">Submit</button>
                <button type="button" class="logout-btn" style="width: 100%; margin-top: 5px;" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    
    function filterTable() {
        const input = document.getElementById('searchInput').value.toUpperCase();
        const tbody = document.getElementById('userTableBody');
        const rows = tbody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const rowText = rows[i].textContent || rows[i].innerText;
            if (rowText.toUpperCase().indexOf(input) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }

    
    function openModal() {
        document.getElementById('addUserModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('addUserModal').style.display = 'none';
        clearJsErrors();
    }

    function clearJsErrors() {
        document.querySelectorAll('.js-error').forEach(el => el.innerText = '');
    }

   
    function validateAddUserForm() {
        clearJsErrors();
        let isValid = true;

        const name = document.getElementById('userName').value.trim();
        const email = document.getElementById('userEmail').value.trim();
        const role = document.getElementById('userRole').value;
        const password = document.getElementById('userPassword').value;

        
        if (name === "") {
            document.getElementById('err_userName').innerText = "Name is required.";
            isValid = false;
        } else if (name.length < 2) {
            document.getElementById('err_userName').innerText = "Name must be at least 2 characters.";
            isValid = false;
        }

        
        
        if (email === "") {
            document.getElementById('err_userEmail').innerText = "Email is required.";
            isValid = false;
        } 

        

         if (password === "") {
         document.getElementById('err_userPassword').innerText = "Password is required.";
          isValid = false;
        } else if (password.length < 6) {
         document.getElementById('err_userPassword').innerText = "Password must be at least 6 characters.";
          isValid = false;
        }

        
        if (role === "") {
            document.getElementById('err_userRole').innerText = "Role selection is required.";
            isValid = false;
        }

        return isValid;
    }

    function toggleUserStatus(userId, currentStatus, buttonElement) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('user_id', userId);
    formData.append('status', newStatus);

    fetch('admin_users.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            
            const statusBadge = document.getElementById(`status-badge-${userId}`);
            statusBadge.textContent = newStatus.toUpperCase();
            
            if (newStatus === 'inactive') {
                statusBadge.classList.add('status-inactive');
            } else {
                statusBadge.classList.remove('status-inactive');
            }

            
            buttonElement.textContent = newStatus === 'active' ? 'Deactivate' : 'Activate';
            buttonElement.setAttribute('onclick', `toggleUserStatus('${userId}', '${newStatus}', this)`);
        } else {
            alert('Failed to update user status.');
        }
    })
    .catch(error => console.error('Error toggling status:', error));
}
    </script>
</body>
</html>