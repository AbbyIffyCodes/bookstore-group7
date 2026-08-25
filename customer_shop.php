<?php
session_start();

// Session Access Control Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Initialize session cart array if not already present
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = "";

// Array of 20 sample books categorized for local filtering
$books = [
    // Programming
    ['id' => 1, 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'price' => 600, 'category' => 'Programming', 'img' => 'clean_code.jpg'],
    ['id' => 2, 'title' => 'The Pragmatic Programmer', 'author' => 'Andrew Hunt', 'price' => 750, 'category' => 'Programming', 'img' => 'pragmatic.jpg'],
    ['id' => 3, 'title' => 'Design Patterns', 'author' => 'Erich Gamma', 'price' => 900, 'category' => 'Programming', 'img' => 'patterns.jpg'],
    ['id' => 4, 'title' => 'You Dont Know JS', 'author' => 'Kyle Simpson', 'price' => 450, 'category' => 'Programming', 'img' => 'ydkjs.jpg'],

    // Fiction
    ['id' => 5, 'title' => 'The Alchemist', 'author' => 'Paulo Coelho', 'price' => 500, 'category' => 'Fiction', 'img' => 'alchemist.jpg'],
    ['id' => 6, 'title' => '1984', 'author' => 'George Orwell', 'price' => 400, 'category' => 'Fiction', 'img' => '1984.jpg'],
    ['id' => 7, 'title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'price' => 480, 'category' => 'Fiction', 'img' => 'mockingbird.jpg'],
    ['id' => 8, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'price' => 350, 'category' => 'Fiction', 'img' => 'gatsby.jpg'],

    // Business
    ['id' => 9, 'title' => 'The Business Book', 'author' => 'Sam Atkinson', 'price' => 1000, 'category' => 'Business', 'img' => 'business.jpg'],
    ['id' => 10, 'title' => 'Rich Dad Poor Dad', 'author' => 'Robert Kiyosaki', 'price' => 550, 'category' => 'Business', 'img' => 'richdad.jpg'],
    ['id' => 11, 'title' => 'The Lean Startup', 'author' => 'Eric Ries', 'price' => 650, 'category' => 'Business', 'img' => 'lean.jpg'],
    ['id' => 12, 'title' => 'Zero to One', 'author' => 'Peter Thiel', 'price' => 580, 'category' => 'Business', 'img' => 'zerotoone.jpg'],

    // Science
    ['id' => 13, 'title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'price' => 700, 'category' => 'Science', 'img' => 'sapiens.jpg'],
    ['id' => 14, 'title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'price' => 600, 'category' => 'Science', 'img' => 'time.jpg'],
    ['id' => 15, 'title' => 'Cosmos', 'author' => 'Carl Sagan', 'price' => 650, 'category' => 'Science', 'img' => 'cosmos.jpg'],

    // History
    ['id' => 16, 'title' => 'Guns, Germs, and Steel', 'author' => 'Jared Diamond', 'price' => 800, 'category' => 'History', 'img' => 'guns.jpg'],
    ['id' => 17, 'title' => 'The Silk Roads', 'author' => 'Peter Frankopan', 'price' => 850, 'category' => 'History', 'img' => 'silk.jpg'],

    // Self Help
    ['id' => 18, 'title' => 'Atomic Habits', 'author' => 'James Clear', 'price' => 600, 'category' => 'Self Help', 'img' => 'atomichabits.jpg'],
    ['id' => 19, 'title' => 'Deep Work', 'author' => 'Cal Newport', 'price' => 520, 'category' => 'Self Help', 'img' => 'deepwork.jpg'],
    ['id' => 20, 'title' => 'Psychology of Money', 'author' => 'Morgan Housel', 'price' => 550, 'category' => 'Self Help', 'img' => 'money.jpg']
];

// Handle PHP Add to Cart Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);

    if ($book_id <= 0) {
        $message = "Invalid book selection.";
    } else {
        // Find selected book from catalog array
        $selected_book = null;
        foreach ($books as $b) {
            if ($b['id'] === $book_id) {
                $selected_book = $b;
                break;
            }
        }

        if ($selected_book) {
            if (isset($_SESSION['cart'][$book_id])) {
                $_SESSION['cart'][$book_id]['qty'] += 1;
            } else {
                $_SESSION['cart'][$book_id] = [
                    'id' => $selected_book['id'],
                    'title' => $selected_book['title'],
                    'author' => $selected_book['author'],
                    'price' => $selected_book['price'],
                    'image' => $selected_book['img'],
                    'qty' => 1
                ];
            }
            $message = "Added '" . htmlspecialchars($selected_book['title']) . "' to your cart!";
        } else {
            $message = "Book not found.";
        }
    }
}

// Total quantity count for header link
$total_cart_items = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_items += $item['qty'];
}
?>
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

        /* Responsive Grid for 20 Books */
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
        }

        .book-title {
            font-size: 15px;
            margin: 5px 0 2px 0;
            text-align: left;
            font-weight: normal;
        }

        .book-author {
            font-size: 13px;
            color: #555;
            margin: 0 0 4px 0;
            text-align: left;
        }

        .book-price {
            font-size: 14px;
            margin: 0 0 15px 0;
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

        /* Banner Features */
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
                <img src="ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo">
                <span>BookShop</span>
            </td>
            <td class="search-box" width="50%" align="center">
                <input type="text" id="searchInput" placeholder="Search books,author,category..." onkeyup="filterBooks()">
            </td>
            <td class="header-links" width="25%">
                <a href="customer_cart.php">Cart(<span id="cart_counter_val"><?php echo $total_cart_items; ?></span>)</a>
                <a href="customer_dashboard.php">Customer</a>
            </td>
        </tr>

        <tr>
            <!-- Sidebar Filtering Links -->
            <td id="sidebar">
                <h2>Categories</h2>
                <span class="cat-link active" onclick="filterCategory('All', this)">All Books</span>
                <span class="cat-link" onclick="filterCategory('Fiction', this)">Fiction</span>
                <span class="cat-link" onclick="filterCategory('Programming', this)">Programming</span>
                <span class="cat-link" onclick="filterCategory('Science', this)">Science</span>
                <span class="cat-link" onclick="filterCategory('Business', this)">Business</span>
                <span class="cat-link" onclick="filterCategory('History', this)">History</span>
                <span class="cat-link" onclick="filterCategory('Self Help', this)">Self Help</span>
            </td>

            <!-- Main Shop Display -->
            <td id="content_area">
                <a href="customer_dashboard.php" class="nav-dashboard-link">&larr; Back to Dashboard</a>

                <?php if (!empty($message)): ?>
                    <div class="msg-box"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="section-header">
                    <h2>Featured Books</h2>
                    <a href="javascript:void(0)" onclick="filterCategory('All', null)">View All</a>
                </div>

                <div class="grid-container" id="booksGrid">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card" data-category="<?php echo htmlspecialchars($book['category']); ?>" data-title="<?php echo htmlspecialchars(strtolower($book['title'])); ?>" data-author="<?php echo htmlspecialchars(strtolower($book['author'])); ?>">
                            <img src="<?php echo htmlspecialchars($book['img']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                            <div class="book-author"><?php echo htmlspecialchars($book['author']); ?></div>
                            <div class="book-price"><?php echo htmlspecialchars($book['price']); ?>TK</div>

                            <form method="POST" action="" onsubmit="return validateAddToCart(this);">
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
                            <h4>Free Delivery</h4>
                            <p>On orders above 5000tk</p>
                        </td>
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
        // JS Category Filter Logic
        function filterCategory(category, element) {
            const cards = document.querySelectorAll('.book-card');
            cards.forEach(card => {
                if (category === 'All' || card.getAttribute('data-category') === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

            if (element) {
                document.querySelectorAll('.cat-link').forEach(link => link.classList.remove('active'));
                element.classList.add('active');
            }
        }

        // JS Real-time Dynamic Search Bar Logic
        function filterBooks() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.book-card');

            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                const author = card.getAttribute('data-author');
                const category = card.getAttribute('data-category').toLowerCase();

                if (title.includes(query) || author.includes(query) || category.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // JS Validation on Add to Cart
        function validateAddToCart(form) {
            const bookId = form.book_id.value;
            if (!bookId || bookId <= 0) {
                alert("Invalid item payload.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>