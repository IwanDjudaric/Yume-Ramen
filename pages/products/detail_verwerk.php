<?php
session_start();
require_once '../../config/config.php';

$productId = $_GET['id'] ?? null;
$product = null;
$error = '';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

if (!$productId) {
    header('Location: scroll.php');
    exit;
}

try {
    $sql = "SELECT * FROM producten WHERE id = ?";
    $stmt = $PDO->prepare($sql);
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        $error = "Product not found.";
    }
} catch (PDOException $e) {
    $error = "Error fetching product: " . $e->getMessage();
}
