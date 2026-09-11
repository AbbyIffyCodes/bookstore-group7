<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || strtoupper($_SESSION['user_role']) !== 'ADMIN') {
    header("Location: login.php");
    exit();
}

$inventory = $_SESSION['inventory'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$success_msg = $_SESSION['success_msg'] ?? "";
unset($_SESSION['errors'], $_SESSION['success_msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books & Inventory - BookShop</title>
    <style>
        html, body { height: 100%; margin: 0; padding: 0; background-color: lightblue;}
        #header_table { width: 100%; height: 50px; background-color: blue; color: white; padding: 0 20px; }
        #main_layout { width: 100%; height: calc(100vh - 50px); border-collapse: collapse; }
        #sidebar { width: 220px; vertical-align: top; background-color: #4c63ee; padding-top: 10px; }
        #sidebar a { display: block; color: white; text-decoration: none; font-weight: bold; padding: 12px 20px; }
        #active_menu { background-color: grey; }
        #content { vertical-align: top; padding: 20px; }
        fieldset { border-color: blue; background-color: white; margin-bottom: 20px; padding: 15px; }
        .logout-btn { background-color: grey; color: white; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; width: 100%; }
        .blue-btn { background-color: blue; color: white; border: none; padding: 6px 12px; cursor: pointer; font-weight: bold; }
        .action-btn { background-color: blue; color: white; border: none; padding: 4px 8px; margin: 0 2px; cursor: pointer; font-weight: bold; font-size: 11px; }
        .delete-btn { background-color: #d9534f; }
        #inventory_table { width: 100%; border-collapse: collapse; }
        #inventory_table th { background-color: #5d83e1; color: white; padding: 12px; }
        #inventory_table td { background-color: #5911eb; color: white; padding: 12px; text-align: center; font-weight: bold; }
        .alert-error { background-color: #ffcccc; color: #990000; border: 1px solid #990000; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .js-error { color: red; font-size: 12px; display: block; margin-top: 2px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 8% auto; padding: 20px; width: 340px; border-radius: 5px; }
        .modal-content label { font-size: 13px; color: #333; display: block; margin-top: 10px; }
        .modal-content input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; }
    </style>
</head>
<body>

    <table id="header_table">
        <tr>
            <td>
                <img src="../public/images/ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo" height="30" style="vertical-align: middle;">
                <b>BOOKSHOP MANAGEMENT</b>
            </td>
            <td align="right"><b>ADMIN PANEL</b></td>
        </tr>
    </table>

    <table id="main_layout">
        <tr>
            <td id="sidebar">
                <a href="../controllers/admin_dashboard_controller.php">DASHBOARD</a>
                <a href="../controllers/admin_inventory_controller.php" id="active_menu">BOOKS & INVENTORY</a>
                <a href="../controllers/admin_users_controller.php">USERS</a>
                <a href="../controllers/profile_settings_controller.php">SETTINGS</a>
                <br><br>
                <div style="padding: 0 20px;">
                    <a href="../controllers/admin_dashboard_controller.php?action=logout" style="padding: 0;"><button type="button" class="logout-btn">LOG OUT</button></a>
                </div>
            </td>
            <td id="content">
                <fieldset>
                    <legend>Manage Books & Inventory</legend>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert-error">
                            <ul style="margin: 0; padding-left: 20px;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success_msg)): ?>
                        <div class="alert-success">
                            <b><?php echo htmlspecialchars($success_msg, ENT_QUOTES, 'UTF-8'); ?></b>
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
                                <th>CATEGORY</th>
                                <th>STOCK</th>
                                <th>PRICE</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            <?php if (!empty($inventory)): ?>
                                <?php foreach ($inventory as $book): ?>
                                    <tr>
                                       <td><?php echo htmlspecialchars(!empty($book['CustomBookID']) ? $book['CustomBookID'] : ('#BK' . $book['BookID']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($book['Title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($book['Category'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(str_pad($book['Stock'], 2, '0', STR_PAD_LEFT), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>BDT <?php echo htmlspecialchars(number_format($book['Price'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <button type="button" 
                                                    class="action-btn" 
                                                    data-id="<?php echo htmlspecialchars($book['BookID'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-customid="<?php echo htmlspecialchars($book['CustomBookID'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-title="<?php echo htmlspecialchars($book['Title'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-category="<?php echo htmlspecialchars($book['Category'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-stock="<?php echo htmlspecialchars($book['Stock'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-price="<?php echo htmlspecialchars($book['Price'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-image="<?php echo htmlspecialchars($book['Image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    onclick="handleEditClick(this)">EDIT</button>
                                            
                                            <form method="POST" action="../controllers/admin_inventory_controller.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                                <input type="hidden" name="action" value="delete_book">
                                                <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['BookID'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="action-btn delete-btn">DELETE</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No books found in inventory.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>

    <div id="bookModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle" style="margin-top:0;">Add New Book</h3>
            <form id="bookForm" action="../controllers/admin_inventory_controller.php" method="POST" enctype="multipart/form-data" onsubmit="return validateBookForm();" novalidate>
                <input type="hidden" name="action" value="save_book">
                <input type="hidden" name="book_id" id="bookId" value="">

                <label for="bookTitle">Book Title</label>
                <input type="text" name="title" id="bookTitle">
                <span id="err_bookTitle" class="js-error"></span>

                <label for="bookCategory">Category</label>
                <select name="category" id="bookCategory">
                <option value="">-- Select Category --</option>
                <option value="Fiction">Fiction</option>
                <option value="Self-Help">Self-Help</option>
                <option value="Business">Business</option>
                <option value="Programming">Programming</option>
                <option value="Science">Science</option>
                <option value="History">History</option>
                </select>
                <span id="err_bookCategory" class="js-error"></span>

                <label for="bookStock">Stock Quantity</label>
                <input type="number" name="stock" id="bookStock" min="0">
                <span id="err_bookStock" class="js-error"></span>

                <label for="bookPrice">Price (BDT)</label>
                <input type="number" name="price" id="bookPrice" step="0.01" min="0">
                <span id="err_bookPrice" class="js-error"></span>

                <label for="bookImage">Book Cover Image</label>
                <input type="file" name="book_image" id="bookImage" accept="image/jpeg,image/png,image/webp">
                <span id="err_bookImage" class="js-error"></span>

                <div id="imagePreviewContainer" style="margin-top: 10px; display: none;">
                    <small>Current Image:</small><br>
                    <img id="currentBookImg" src="" alt="Cover" style="max-width: 80px; max-height: 80px; border: 1px solid #ccc; margin-top: 4px;">
                </div>

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
            rows[i].style.display = rowText.toUpperCase().indexOf(input) > -1 ? "" : "none";
        }
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerText = "Add New Book";
        document.getElementById('bookId').value = "";
        document.getElementById('bookTitle').value = "";
        document.getElementById('bookCategory').value = "";
        document.getElementById('bookStock').value = "";
        document.getElementById('bookPrice').value = "";
        document.getElementById('imagePreviewContainer').style.display = 'none';
        clearJsErrors();
        document.getElementById('bookModal').style.display = 'block';
    }

    function handleEditClick(btn) {
        const id = btn.getAttribute('data-id');
        const customId = btn.getAttribute('data-customid');
        const title = btn.getAttribute('data-title');
        const category = btn.getAttribute('data-category');
        const stock = btn.getAttribute('data-stock');
        const price = btn.getAttribute('data-price');
        const imagePath = btn.getAttribute('data-image');

        document.getElementById('modalTitle').innerText = "Edit Book (#" + id + ")";
        document.getElementById('bookId').value = id;
        document.getElementById('bookTitle').value = title;
        document.getElementById('bookCategory').value = category;
        document.getElementById('bookStock').value = stock;
        document.getElementById('bookPrice').value = price;

        const previewContainer = document.getElementById('imagePreviewContainer');
        const previewImg = document.getElementById('currentBookImg');

        if (imagePath && imagePath.trim() !== '') {
            previewImg.src = imagePath;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }

        clearJsErrors();
        document.getElementById('bookModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('bookModal').style.display = 'none';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        clearJsErrors();
    }

    function clearJsErrors() {
        document.querySelectorAll('.js-error').forEach(el => el.innerText = '');
    }

    function validateBookForm() {
        clearJsErrors();
        let isValid = true;

        const title = document.getElementById('bookTitle').value.trim();
        const category = document.getElementById('bookCategory').value;
        const stock = document.getElementById('bookStock').value.trim();
        const price = document.getElementById('bookPrice').value.trim();

        if (title === "") {
            document.getElementById('err_bookTitle').innerText = "Book title is required.";
            isValid = false;
        } else if (title.length < 2) {
            document.getElementById('err_bookTitle').innerText = "Title must be at least 2 characters.";
            isValid = false;
        }

        if (category === "") {
            document.getElementById('err_bookCategory').innerText = "Please select a category.";
            isValid = false;
        }

        if (stock === "") {
            document.getElementById('err_bookStock').innerText = "Stock quantity is required.";
            isValid = false;
        } else if (isNaN(stock) || parseInt(stock, 10) < 0) {
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