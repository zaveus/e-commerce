<?php
    session_start();

    $baseDir = dirname(__DIR__);
    require_once "$baseDir/php/dbConnect.php";

    $_SESSION['cart'] ??= [];
    $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $token = $_POST['csrf_token'] ?? '';

        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit("Invalid Request.");
        }

        $bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);

        $action = $_POST['action'] ?? '';

        if ($bookId > 0 && &action === 'add') {

            $stmt = $dbConnect->prepare("SELECT stock FROM books WHERE id = :id");
        }

        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch();

        $quantity = $_SESSION['cart'][$bookId] ?? 0;

        if ($book && $quantity < $book['stock']) {
            $_SESSION['cart'][$bookId] = $quantity + 1;
        } else {
            $_SESSION['cart_error'] = "Book is out of stock.";
    
        }
    }

        header("Location: /cart.php", true, 303);
        exit();


?>

<h1>Shopping Cart</h1>

<a href="index.php">Continue Shopping</a>

<?php if (isset($_SESSION['cart_error'])): ?>
    <p><?= htmlspecialchars($_SESSION['cart_error']) ?></p>
    <?php unset($_SESSION['cart_error']); ?>
<?php endif; ?>

<pre>
    <?php print_r($_SESSION['cart']); ?>
</pre>