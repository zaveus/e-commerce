<?php
    session_start();

    header("Content-Type: application/json");
    header("Cache-Control: no-store");

    $baseDir = dirname(__DIR__, 2);
    require_once "$baseDir/php/dbConnect.php";

    $_SESSION['cart'] ??= [];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }

    $bookId = filter_input(
        INPUT_POST,
        'book_id',
        FILTER_VALIDATE_INT
    );

    $action = $_POST['action'] ?? '';

    if (!$bookId || $bookId < 1 ||
        !in_array($action, ['add', 'decrease', 'remove'], true)) {

        http_response_code(400);
        echo json_encode(["success" => false]);
        exit;
    }

    $quantity = $_SESSION['cart'][$bookId] ?? 0;

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

    } elseif ($action === 'decrease') {

        if ($quantity > 1) {
            $_SESSION['cart'][$bookId]--;
        } else {
            unset($_SESSION['cart'][$bookId]);
        }

    } elseif ($action === 'remove') {

        unset($_SESSION['cart'][$bookId]);
    }

    echo json_encode([
        "success" => true,
        "cart" => (object) $_SESSION['cart'],
        "count" => array_sum($_SESSION['cart'])
    ]);