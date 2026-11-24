<?php

    $db_host = getenv("DB_HOST");
    $db_port = getenv("DB_PORT") ?: "5432";
    $db_name = getenv("DB_NAME");
    $db_user = getenv("DB_USER");
    $db_password = getenv("DB_PASSWORD");

    try {
        $dbConnect = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_password);
        $dbConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbConnect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $dbConnect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit("Database connection failed.");
    }

?>