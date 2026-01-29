<?php
session_start();
require_once '../../config/config.php';

$producten = [];
$error = '';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

try {
    $sql = "SELECT * FROM producten ORDER BY id DESC";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();
    $producten = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching products: " . $e->getMessage();
}
