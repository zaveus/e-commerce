<?php
    session_start();

    $baseDir = dirname(__DIR__);
    require_once "$baseDir/php/dbConnect.php";

    $_SESSION['cart'] ??= [];

    $cartItems = [];
    $subtotal = 0;

    $stmt = $dbConnect->prepare(
        "SELECT * FROM books WHERE id = :id"
    );

    foreach ($_SESSION['cart'] as $bookId => $quantity) {
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch();

        if ($book) {
            $book['quantity'] = $quantity;
            $book['item_total'] = $book['price'] * $quantity;

            $subtotal += $book['item_total'];
            $cartItems[] = $book;
        }
    }
    // ADding taxes

    $taxRate = 0.06625;
    $tax = round($subtotal * $taxRate, 2);
    $total = $subtotal + $tax;
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Shopping Cart</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body class="cart-page">
        <header class="cart-header">
            <h1>Books On The Go!</h1>
            <a href="index.php">Continue Shopping</a>
        </header>
        <main class="cart-wrapper">
            <h1>Shopping Cart</h1>

            <a href="index.php" class="continue-shopping">< Continue Shopping</a>

            <p id="empty-cart-message"
                <?= !empty($cartItems) ? 'hidden' : '' ?>>
                Your cart is empty.
            </p>
            <div class="cart-layout">
                <div class="cart-container">

                    <?php foreach ($cartItems as $book): ?>

                        <div class="cart-item"
                            data-book-id="<?= (int) $book['id'] ?>"
                            data-price-cents="<?= (int) round($book['price'] * 100) ?>">

                            <img
                                src="<?= htmlspecialchars($book['image_url'] ?? '') ?>"
                                alt="<?= htmlspecialchars($book['name']) ?>"
                                width="100"
                            >

                            <h3><?= htmlspecialchars($book['name']) ?></h3>

                            <p>
                                Price: $<?= number_format($book['price'], 2) ?>
                            </p>

                            <div class="quantity-controls">

                                <button type="button"
                                    onclick="updateCart(<?= (int) $book['id'] ?>, 'decrease')">
                                    -
                                </button>

                                <span class="cart-quantity">
                                    <?= $book['quantity'] ?>
                                </span>

                                <button type="button"
                                    onclick="updateCart(<?= (int) $book['id'] ?>, 'add')">
                                    +
                                </button>

                                <button type="button"
                                    onclick="updateCart(<?= (int) $book['id'] ?>, 'remove')">
                                    Remove
                                </button>

                            </div>

                            <p>
                                Total: $<span class="cart-item-total"><?= number_format($book['item_total'], 2) ?></span>
                            </p>

                        </div>

                    <?php endforeach; ?>

                </div>

                <div class="cart-summary">
                    <p>
                        Subtotal: <span id="cart-subtotal">$<?= number_format($subtotal, 2) ?></span>
                    </p>

                    <p>
                        Tax (6.625%): <span id="cart-tax">$<?= number_format($tax, 2) ?></span>
                    </p>

                    <h2>
                        Total: <span id="cart-total">$<?= number_format($total, 2) ?></span>
                    </h2>
                    <a href="checkout.php" class="checkout-button">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </main>
        <script src="js/cart.js"></script>
    </body>
</html>
