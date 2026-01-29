<?php
session_start();
require_once 'config/config.php';

$orderId = $_GET['order_id'] ?? null;
$isLoggedIn = isset($_SESSION['user_id']);

if (!$orderId) {
    $_SESSION['error'] = "Order not found";
    header('Location: index.html');
    exit;
}

$order = null;
$orderItems = [];
$address = null;
$isGuestOrder = false;

try {
    // Get order details - check if user is logged in for guest orders
    if ($isLoggedIn) {
        $sql = "SELECT * FROM bestellingen WHERE id = ? AND gebruiker_id = ?";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$orderId, $_SESSION['user_id']]);
    } else {
        // Guest order - use session or check guest_email
        $sql = "SELECT * FROM bestellingen WHERE id = ? AND gebruiker_id IS NULL";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$orderId]);
        $isGuestOrder = true;
    }
    $order = $stmt->fetch();

    if (!$order) {
        throw new Exception("Order not found or unauthorized");
    }

    // Get address details
    $sql = "SELECT * FROM adressen WHERE id = ?";
    $stmt = $PDO->prepare($sql);
    $stmt->execute([$order['adres_id']]);
    $address = $stmt->fetch();

    // Get order items
    $sql = "SELECT bi.*, p.naam FROM bestellings_items bi 
            JOIN producten p ON bi.product_id = p.id
            WHERE bi.bestelling_id = ?";
    $stmt = $PDO->prepare($sql);
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll();

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Yume Ramen</title>
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
                <a class="navbar-brand" href="index.php">Yume Ramen</a>
            </div>
        </nav>

        <!--ORDER CONFIRMATION PAGE-->
        <div class="basket-container order-confirmation-container">
            <div class="alert alert-success" role="alert">
                <h2>✓ Order Confirmed!</h2>
                <p>Thank you for your order. Your delicious ramen is on the way!</p>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h4>Order #<?= htmlspecialchars($order['id']) ?></h4>
                    <?php if ($isGuestOrder): ?>
                        <small class="text-muted">Guest Order</small>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Order Date:</strong><br>
                            <?= date('d-m-Y H:i', strtotime($order['aangemaakt_op'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-primary"><?= htmlspecialchars(ucfirst($order['status'])) ?></span>
                        </div>
                    </div>
                    <?php if ($isGuestOrder && isset($order['guest_email'])): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Email:</strong><br>
                                <?= htmlspecialchars($order['guest_email']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <h3>Order Items</h3>
            <div class="card mb-4">
                <div class="card-body">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span><?= htmlspecialchars($item['naam']) ?> × <?= $item['aantal'] ?></span>
                            <span>€<?= number_format($item['prijs_per_stuk'] * $item['aantal'], 2, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-3 total-row-medium">
                        <span>Total:</span>
                        <span>€<?= number_format($order['totaal_prijs'], 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <h3>Delivery Address</h3>
            <div class="card mb-4">
                <div class="card-body">
                    <?php if ($isGuestOrder && isset($address['guest_voornaam'])): ?>
                        <strong><?= htmlspecialchars($address['guest_voornaam'] ?? '') ?> <?= htmlspecialchars($address['guest_achternaam'] ?? '') ?></strong><br>
                        <?= htmlspecialchars($address['guest_telefoonnummer'] ?? '') ?><br>
                    <?php endif; ?>
                    <strong><?= htmlspecialchars($address['straat'] ?? '') ?> <?= htmlspecialchars($address['huisnummer'] ?? '') ?></strong><br>
                    <?= htmlspecialchars($address['postcode'] ?? '') ?> <?= htmlspecialchars($address['stad'] ?? '') ?><br>
                    <?= htmlspecialchars($address['land'] ?? '') ?>
                </div>
            </div>

            <h3>Payment Method</h3>
            <div class="card mb-4">
                <div class="card-body">
                    <p><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $order['betaalmethode']))) ?></p>
                </div>
            </div>

            <a href="index.php" class="btn btn-primary w-100 py-3">Continue Shopping</a>
            
            <?php if ($isGuestOrder): ?>
                <div class="alert alert-info mt-4">
                    <p>Thank you for your order! A confirmation email has been sent to <strong><?= htmlspecialchars($order['guest_email'] ?? '') ?></strong>.</p>
                    <p>You can <a href="account/register.php">create an account</a> to track your orders in the future.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
