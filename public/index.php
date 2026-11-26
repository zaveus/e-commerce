<?php
    session_start();

    $baseDir = dirname(__DIR__);
    require_once "$baseDir/php/dbConnect.php";

    $_SESSION['cart'] ??= [];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Books On The Go! E-Commerce Platform</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <header>
            <h1>Books On The Go!</h1>

            <a href="cart.php">
                Shopping Cart (<?= array_sum($_SESSION['cart']) ?>)
            </a>
        </header>

        <div id="featured-items">
            <?php require_once 'featuredItems.php'; ?>
        </div>

        <div id="all-items">
            <?php require_once 'allItems.php'; ?>
        </div>

        <script src="assets/js/script.js"></script>
    </body>
</html>