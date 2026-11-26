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

        header("Location: cart.php", true, 303);
        exit;
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

        <pre>
            <?php print_r($_SESSION['cart']); ?>
        </pre>
    </body>
</html>