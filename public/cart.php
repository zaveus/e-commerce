
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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Shopping Cart</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <h1>Shopping Cart</h1>

        <a href="index.php">Continue Shopping</a>

        <p id="empty-cart-message"
            <?= !empty($cartItems) ? 'hidden' : '' ?>>
            Your cart is empty.
        </p>

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

        <h2>
            Subtotal: $<span id="cart-subtotal"><?= number_format($subtotal, 2) ?></span>
        </h2>

        <script src="js/cart.js"></script>
    </body>
</html>
