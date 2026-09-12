<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catalog Browsing - BookShop</title>
    <style>
        html, body {
            width: 100%;
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
        }

        #page_wrapper {
            width: 100%;
            height: 100vh;
            border-collapse: collapse;
        }

        #top_bar {
            height: 70px;
            background-color: #4c63ee;
            color: #ffffff;
            padding: 0 30px;
        }

        .logo-section img {
            height: 40px;
            vertical-align: middle;
            margin-right: 15px;
            background: #fff;
            padding: 2px;
            border-radius: 4px;
        }

        .logo-section span {
            font-size: 24px;
            font-weight: bold;
            vertical-align: middle;
        }

        .search-box input {
            width: 400px;
            height: 38px;
            border: none;
            padding: 0 15px;
            font-size: 14px;
            border-radius: 2px;
            box-sizing: border-box;
        }

        .header-links {
            text-align: right;
            font-size: 16px;
        }

        .header-links a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
        }

        #sidebar {
            width: 220px;
            background-color: #f3ecee;
            vertical-align: top;
            padding: 30px;
        }

        #sidebar h2 {
            font-size: 24px;
            font-weight: normal;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .cat-link {
            display: block;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 18px;
            cursor: pointer;
        }

        .cat-link:hover, .cat-link.active {
            color: #4c63ee;
            font-weight: bold;
        }

        #content_area {
            vertical-align: top;
            padding: 30px;
        }

        .section-header {
            width: 100%;
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 22px;
            font-weight: normal;
            margin: 0;
            display: inline-block;
        }

        .section-header a {
            float: right;
            color: #4c63ee;
            text-decoration: none;
            font-size: 15px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }

        .book-card {
            background-color: #f3ecee;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .book-card img {
            width: 130px;
            height: 180px;
            object-fit: cover;
            margin-bottom: 15px;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.2);
            border-radius: 2px;
        }

        .book-title {
            font-size: 15px;
            margin: 5px 0 2px 0;
            text-align: left;
            font-weight: bold;
        }

        .book-price {
            font-size: 14px;
            margin: 5px 0 15px 0;
            text-align: left;
        }

        .add-btn {
            width: 100%;
            height: 38px;
            background-color: #4c63ee;
            color: white;
            border: none;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
        }

        .add-btn:hover {
            background-color: #3b50cb;
        }

        .features-row {
            width: 100%;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .feature-box {
            display: inline-block;
            width: 30%;
        }

        .feature-box h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }

        .feature-box p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .nav-dashboard-link {
            margin-bottom: 15px;
            display: inline-block;
            color: #4c63ee;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .msg-box {
            color: green;
            background: #e6ffe6;
            border: 1px solid green;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <table id="page_wrapper">
        <tr id="top_bar">
            <td class="logo-section" width="25%">
                <img src="../public/images/ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo">
                <span>BookShop</span>
            </td>
            <td class="search-box" width="50%" align="center">
                <input type="text" id="searchInput" placeholder="Search books, category...">
            </td>
            <td class="header-links" width="25%">
                <a href="../controllers/customer_cart_controller.php">Cart(<span id="cart_counter_val"><?php echo $total_cart_items; ?></span>)</a>
                <a href="../controllers/customer_dashboard_controller.php">Customer</a>
            </td>
        </tr>

        <tr>
            <td id="sidebar">
                <h2>Categories</h2>
                <span class="cat-link active" onclick="selectCategory('All', this)">All Books</span>
                <span class="cat-link" onclick="selectCategory('Fiction', this)">Fiction</span>
                <span class="cat-link" onclick="selectCategory('Programming', this)">Programming</span>
                <span class="cat-link" onclick="selectCategory('Science', this)">Science</span>
                <span class="cat-link" onclick="selectCategory('Business', this)">Business</span>
                <span class="cat-link" onclick="selectCategory('History', this)">History</span>
                <span class="cat-link" onclick="selectCategory('Self-Help', this)">Self Help</span>
            </td>

            <td id="content_area">
                <a href="../controllers/customer_dashboard_controller.php" class="nav-dashboard-link">&larr; Back to Dashboard</a>

                <?php if (!empty($message)): ?>
                    <div class="msg-box"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="section-header">
                    <h2>Featured Books</h2>
                    <a href="javascript:void(0)" onclick="selectCategory('All', null)">View All</a>
                </div>

                <div class="grid-container" id="booksGrid">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card" data-category="<?php echo htmlspecialchars($book['category'] ?? ''); ?>" data-title="<?php echo htmlspecialchars(strtolower($book['title'])); ?>">
                            <img src="<?php echo htmlspecialchars($book['img']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                            <div class="book-price"><?php echo htmlspecialchars($book['price']); ?> TK</div>

                            <form method="POST" action="../controllers/customer_shop_controller.php" onsubmit="return validateAddToCart(this);">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="add-btn">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <table class="features-row">
                    <tr>
                        <td class="feature-box">
                            <h4>Secure Payment</h4>
                            <p>100% secure payment</p>
                        </td>
                        <td class="feature-box">
                            <h4>Best Quality</h4>
                            <p>Genuine Books</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <script>
        let currentSelectedCategory = 'All';

        function fetchBooks() {
            const searchQuery = document.getElementById('searchInput')?.value || '';
            const xhr = new XMLHttpRequest();

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    const grid = document.getElementById('booksGrid');
                    grid.innerHTML = ''; 

                    if (data.length === 0) {
                        grid.innerHTML = '<p>No books found.</p>';
                        return;
                    }

                    data.forEach(book => {
                        grid.innerHTML += `
                            <div class="book-card" data-category="${escapeHtml(book.category)}" data-title="${escapeHtml(book.title.toLowerCase())}">
                                <img src="${escapeHtml(book.img)}" alt="${escapeHtml(book.title)}">
                                <div class="book-title">${escapeHtml(book.title)}</div>
                                <div class="book-price">${escapeHtml(book.price)} TK</div>
                                <form method="POST" action="../controllers/customer_shop_controller.php" onsubmit="return validateAddToCart(this);">
                                    <input type="hidden" name="add_to_cart" value="1">
                                    <input type="hidden" name="book_id" value="${book.id}">
                                    <button type="submit" class="add-btn">Add to Cart</button>
                                </form>
                            </div>
                        `;
                    });
                }
            };

            const url = "../controllers/customer_shop_controller.php?action=search_books&category=" + encodeURIComponent(currentSelectedCategory) + "&query=" + encodeURIComponent(searchQuery);
            xhr.open("GET", url);
            xhr.send();
        }

        function selectCategory(category, element) {
            currentSelectedCategory = category;

            if (element) {
                document.querySelectorAll('.cat-link').forEach(link => link.classList.remove('active'));
                element.classList.add('active');
            }

            fetchBooks();
        }

        function validateAddToCart(form) {
            const bookId = form.book_id.value;
            if (!bookId || bookId <= 0) {
                alert("Invalid item payload.");
                return false;
            }
            return true;
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

        document.getElementById('searchInput')?.addEventListener('keyup', fetchBooks);
    </script>
</body>
</html>