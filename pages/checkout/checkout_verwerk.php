<?php
session_start();
require_once '../../config/config.php';

// Initialize basket in session if not exists
if (!isset($_SESSION['basket']) || empty($_SESSION['basket'])) {
    $_SESSION['error'] = "Your basket is empty";
    header('Location: basket.php');
    exit;
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
                'price' => $product['prijs'],
                'quantity' => $quantity,
                'total' => $itemTotal
            ];
            $totalPrice += $itemTotal;
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

$grandTotal = $totalPrice + $deliveryPrice;

// Get stored addresses for the user
$savedAddresses = [];
if ($isLoggedIn) {
    try {
        $sql = "SELECT * FROM adressen WHERE gebruiker_id = ? ORDER BY is_default DESC";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $savedAddresses = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Table might not exist yet
    }
}

$errors = [];
$formData = [
    'voornaam' => '',
    'achternaam' => '',
    'email' => '',
    'telefoonnummer' => '',
    'straat' => '',
    'huisnummer' => '',
    'postcode' => '',
    'stad' => '',
    'land' => 'Nederland',
    'betaalmethode' => 'creditcard'
];

// Pre-fill with logged in user info if exists
if ($isLoggedIn) {
    if (!empty($savedAddresses)) {
        $defaultAddress = current($savedAddresses);
        $formData['straat'] = $defaultAddress['straat'] ?? '';
        $formData['huisnummer'] = $defaultAddress['huisnummer'] ?? '';
        $formData['postcode'] = $defaultAddress['postcode'] ?? '';
        $formData['stad'] = $defaultAddress['stad'] ?? '';
        $formData['land'] = $defaultAddress['land'] ?? 'Nederland';
    }
    
    // Get user info
    try {
        $sql = "SELECT * FROM gebruikers WHERE id = ?";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $formData['voornaam'] = $user['voornaam'] ?? '';
            $formData['achternaam'] = $user['achternaam'] ?? '';
            $formData['email'] = $user['email'] ?? '';
            $formData['telefoonnummer'] = $user['telefoonnummer'] ?? '';
        }
    } catch (PDOException $e) {
        // Column might not exist
    }
}
