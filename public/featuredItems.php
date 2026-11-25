<?php
    require_once __DIR__ . '/../php/dbConnect.php';


    $query = "SELECT * FROM books ORDER BY id WHERE is_featured = TRUE;
    $stmt = $dbConnect->query($query);
    $ooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>