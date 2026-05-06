<?php
define('DB_HOST', getenv('MYSQL_ADDON_HOST') ?: getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('MYSQL_ADDON_USER') ?: getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('MYSQL_ADDON_PASSWORD') ?: getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('MYSQL_ADDON_DB') ?: getenv('DB_NAME') ?: 'pharmacy_db');
define('DB_PORT', getenv('MYSQL_ADDON_PORT') ?: getenv('DB_PORT') ?: '3306');

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Chi tiet loi: " . $e->getMessage());
}
