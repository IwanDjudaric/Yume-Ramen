<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

        if (empty($email) || empty($telefoonnummer) || empty($straat) || empty($huisnummer) || empty($postcode) || empty($stad)) {
            $errors[] = "All required fields must be filled";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address";
        }
        
        // Validate phone number - max 10 digits and must be numeric
        if (!empty($telefoonnummer)) {
            $cleanedPhone = preg_replace('/[^0-9]/', '', $telefoonnummer);
            if (strlen($cleanedPhone) > 10) {
                $errors[] = "Phone number cannot exceed 10 digits";
            }
            if (!is_numeric($cleanedPhone) || strlen($cleanedPhone) == 0) {
                $errors[] = "Phone number must contain only numbers";
            }
        }
        
        // Validate first name and last name - must not be purely numeric
        if (!empty($voornaam) && is_numeric($voornaam)) {
            $errors[] = "First name cannot be only numbers";
        }
        if (!empty($achternaam) && is_numeric($achternaam)) {
            $errors[] = "Last name cannot be only numbers";
        }
        
        // Validate street - must not be purely numeric
        if (!empty($straat) && is_numeric($straat)) {
            $errors[] = "Street name cannot be only numbers";
        }
        
        // Validate house number - must contain numbers (can have letters like 23a)
        if (!empty($huisnummer) && !preg_match('/[0-9]+/', $huisnummer)) {
            $errors[] = "House number must contain at least one number";
        }
        
        // Validate postal code - must not be empty
        if (empty($postcode)) {
            $errors[] = "Postal code is required";
        }
        
        // Validate city - must not be purely numeric
        if (!empty($stad) && is_numeric($stad)) {
            $errors[] = "City name cannot be only numbers";
        }

        if (empty($errors)) {
            $guestEmail = $email;
            
            // Insert address (for both logged-in users and guests)
            if ($isLoggedIn) {
                // Logged-in user address
                $sql = "INSERT INTO adressen (gebruiker_id, straat, huisnummer, postcode, stad, land, is_guest) 
                        VALUES (?, ?, ?, ?, ?, ?, 0)";
                $stmt = $PDO->prepare($sql);
                $stmt->execute([$userId, $straat, $huisnummer, $postcode, $stad, $land]);
            } else {
                // Guest address with guest user ID (3) and guest-specific fields
                $sql = "INSERT INTO adressen (gebruiker_id, straat, huisnummer, postcode, stad, land, is_guest, guest_email, guest_voornaam, guest_achternaam, guest_telefoonnummer) 
                        VALUES (3, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)";
                $stmt = $PDO->prepare($sql);
                $stmt->execute([$straat, $huisnummer, $postcode, $stad, $land, $email, $voornaam, $achternaam, $telefoonnummer]);
            }
            $addressId = $PDO->lastInsertId();
        }
    }

    if (empty($errors)) {
        // Get payment method and special instructions
        $betaalmethode = $_POST['betaalmethode'] ?? 'creditcard';
        $opmerkingen = trim($_POST['opmerkingen'] ?? '');
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
        // Note: gebruiker_id is 3 for guests (special guest_user account)
        $sql = "INSERT INTO bestellingen (gebruiker_id, guest_email, adres_id, totaal_prijs, status, betaalmethode, opmerkingen, aangemaakt_op, bijgewerkt_op)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $PDO->prepare($sql);
        
        // Use 3 for guest orders (guest_user account ID)
        $orderUserId = $isLoggedIn ? $userId : 3;
        $orderGuestEmail = !$isLoggedIn ? $guestEmail : null;
        $stmt->execute([$orderUserId, $orderGuestEmail, $addressId, $calculatedTotal, 'pending', $betaalmethode, $opmerkingen]);
        
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
