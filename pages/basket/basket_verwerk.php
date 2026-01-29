<?php
session_start();
require_once '../../config/config.php';

// Initialize basket in session if not exists
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get basket items with product details
$basketItems = [];
$totalPrice = 0;
$deliveryPrice = 5.00;

try {
    foreach ($_SESSION['basket'] as $productId => $quantity) {
        $sql = "SELECT * FROM producten WHERE id = ?";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            $itemTotal = $product['prijs'] * $quantity;
            $basketItems[] = [
                'id' => $product['id'],
                'name' => $product['naam'],
                'description' => $product['beschrijving'],
                'price' => $product['prijs'],
                'image' => $product['afbeelding'],
                'quantity' => $quantity,
                'total' => $itemTotal
            ];
            $totalPrice += $itemTotal;
        }
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}

$grandTotal = $totalPrice + $deliveryPrice;
