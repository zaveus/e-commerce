
<?php
    session_start();

    header("Content-Type: application/json");
    header("Cache-Control: no-store");

    $baseDir = dirname(__DIR__, 2);
    require_once "$baseDir/php/dbConnect.php";

    $_SESSION['cart'] ??= [];

    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);

        echo json_encode([
            "success" => false,
            "message" => "Method not allowed."
        ]);

        exit;
    }

    // Retrieve and validate input
    $bookId = filter_input(
        INPUT_POST,
        'book_id',
        FILTER_VALIDATE_INT
    );

    $action = $_POST['action'] ?? '';

    if (!$bookId || $bookId < 1 ||
        !in_array($action, ['add', 'decrease', 'remove'], true)) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Invalid request."
        ]);

        exit;
    }

    $quantity = $_SESSION['cart'][$bookId] ?? 0;

    // Add or increase quantity
    if ($action === 'add') {

        $stmt = $dbConnect->prepare(
            "SELECT stock FROM books WHERE id = :id"
        );

        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch();

        if (!$book || $quantity >= $book['stock']) {
            http_response_code(409);

            echo json_encode([
                "success" => false,
                "message" => "Stock limit reached or book unavailable."
            ]);

            exit;
        }

        $_SESSION['cart'][$bookId] = $quantity + 1;

    // Decrease quantity
    } elseif ($action === 'decrease') {

        if ($quantity > 1) {
            $_SESSION['cart'][$bookId]--;
        } else {
            unset($_SESSION['cart'][$bookId]);
        }

    // Remove the entire item
    } elseif ($action === 'remove') {

        unset($_SESSION['cart'][$bookId]);

    }

    // Return updated cart to JavaScript
    echo json_encode([
        "success" => true,
        "cart" => (object) $_SESSION['cart'],
        "count" => array_sum($_SESSION['cart'])
    ]);
