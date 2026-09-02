<?php
session_start();
require_once 'dbconnection.php';

if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'customer') {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if (isset($_GET['action']) && $_GET['action'] === 'search_books') {
    header('Content-Type: application/json');

    $category = $_GET['category'] ?? '';
    $query = $_GET['query'] ?? '';

    $sql = "SELECT BookID, Title, Price, Category, Image FROM books WHERE Title LIKE ?";
    $searchTerm = "%" . $query . "%";

    if (!empty($category) && $category !== 'All') {
        $sql .= " AND Category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $category);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $searchTerm);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];

    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'id'       => $row['BookID'],
            'title'    => $row['Title'],
            'price'    => $row['Price'],
            'category' => $row['Category'],
            'img'      => !empty($row['Image']) ? $row['Image'] : 'images/default_cover.jpg'
        ];
    }

    echo json_encode($books);
    exit; 
}

$message = "";


$books = [];
$query = "SELECT BookID, Title, Price, Category, Image FROM books";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'id'       => $row['BookID'],
            'title'    => $row['Title'],
            'price'    => $row['Price'],
            'category' => $row['Category'],
            'img'      => !empty($row['Image']) ? $row['Image'] : 'images/default_cover.jpg'
        ];
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);

    if ($book_id <= 0) {
        $message = "Invalid book selection.";
    } else {
        $stmt = $conn->prepare("SELECT BookID, Title, Price, Image FROM books WHERE BookID = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $selected_book = $res->fetch_assoc()) {
            if (isset($_SESSION['cart'][$book_id])) {
                $_SESSION['cart'][$book_id]['qty'] += 1;
            } else {
                $_SESSION['cart'][$book_id] = [
                    'id'    => $selected_book['BookID'],
                    'title' => $selected_book['Title'],
                    'price' => $selected_book['Price'],
                    'image' => !empty($selected_book['Image']) ? $selected_book['Image'] : 'images/default_cover.jpg',
                    'qty'   => 1
                ];
            }
            $message = "Added '" . htmlspecialchars($selected_book['Title']) . "' to your cart!";
        } else {
            $message = "Book not found.";
        }
        $stmt->close();
    }
}

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
                <img src="ONLINE_BOOKSHOP_LOGO.jpg" alt="Logo">
                <span>BookShop</span>
            </td>
            <td class="search-box" width="50%" align="center">
                <input type="text" id="searchInput" placeholder="Search books, category...">
            </td>
            <td class="header-links" width="25%">
                <a href="customer_cart.php">Cart(<span id="cart_counter_val"><?php echo $total_cart_items; ?></span>)</a>
                <a href="customer_dashboard.php">Customer</a>
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
                <span class="cat-link" onclick="selectCategory('Self Help', this)">Self Help</span>
            </td>

            <td id="content_area">
                <a href="customer_dashboard.php" class="nav-dashboard-link">&larr; Back to Dashboard</a>

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

            const params = new URLSearchParams({
                action: 'search_books',
                category: currentSelectedCategory,
                query: searchQuery
            });

            fetch(`customer_shop.php?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const grid = document.getElementById('booksGrid');
                grid.innerHTML = ''; 

                if (data.length === 0) {
                    grid.innerHTML = '<p>No books found.</p>';
                    return;
                }

                data.forEach(book => {
                    grid.innerHTML += `
                        <div class="book-card" data-category="${book.category}" data-title="${book.title.toLowerCase()}">
                            <img src="${book.img}" alt="${book.title}">
                            <div class="book-title">${book.title}</div>
                            <div class="book-price">${book.price} TK</div>
                            <form method="POST" action="" onsubmit="return validateAddToCart(this);">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="book_id" value="${book.id}">
                                <button type="submit" class="add-btn">Add to Cart</button>
                            </form>
                        </div>
                    `;
                });
            })
            .catch(error => console.error('Error fetching books:', error));
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

        document.getElementById('searchInput')?.addEventListener('input', fetchBooks);
    </script>
</body>
</html>
