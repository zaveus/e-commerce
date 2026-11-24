<?php
    $baseDir = dirname(__DIR__);
    require_once "$baseDir/php/dbConnect.php";
?>

<!DOCTYPE html>
<!-- This is the main storefront like stores etc. -->
<html>
    <head>
        <title>Books On The Go! E-Commerce Platform</title>
        <link rel="stylesheet" href="assets/css/style.css">
        
    </head>
    <script src="assets/js/script.js"></script>
    <body>
        <div id="featured-items">
            <h1>Featured Items</h1>
            <!-- Have here become a php script to get the featured items that uses javascript to scroll through items and diplay dynamically -->
            <?php 
                //require_once 'featuredItems.php';

            ?>
        </div>
        <div><h1> All Items</h1></div>
        <div id="all-items">
             <?php 
                //require_once 'allItems.php';
            ?>
        </div>
    </body>

</html>