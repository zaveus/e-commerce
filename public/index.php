<?php
    require_once __DIR__ . '/../php/dbConnect.php';

?>

<!DOCTYPE html>
<!-- This is the main storefront like stores etc. -->
<html>
    <head>
        <title>Books On The Go! E-Commerce Platform</title>
        <link rel="stylesheet" href="style.css">
        
    </head>
    <script src="assets/js/script.js"></script>
    <body>
        <div id="featured-items">
            <!-- Have here become a php script to get the featured items that uses javascript to scroll through items and diplay dynamically -->
            <?php 
                require_once 'featuredItems.php';
                require_once 'allItems.php';
            ?>
        </div>
    </body>

</html>