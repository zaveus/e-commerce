<?php
    $baseDir = dirname(__DIR__);
    require_once "$baseDir/php/dbConnect.php";
    $query = "SELECT * FROM books ORDER BY id";
    $stmt = $dbConnect->query($query);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="all-item">
    <h2>All books</h2>
    <div class="books-container">
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                <img src="<?= htmlspecialchars($book['image_url'] ?? '') ?>" alt="<?= htmlspecialchars($book['name']) ?>" width="150"/>
                <h3><?= htmlspecialchars($book['name']) ?></h3>
                <p><?= htmlspecialchars($book['author']) ?></p>
                <p><?= number_format($book['price'], 2) ?></p>
                <p>Stock: <?= $book['stock'] ?></p>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="book_id"
                        value="<?= $book['id'] ?>">

                    <input type="hidden" name="action" value="add">

                    <input type="hidden" name="csrf_token"
                        value="<?= $_SESSION['csrf_token'] ?>">

                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>