<?php

session_start();

if (!isset($_SESSION['inventory_db'])) {
    $_SESSION['inventory_db'] = [
        ["id" => "TXN1001", "title" => "THE ALCHEMIST", "stock" => 15, "price" => 450],
        ["id" => "TXN1002", "title" => "ATOMIC HABITS", "stock" => 27, "price" => 520],
        ["id" => "TXN1003", "title" => "A SONG OF ICE AND FIRE", "stock" => 35, "price" => 2225],
        ["id" => "TXN1004", "title" => "RICH DAD POOR DAD", "stock" => 9, "price" => 570],
    ];
}

$errors = [];
$success_msg = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === "save_book") {
        $book_id = trim($_POST['book_id'] ?? '');
        $title   = trim($_POST['title'] ?? '');
        $stock   = trim($_POST['stock'] ?? '');
        $price   = trim($_POST['price'] ?? '');

        
        if (empty($title)) {
            $errors[] = "Book title is required.";
        } elseif (strlen($title) < 2) {
            $errors[] = "Book title must be at least 2 characters.";
        }

        
        if ($stock === "") {
            $errors[] = "Stock quantity is required.";
        } elseif ((int)$stock < 0) {
            $errors[] = "Stock must be a non-negative whole number.";
        }

        
        if ($price === "") {
            $errors[] = "Price is required.";
        } elseif (!is_numeric($price) || (float)$price <= 0) {
            $errors[] = "Price must be a positive number.";
        }

      
        if (empty($errors)) {
            if (!empty($book_id)) {
              
                foreach ($_SESSION['inventory_db'] as &$book) {
                    if ($book['id'] === $book_id) {
                        $book['title'] = strtoupper(htmlspecialchars($title));
                        $book['stock'] = (int)$stock;
                        $book['price'] = (float)$price;
                        $success_msg   = "Book " . htmlspecialchars($book_id) . " updated successfully!";
                        break;
                    }
                }
            } else {
                
                $next_id = "TXN" . (1001 + count($_SESSION['inventory_db']));
                $_SESSION['inventory_db'][] = [
                    "id"    => $next_id,
                    "title" => strtoupper(htmlspecialchars($title)),
                    "stock" => (int)$stock,
                    "price" => (float)$price
                ];
                $success_msg = "New book successfully added!";
            }
        }
    } elseif ($action === "delete_book") {
        $book_id = $_POST['book_id'] ?? '';
        foreach ($_SESSION['inventory_db'] as $key => $book) {
            if ($book['id'] === $book_id) {
                unset($_SESSION['inventory_db'][$key]);
                $_SESSION['inventory_db'] = array_values($_SESSION['inventory_db']);
                $success_msg = "Book " . htmlspecialchars($book_id) . " deleted successfully!";
                break;
            }
        }
    }
}

$inventory = $_SESSION['inventory_db'];
?>
<!DOCTYPE html>
<head>
	<title>Manage Books & Inventory - BookShop</title>
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
			background-color: blue;
			color: white;
			border: none;
			padding: 4px 8px;
			margin: 0 2px;
			cursor: pointer;
			font-weight: bold;
			font-size: 11px;
		}
		.delete-btn {
            background-color: #d9534f;
        }
		#inventory_table {
			width: 100%;
			border-collapse: collapse;
		}
		#inventory_table th {
			background-color: #5d83e1;
			color: white;
			padding: 12px;
		}
		#inventory_table td {
			background-color: #5911eb;
			color: white;
			padding: 12px;
			text-align: center;
			font-weight: bold;
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
        .modal-content input {
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
				<a href="admin_inventory.php" id="active_menu">BOOKS & INVENTORY</a>
				<a href="admin_users.php">USERS</a>
				<a href="profile_settings.php">SETTINGS</a>
				
				<br><br>
				<div style="padding: 0 20px;">
					<a href="login.php" style="padding: 0;"><button type="button" class="logout-btn">LOG OUT</button></a>
				</div>
			</td>
			<td id="content">
				<fieldset>
					<legend>Manage Books & Inventory</legend>
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
								<input type="text" id="searchInput" onkeyup="filterTable()" placeholder="SEARCH BOOKS" style="padding: 5px; width: 220px;">
                                <button type="button" class="blue-btn" onclick="filterTable()">SEARCH</button>
							</td>
							<td align="right">
								<button type="button" class="blue-btn" onclick="openAddModal()">ADD NEW BOOK</button>
							</td>
						</tr>
					</table>

					<table id="inventory_table" border="1">
						<thead>
                            <tr>
                                <th>ID</th>
                                <th>DETAILS</th>
                                <th>STOCK</th>
                                <th>PRICE</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
						<tbody id="inventoryTableBody">
                            <?php foreach ($inventory as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['id']); ?></td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars(str_pad($book['stock'], 2, '0', STR_PAD_LEFT)); ?></td>
                                    <td>BDT <?php echo htmlspecialchars(number_format($book['price'], 2)); ?></td>
                                    <td>
                                        <button type="button" class="action-btn" onclick="openEditModal('<?php echo $book['id']; ?>', '<?php echo addslashes($book['title']); ?>', <?php echo $book['stock']; ?>, <?php echo $book['price']; ?>)">EDIT</button>
                                        <form method="POST" action="admin_inventory.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            <input type="hidden" name="action" value="delete_book">
                                            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['id']); ?>">
                                            <button type="submit" class="action-btn delete-btn">DELETE</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>
<div id="bookModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle" style="margin-top:0;">Add New Book</h3>
            <form id="bookForm" action="admin_inventory.php" method="POST" onsubmit="return validateBookForm();" novalidate>
                <input type="hidden" name="action" value="save_book">
                <input type="hidden" name="book_id" id="bookId" value="">

                <label for="bookTitle">Book Title</label>
                <input type="text" name="title" id="bookTitle">
                <span id="err_bookTitle" class="js-error"></span>

                <label for="bookStock">Stock Quantity</label>
                <input type="number" name="stock" id="bookStock" min="0">
                <span id="err_bookStock" class="js-error"></span>

                <label for="bookPrice">Price (BDT)</label>
                <input type="number" name="price" id="bookPrice" step="0.01" min="0">
                <span id="err_bookPrice" class="js-error"></span>

                <br><br>
                <button type="submit" class="blue-btn" style="width: 100%;">Save Book</button>
                <button type="button" class="logout-btn" style="width: 100%; margin-top: 5px;" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
   
    function filterTable() {
        const input = document.getElementById('searchInput').value.toUpperCase();
        const tbody = document.getElementById('inventoryTableBody');
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

    
    function openAddModal() {
        document.getElementById('modalTitle').innerText = "Add New Book";
        document.getElementById('bookId').value = "";
        document.getElementById('bookTitle').value = "";
        document.getElementById('bookStock').value = "";
        document.getElementById('bookPrice').value = "";
        clearJsErrors();
        document.getElementById('bookModal').style.display = 'block';
    }

    function openEditModal(id, title, stock, price) {
        document.getElementById('modalTitle').innerText = "Edit Book (" + id + ")";
        document.getElementById('bookId').value = id;
        document.getElementById('bookTitle').value = title;
        document.getElementById('bookStock').value = stock;
        document.getElementById('bookPrice').value = price;
        clearJsErrors();
        document.getElementById('bookModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('bookModal').style.display = 'none';
        clearJsErrors();
    }

    function clearJsErrors() {
        document.querySelectorAll('.js-error').forEach(el => el.innerText = '');
    }


    function validateBookForm() {
        clearJsErrors();
        let isValid = true;

        const title = document.getElementById('bookTitle').value.trim();
        const stock = document.getElementById('bookStock').value.trim();
        const price = document.getElementById('bookPrice').value.trim();

        
        if (title === "") {
            document.getElementById('err_bookTitle').innerText = "Book title is required.";
            isValid = false;
        } else if (title.length < 2) {
            document.getElementById('err_bookTitle').innerText = "Title must be at least 2 characters.";
            isValid = false;
        }

        
        if (stock === "") {
            document.getElementById('err_bookStock').innerText = "Stock quantity is required.";
            isValid = false;
        } else if (isNaN(stock) || parseInt(stock) < 0 || !Number.isInteger(Number(stock))) {
            document.getElementById('err_bookStock').innerText = "Stock must be a non-negative integer.";
            isValid = false;
        }

        
        if (price === "") {
            document.getElementById('err_bookPrice').innerText = "Price is required.";
            isValid = false;
        } else if (isNaN(price) || parseFloat(price) <= 0) {
            document.getElementById('err_bookPrice').innerText = "Price must be a positive number.";
            isValid = false;
        }

        return isValid;
    }

    
    <?php if (!empty($errors)): ?>
        document.getElementById('bookModal').style.display = 'block';
    <?php endif; ?>
    </script>
</body>
</html>