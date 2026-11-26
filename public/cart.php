<?php
    session_start();

    $baseDir = dirname(__DIR__);
    require_once "$baseDir/php/dbConnect.php";

    $_SESSION['cart'] ??= [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $bookId = filter_input(
            INPUT_POST,
            'book_id',
            FILTER_VALIDATE_INT
        );

        $action = $_POST['action'] ?? '';

        if ($bookId > 0 && $action === 'add') {

            $stmt = $dbConnect->prepare(
                "SELECT stock FROM books WHERE id = :id"
            );

            $stmt->execute(['id' => $bookId]);
            $book = $stmt->fetch();

            $quantity = $_SESSION['cart'][$bookId] ?? 0;

            if ($book && $quantity < $book['stock']) {
                $_SESSION['cart'][$bookId] = $quantity + 1;
            } else {
                $_SESSION['cart_error'] = "Book unavailable or stock limit reached.";
            }
        }
        
        if ($bookId > 0 && isset($_SESSION['cart'][$bookId])) {

            if ($action === 'decrease') {

                $_SESSION['cart'][$bookId]--;

                if ($_SESSION['cart'][$bookId] <= 0) {
                    unset($_SESSION['cart'][$bookId]);
                }

            } elseif ($action === 'remove') {

                unset($_SESSION['cart'][$bookId]);

            }
        }
        header("Location: cart.php", true, 303);
        exit;
    }
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

        <?php if (isset($_SESSION['cart_error'])): ?>
            <p><?= htmlspecialchars($_SESSION['cart_error']) ?></p>
            <?php unset($_SESSION['cart_error']); ?>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>

            <p>Your cart is empty.</p>

        <?php else: ?>

            <?php foreach ($cartItems as $book): ?>

                <div class="cart-item">
                    <img
                        src="<?= htmlspecialchars($book['image_url'] ?? '') ?>"
                        alt="<?= htmlspecialchars($book['name']) ?>"
                        width="100"
                    >

                    <h3><?= htmlspecialchars($book['name']) ?></h3>

                    <p>Price: $<?= number_format($book['price'], 2) ?></p>

                    <form action="cart.php" method="POST">

                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

                    <button type="submit" name="action" value="decrease">-</button>

                    <span><?= $book['quantity'] ?></span>

                    <button type="submit" name="action" value="add">+</button>
                        

                    <button type="submit" name="action" value="remove">Remove</button>

                </form>

                    <p>Total: $<?= number_format($book['item_total'], 2) ?></p>
                </div>

            <?php endforeach; ?>

            <h2>Subtotal: $<?= number_format($subtotal, 2) ?></h2>

        <?php endif; ?>
    </body>
</html>