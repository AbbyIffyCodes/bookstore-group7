<!DOCTYPE html>
<html>
<head>
    <title>Employee Stock Checker - BookShop</title>
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
        .stock-out {
            background-color: #d9534f !important;
        }
    </style>
</head>
<body>

    <table id="header_table">
        <tr>
            <td>
                <img src="../public/images/ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo" height="30" align="middle">
                <b>BOOKSHOP</b>
            </td>
            <td align="right"><b>EMPLOYEE PANEL</b></td>
        </tr>
    </table>

    <table id="main_layout">
        <tr>
            <td id="sidebar">
                <a href="../controllers/employee_dashboard_controller.php">DASHBOARD</a>
                <a href="../controllers/employee_billing_controller.php">BILLING</a>
                <a href="../controllers/employee_transaction_controller.php" id="active_menu">STOCK</a>
                <a href="../controllers/profile_settings_controller.php">SETTINGS</a>
                <br><br>
                <div style="padding: 0 20px;">
                    <button type="button" class="logout-btn" onclick="confirmLogout()">LOG OUT</button>
                </div>
            </td>
            <td id="content">
                <fieldset>
                    <legend>STOCK CHECKER</legend>
                    
                    <div style="margin-bottom: 15px;">
                        <input type="text" id="searchInput" placeholder="SEARCH BOOK OR CATEGORY..." style="padding: 6px; width: 250px;">
                        <button type="button" class="blue-btn" onclick="fetchStockData()">SEARCH</button>
                    </div>

                    <table id="inventory_table" border="1">
                        <thead>
                            <tr>
                                <th>BOOK ID</th>
                                <th>TITLE</th>
                                <th>CATEGORY</th>
                                <th>PRICE</th>
                                <th>STOCK QTY</th>
                            </tr>
                        </thead>
                        <tbody id="stockTableBody">
                            <?php if (!empty($stock_list)): ?>
                                <?php foreach ($stock_list as $item): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($item['CustomBookID'] ?? $item['BookID']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Category']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Price']); ?> TK</td>
                                    <td class="<?php echo ($item['Stock'] <= 0) ? 'stock-out' : ''; ?>">
                                        <?php echo htmlspecialchars($item['Stock']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No stock records found in database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "../controllers/employee_transaction_controller.php?action=logout";
            }
        }

        function fetchStockData() {
            const searchQuery = document.getElementById('searchInput').value;
            const xhr = new XMLHttpRequest();

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    const tbody = document.getElementById('stockTableBody');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5">No matching stock records found.</td></tr>';
                        return;
                    }

                    data.forEach(item => {
                        const customId = item.CustomBookID ? item.CustomBookID : item.BookID;
                        const stockClass = parseInt(item.Stock) <= 0 ? 'stock-out' : '';

                        tbody.innerHTML += `
                            <tr>
                                <td>#${escapeHtml(customId)}</td>
                                <td>${escapeHtml(item.Title)}</td>
                                <td>${escapeHtml(item.Category)}</td>
                                <td>${escapeHtml(item.Price)} TK</td>
                                <td class="${stockClass}">${escapeHtml(item.Stock)}</td>
                            </tr>
                        `;
                    });
                }
            };

            xhr.open("GET", "../controllers/employee_transaction_controller.php?action=search_stock&query=" + encodeURIComponent(searchQuery));
            xhr.send();
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        document.getElementById('searchInput')?.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                fetchStockData();
            }
        });
    </script>
</body>
</html>