<?php
session_start();
require_once 'config/config.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen - Checkout</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/basket.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <!--NAVBAR-->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.html">Yume Ramen</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="basket.php">Back to Basket</a>
                        </li>
                        <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="account/logout.php">Logout</a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="account/login.php">Login</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!--CHECKOUT PAGE-->
        <div class="basket-container checkout-container">
            <h1 class="basket-page-title">Checkout</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mt-4">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <?php if ($isLoggedIn): ?>
                    <strong>Logged in as guest checkout.</strong> You can also <a href="account/login.php">login to your account</a> for faster checkout with saved addresses.
                <?php else: ?>
                    <strong>Guest checkout.</strong> You can <a href="account/register.php">create an account</a> after placing your order or proceed as guest.
                <?php endif; ?>
            </div>

            <div class="row mt-4">
                <!-- Order Summary -->
                <div class="col-md-6">
                    <h3>Order Summary</h3>
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($basketItems as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                                    <span>€<?= number_format($item['total'], 2, ',', '.') ?></span>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>€<?= number_format($totalPrice, 2, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery:</span>
                                <span>€<?= number_format($deliveryPrice, 2, ',', '.') ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between total-row-large">
                                <span>Total:</span>
                                <span>€<?= number_format($grandTotal, 2, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information Form -->
                <div class="col-md-6">
                    <h3>Delivery & Contact Information</h3>
                    <form method="POST" action="checkout_process.php" novalidate>
                        <?php if ($isLoggedIn && !empty($savedAddresses)): ?>
                            <div class="mb-3">
                                <label for="useDefault" class="form-label">
                                    <input type="radio" name="address_option" value="default" id="useDefault" checked>
                                    Use default address
                                </label>
                            </div>
                            <div id="defaultAddressDisplay" class="alert alert-info mb-3">
                                <strong><?= htmlspecialchars($savedAddresses[0]['straat'] ?? '') ?> <?= htmlspecialchars($savedAddresses[0]['huisnummer'] ?? '') ?></strong><br>
                                <?= htmlspecialchars($savedAddresses[0]['postcode'] ?? '') ?> <?= htmlspecialchars($savedAddresses[0]['stad'] ?? '') ?><br>
                                <?= htmlspecialchars($savedAddresses[0]['land'] ?? '') ?>
                            </div>
                            <div class="mb-3">
                                <label for="useNew" class="form-label">
                                    <input type="radio" name="address_option" value="new" id="useNew">
                                    Enter a new address
                                </label>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="address_option" value="new">
                        <?php endif; ?>

                        <div id="addressForm" class="<?php if ($isLoggedIn && !empty($savedAddresses)) echo 'form-hidden'; ?>">
                            <!-- Contact Information -->
                            <h5>Contact Information</h5>
                            <div class="mb-3">
                                <label for="voornaam" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="voornaam" name="voornaam" value="<?= htmlspecialchars($formData['voornaam']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="achternaam" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="achternaam" name="achternaam" value="<?= htmlspecialchars($formData['achternaam']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefoonnummer" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="telefoonnummer" name="telefoonnummer" value="<?= htmlspecialchars($formData['telefoonnummer']) ?>" required>
                            </div>

                            <!-- Delivery Address -->
                            <h5 class="mt-4">Delivery Address</h5>
                            <div class="mb-3">
                                <label for="straat" class="form-label">Street</label>
                                <input type="text" class="form-control" id="straat" name="straat" value="<?= htmlspecialchars($formData['straat']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="huisnummer" class="form-label">House Number</label>
                                <input type="text" class="form-control" id="huisnummer" name="huisnummer" value="<?= htmlspecialchars($formData['huisnummer']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="postcode" class="form-label">Postcode</label>
                                <input type="text" class="form-control" id="postcode" name="postcode" value="<?= htmlspecialchars($formData['postcode']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="stad" class="form-label">City</label>
                                <input type="text" class="form-control" id="stad" name="stad" value="<?= htmlspecialchars($formData['stad']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="land" class="form-label">Country</label>
                                <input type="text" class="form-control" id="land" name="land" value="<?= htmlspecialchars($formData['land']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="opmerkingen" class="form-label">Special Instructions (Allergies, dietary requirements, etc.)</label>
                            <textarea class="form-control" id="opmerkingen" name="opmerkingen" rows="3" placeholder="Please let us know about any allergies or special dietary requirements..."></textarea>
                        </div>

                        <h3 class="mt-4">Payment Method</h3>
                        <div class="mb-3">
                            <select class="form-control" name="betaalmethode" required>
                                <option value="creditcard">Credit Card</option>
                                <option value="debitcard">Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="banktransfer">Bank Transfer</option>
                            </select>
                        </div>
                        <input type=\"hidden\" name=\"total_price\" value=\"<?= $grandTotal ?>\">

                        <button type=\"submit\" class=\"btn btn-primary w-100 py-3 btn-large-text\">
                            Complete Order - €<?= number_format($grandTotal, 2, ',', '.') ?>
                        </button>

                        <a href=\"basket.php\" class=\"btn btn-secondary w-100 mt-2\">Back to Basket</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($isLoggedIn && !empty($savedAddresses)): ?>
        const addressOptions = document.querySelectorAll('input[name="address_option"]');
        const addressForm = document.getElementById('addressForm');

        addressOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.value === 'new') {
                    addressForm.style.display = 'block';
                    // Mark form fields as required
                    document.querySelectorAll('#addressForm input').forEach(input => {
                        input.required = true;
                    });
                } else {
                    addressForm.style.display = 'none';
                    // Mark form fields as not required
                    document.querySelectorAll('#addressForm input').forEach(input => {
                        input.required = false;
                    });
                }
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>
