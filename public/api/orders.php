<?php
session_start();

header("Content-Type: application/json");

$baseDir = dirname(__DIR__, 2);
require_once "$baseDir/php/dbConnect.php";

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Validate CSRF token
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    !isset($_SESSION['checkout_csrf_token']) ||
    !hash_equals($_SESSION['checkout_csrf_token'], $token)
) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid security token.'
    ]);
    exit;
}

// Validate customer email
$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address.'
    ]);
    exit;
}

// Retrieve shopping cart
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Your cart is empty.'
    ]);
    exit;
}

try {
    // Start transaction
    $dbConnect->beginTransaction();

    $books = [];
    $subtotalCents = 0;
    $taxRate = 0.06625;

    ksort($cart, SORT_NUMERIC);

    // Retrieve and lock book records
    $stmt = $dbConnect->prepare(
        "SELECT id, name, price, stock
         FROM books
         WHERE id = :id
         FOR UPDATE"
    );

    foreach ($cart as $bookId => $quantity) {
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch();

        if (!$book) {
            throw new DomainException(
                "A book is no longer available."
            );
        }

        if ($quantity <= 0 || $quantity > $book['stock']) {
            throw new DomainException(
                "Insufficient stock for {$book['name']}."
            );
        }

        $book['quantity'] = $quantity;

        $priceCents = (int) round($book['price'] * 100);
        $subtotalCents += $priceCents * $quantity;

        $books[] = $book;
    }

    // Calculate final total
    $taxCents = (int) round($subtotalCents * $taxRate);
    $total = ($subtotalCents + $taxCents) / 100;

    // Create order
    $stmt = $dbConnect->prepare(
        "INSERT INTO orders (customer_email, total, status)
         VALUES (:email, :total, 'confirmed')
         RETURNING id"
    );

    $stmt->execute([
        'email' => $email,
        'total' => $total
    ]);

    $orderId = $stmt->fetchColumn();

    // Prepare order items insertion
    $itemStmt = $dbConnect->prepare(
        "INSERT INTO order_items
         (order_id, book_id, quantity, unit_price)
         VALUES
         (:order_id, :book_id, :quantity, :unit_price)"
    );

    // Prepare stock update
    $stockStmt = $dbConnect->prepare(
        "UPDATE books
         SET stock = stock - :quantity
         WHERE id = :book_id"
    );

    foreach ($books as $book) {
        $itemStmt->execute([
            'order_id' => $orderId,
            'book_id' => $book['id'],
            'quantity' => $book['quantity'],
            'unit_price' => $book['price']
        ]);

        $stockStmt->execute([
            'quantity' => $book['quantity'],
            'book_id' => $book['id']
        ]);
    }

    // Save all database changes
    $dbConnect->commit();

    // Clear shopping cart and checkout token
    $_SESSION['cart'] = [];
    unset($_SESSION['checkout_csrf_token']);

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $orderId,
        'total' => $total
    ]);

} catch (DomainException $e) {
    if ($dbConnect->inTransaction()) {
        $dbConnect->rollBack();
    }

    http_response_code(409);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

} catch (Throwable $e) {
    if ($dbConnect->inTransaction()) {
        $dbConnect->rollBack();
    }

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to process order.'
    ]);
}
