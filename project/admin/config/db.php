<?php
$host = 'sql312.infinityfree.com';
$dbname = 'if0_39187569_portfolio_db';
$username = 'if0_39187569';
$password = 'Pmpk132006'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>