<?php
session_start();
require_once '../../config/config.php';

// Check if basket is empty
if (!isset($_SESSION['basket']) || empty($_SESSION['basket'])) {
    $_SESSION['error'] = "Your basket is empty";
    header('Location: ../basket/basket.php');
    exit;
}

$errors = [];
$addressId = null;
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;
$guestEmail = null;

try {
    // Get address information
    $addressOption = $_POST['address_option'] ?? 'new';
    
    if ($isLoggedIn && $addressOption === 'default') {
        // Get the most recent address for logged-in user
        $sql = "SELECT id FROM adressen WHERE gebruiker_id = ? ORDER BY id DESC LIMIT 1";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$userId]);
        $address = $stmt->fetch();
        
        if ($address) {
            $addressId = $address['id'];
        } else {
            $errors[] = "No saved address found. Please enter an address.";
        }
    } else {
        // Validate and save new address
        $voornaam = trim($_POST['voornaam'] ?? '');
        $achternaam = trim($_POST['achternaam'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefoonnummer = trim($_POST['telefoonnummer'] ?? '');
        $straat = trim($_POST['straat'] ?? '');
        $huisnummer = trim($_POST['huisnummer'] ?? '');
        $postcode = trim($_POST['postcode'] ?? '');
        $stad = trim($_POST['stad'] ?? '');
        $land = trim($_POST['land'] ?? 'Nederland');

        if (empty($voornaam) || empty($achternaam) || empty($email) || empty($telefoonnummer) || empty($straat) || empty($huisnummer) || empty($postcode) || empty($stad)) {
            $errors[] = "All fields are required";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address";
        }

        if (empty($errors)) {
            $guestEmail = $email;
            
            // Insert address
            if ($isLoggedIn) {
                $sql = "INSERT INTO adressen (gebruiker_id, straat, huisnummer, postcode, stad, land) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $PDO->prepare($sql);
                $stmt->execute([$userId, $straat, $huisnummer, $postcode, $stad, $land]);
            } else {
                // For guests, store minimal address info
                $sql = "INSERT INTO adressen (straat, huisnummer, postcode, stad, land) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $PDO->prepare($sql);
                $stmt->execute([$straat, $huisnummer, $postcode, $stad, $land]);
            }
            $addressId = $PDO->lastInsertId();
        }
    }

    if (empty($errors)) {
        // Get payment method
        $betaalmethode = $_POST['betaalmethode'] ?? 'creditcard';
        $totalPrice = (float)($_POST['total_price'] ?? 0);

        // Calculate order total from basket (security: don't trust form submission)
        $calculatedTotal = 5.00; // delivery price
        foreach ($_SESSION['basket'] as $productId => $quantity) {
            $sql = "SELECT prijs FROM producten WHERE id = ?";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            if ($product) {
                $calculatedTotal += ($product['prijs'] * $quantity);
            }
        }

        // Create order
        $sql = "INSERT INTO bestellingen (gebruiker_id, adres_id, totaal_prijs, status, betaalmethode, aangemaakt_op, bijgewerkt_op)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $PDO->prepare($sql);
        
        // Use NULL for guest orders (no user_id)
        $orderUserId = $isLoggedIn ? $userId : null;
        $stmt->execute([$orderUserId, $addressId, $calculatedTotal, 'pending', $betaalmethode]);
        
        $orderId = $PDO->lastInsertId();

        // Insert order items
        $sql = "INSERT INTO bestellings_items (bestelling_id, product_id, aantal, prijs_per_stuk)
                VALUES (?, ?, ?, ?)";
        
        foreach ($_SESSION['basket'] as $productId => $quantity) {
            $productSql = "SELECT prijs FROM producten WHERE id = ?";
            $productStmt = $PDO->prepare($productSql);
            $productStmt->execute([$productId]);
            $product = $productStmt->fetch();
            
            if ($product) {
                $stmt = $PDO->prepare($sql);
                $stmt->execute([$orderId, $productId, $quantity, $product['prijs']]);
            }
        }

        // Clear basket
        $_SESSION['basket'] = [];
        $_SESSION['success'] = "Order placed successfully! Order #" . $orderId;
        
        // Store order ID for guests (for confirmation page)
        if (!$isLoggedIn) {
            $_SESSION['guest_order_id'] = $orderId;
            $_SESSION['guest_email'] = $guestEmail;
        }
        
        // Redirect to order confirmation
        header('Location: ../orders/order_confirmation.php?order_id=' . $orderId);
        exit;
    }

} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(", ", $errors);
    header('Location: checkout.php');
    exit;
}
