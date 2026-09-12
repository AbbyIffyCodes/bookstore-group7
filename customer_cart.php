<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart & Checkout - BookShop</title>
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

        .header-links {
            text-align: right;
            font-size: 16px;
        }

        .header-links a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
        }

        #main_content {
            vertical-align: top;
            padding: 40px;
        }

        #cart_table {
            width: 100%;
            border-collapse: collapse;
        }

        #cart_box {
            width: 68%;
            background-color: #f3ecee;
            vertical-align: top;
            padding: 30px;
            border-radius: 4px;
        }

        #summary_box {
            width: 28%;
            background-color: #f3ecee;
            vertical-align: top;
            padding: 30px;
            border-radius: 4px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            font-size: 18px;
            font-weight: normal;
            padding-bottom: 25px;
            color: #000;
        }

        .items-table td {
            padding: 15px 0;
            vertical-align: middle;
            font-size: 15px;
        }

        .book-img {
            width: 70px;
            height: 95px;
            object-fit: cover;
            border-radius: 2px;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.2);
        }

        .qty-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 0 8px;
            font-weight: bold;
        }

        .qty-val {
            font-size: 15px;
            display: inline-block;
            width: 20px;
            text-align: center;
        }

        .summary-title {
            font-size: 22px;
            text-align: center;
            margin-top: 0;
            margin-bottom: 30px;
            font-weight: normal;
        }

        .summary-row {
            width: 100%;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .summary-row.total {
            font-size: 22px;
            margin-top: 30px;
        }

        .btn-checkout {
            width: 100%;
            height: 48px;
            background-color: #4c63ee;
            color: white;
            border: none;
            font-size: 15px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 25px;
        }

        .btn-checkout:hover {
            background-color: #3b50cb;
        }

        .btn-continue {
            display: block;
            width: 100%;
            height: 48px;
            line-height: 48px;
            background-color: #ede6e8;
            color: #000000;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            border-radius: 4px;
            margin-top: 15px;
            box-sizing: border-box;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #4c63ee;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .empty-msg {
            text-align: center;
            font-size: 18px;
            color: #777;
            padding: 40px 0;
        }

        .error-box {
            color: red;
            border: 1px solid red;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #ffe6e6;
        }

        .success-box {
            color: green;
            border: 1px solid green;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #e6ffe6;
        }
    </style>
</head>
<body>

    <table id="page_wrapper">
        <tr id="top_bar">
            <td class="logo-section">
                <img src="../public/images/ONLINE_BOOKSHOP_LOGO.jpg" alt="BookShop Logo">
                <span>BookShop</span>
            </td>
            <td class="header-links">
                <a href="../controllers/customer_cart_controller.php">Cart(<?php echo $total_items; ?>)</a>
                <a href="../controllers/customer_dashboard_controller.php">Customer</a>
            </td>
        </tr>

        <tr>
            <td id="main_content" colspan="2">
                <a href="../controllers/customer_dashboard_controller.php" class="back-link">&larr; Back to Dashboard</a>

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

                <table id="cart_table">
                    <tr>
                        <td id="cart_box">
                            <?php if (empty($_SESSION['cart'])): ?>
                                <div class="empty-msg">Your cart is currently empty.</div>
                            <?php else: ?>
                                <form id="updateForm" action="../controllers/customer_cart_controller.php" method="POST">
                                    <input type="hidden" name="action" value="update_cart">
                                    <table class="items-table">
                                        <thead>
                                            <tr>
                                                <th align="left" width="45%">Book</th>
                                                <th align="center" width="18%">Price</th>
                                                <th align="center" width="18%">Qty</th>
                                                <th align="right" width="19%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                                                <tr>
                                                    <td>
                                                        <table role="presentation">
                                                            <tr>
                                                                <td style="padding:0 15px 0 0;">
                                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Cover" class="book-img">
                                                                </td>
                                                                <td style="padding:0;"><?php echo htmlspecialchars($item['title']); ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td align="center"><?php echo $item['price']; ?>TK</td>
                                                    <td align="center">
                                                        <button type="button" class="qty-btn" onclick="adjustQty(<?php echo $id; ?>, -1)">-</button>
                                                        <span class="qty-val" id="qty_val_<?php echo $id; ?>"><?php echo $item['qty']; ?></span>
                                                        <input type="hidden" name="quantities[<?php echo $id; ?>]" id="input_qty_<?php echo $id; ?>" value="<?php echo $item['qty']; ?>">
                                                        <button type="button" class="qty-btn" onclick="adjustQty(<?php echo $id; ?>, 1)">+</button>
                                                    </td>
                                                    <td align="right"><?php echo $item['price'] * $item['qty']; ?>TK</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </form>
                            <?php endif; ?>
                        </td>

                        <td width="4%"></td>

                        <td id="summary_box">
                            <h2 class="summary-title">Order Summary</h2>

                            <table class="summary-row">
                                <tr>
                                    <td>Subtotal</td>
                                    <td align="right"><?php echo $subtotal; ?>TK</td>
                                </tr>
                            </table>

                            <table class="summary-row">
                                <tr>
                                    <td>Delivery</td>
                                    <td align="right"><?php echo ($subtotal > 0) ? $delivery_fee : 0; ?>TK</td>
                                </tr>
                            </table>

                            <table class="summary-row total">
                                <tr>
                                    <td><b>Total</b></td>
                                    <td align="right"><b><?php echo $grand_total; ?>TK</b></td>
                                </tr>
                            </table>

                            <form action="../controllers/customer_cart_controller.php" method="POST" onsubmit="return validateCartCheckout();">
                                <input type="hidden" name="action" value="checkout">
                                <button type="submit" class="btn-checkout">Proceed to Checkout</button>
                            </form>
                            <a href="../controllers/customer_shop_controller.php" class="btn-continue">Continue Shopping</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <script>
        function adjustQty(id, change) {
            const inputField = document.getElementById('input_qty_' + id);
            let currentVal = parseInt(inputField.value);
            
            if (currentVal + change >= 0) {
                inputField.value = currentVal + change;
                document.getElementById('updateForm').submit();
            }
        }

        function validateCartCheckout() {
            const itemCount = <?php echo $total_items; ?>;
            if (itemCount <= 0) {
                alert("Your cart is empty! Please add books from the shop before checking out.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>