<?php
session_start();
require_once 'config/config.php';

// Initialize basket in session if not exists
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

$action = $_POST['action'] ?? '';
$productId = $_POST['product_id'] ?? null;

if ($action === 'add' && $productId) {
    // Add or increase quantity
    if (isset($_SESSION['basket'][$productId])) {
        $_SESSION['basket'][$productId]++;
    } else {
        $_SESSION['basket'][$productId] = 1;
    }
    $_SESSION['success'] = "Product added to basket!";
} 
elseif ($action === 'remove' && $productId) {
    // Remove item from basket
    if (isset($_SESSION['basket'][$productId])) {
        unset($_SESSION['basket'][$productId]);
    }
    $_SESSION['success'] = "Product removed from basket!";
} 
elseif ($action === 'update' && $productId) {
    // Update quantity
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($quantity <= 0) {
        unset($_SESSION['basket'][$productId]);
    } else {
        $_SESSION['basket'][$productId] = $quantity;
    }
}
elseif ($action === 'checkout') {
    // TODO: Process checkout and create order
    if (empty($_SESSION['basket'])) {
        $_SESSION['error'] = "Basket is empty!";
        header('Location: basket.php');
        exit;
    }
    
    // For now, just clear the basket (implement full order processing as needed)
    $_SESSION['success'] = "Order placed successfully!";
    $_SESSION['basket'] = [];
    header('Location: basket.php');
    exit;
}
elseif ($action === 'clear') {
    // Clear entire basket
    $_SESSION['basket'] = [];
    $_SESSION['success'] = "Basket cleared!";
}

// Redirect based on action
if ($action === 'add') {
    // For add action, go back to referrer if available, otherwise go to scroll
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'scroll.php';
    header('Location: ' . $referrer);
} else {
    // For remove, update, or checkout, go to basket
    header('Location: basket.php');
}
exit;
