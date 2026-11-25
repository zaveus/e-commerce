<?php
    require_once __DIR__ . '/../php/dbConnect.php';

    $query = "SELECT * FROM books WHERE is_featured = TRUE ORDER BY id";
    $stmt = $dbConnect->query($query);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="featured-items">
    <h2>Featured Books</h2>
    <div class="books-container-scroll">
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                <img src="<?= htmlspecialchars($book['image_url'] ?? '') ?>" alt="<?= htmlspecialchars($book['name']) ?>" width="150"/>
                <h3><?= htmlspecialchars($book['name']) ?></h3>
                <p><?= htmlspecialchars($book['author']) ?></p>
                <p><?= number_format($book['price'], 2) ?></p>
                <p>Stock: <?= $book['stock'] ?></p>
                <button type="button">Add To Cart</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>