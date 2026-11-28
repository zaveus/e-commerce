<?php
session_start();

$baseDir = dirname(__DIR__);
require_once "$baseDir/php/dbConnect.php";

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

$books = [];
$subtotal = 0;

$stmt = $dbConnect->prepare(
    "SELECT * FROM books WHERE id = :id"
);

foreach ($cart as $bookId => $quantity) {
    $stmt->execute(['id' => $bookId]);
    $book = $stmt->fetch();

    if ($book) {
        $book['quantity'] = $quantity;
        $book['item_total'] = $book['price'] * $quantity;

        $subtotal += $book['item_total'];
        $books[] = $book;
    }
}

if (empty($books)) {
    header("Location: cart.php");
    exit;
}

$taxRate = 0.06625;
$tax = round($subtotal * $taxRate, 2);
$total = $subtotal + $tax;

$_SESSION['checkout_csrf_token'] ??= bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Books On The Go!</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="cart-page">

    <header class="cart-header">
        <h1>Books On The Go!</h1>
        <a href="cart.php">Back to Cart</a>
    </header>

    <main class="cart-wrapper">

        <h1>Checkout</h1>

        <div class="checkout-layout">

            <div class="checkout-details">

                <h2>Customer Information</h2>

                <form id="checkout-form"
                      action="api/orders.php"
                      method="POST">

                    <input type="hidden"
                           name="csrf_token"
                           value="<?= htmlspecialchars($_SESSION['checkout_csrf_token']) ?>">

                    <label for="email">Email Address</label>

                    <input type="email"
                           id="email"
                           name="email"
                           placeholder="Enter your email address"
                           required>

                    <button type="submit" class="checkout-button">
                        Place Order
                    </button>

                </form>
                <div id="checkout-message"></div>
            </div>

            <div class="cart-summary">

                <h3>Order Summary</h3>

                <?php foreach ($books as $book): ?>

                    <p>
                        <span>
                            <?= htmlspecialchars($book['name']) ?>
                            x <?= $book['quantity'] ?>
                        </span>

                        <span>$<?= number_format($book['item_total'], 2) ?></span>
                    </p>

                <?php endforeach; ?>

                <hr>

                <p>
                    Subtotal:<span>$<?= number_format($subtotal, 2) ?></span>
                </p>

                <p>
                    Tax (6.625%):<span>$<?= number_format($tax, 2) ?></span>
                </p>

                <h2>
                    Total: $<?= number_format($total, 2) ?>
                </h2>

            </div>

        </div>

    </main>
<script src="js/checkout.js"></script>
</body>
</html>
