<?php
    require_once __DIR__ . '/../php/dbConnect.php';


    $query = "SELECT * FROM books ORDER BY id";
    $stmt = $dbConnect->query($query);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="all-item">
    <h2>All books</h2>
    <div class="all-item__list">
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