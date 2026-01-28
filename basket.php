<?php
session_start();
require_once 'config/config.php';

// Initialize basket in session if not exists
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

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
                'quantity' => $quantity,
                'total' => $itemTotal
            ];
            $totalPrice += $itemTotal;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$grandTotal = $totalPrice + $deliveryPrice;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen - Basket</title>
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
                            <a class="nav-link" href="./account/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./account/register.php">Sign Up</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="basket.php">Basket</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!--BASKET PAGE-->
        <div class="basket-container">
            <h1 class="basket-page-title">Your Basket</h1>

            <?php if (empty($basketItems)): ?>
                <div class="alert alert-info mt-4">
                    <p>Your basket is empty. <a href="scroll.html">Continue shopping</a></p>
                </div>
            <?php else: ?>
                <!-- Basket Items -->
                <?php foreach ($basketItems as $item): ?>
                    <div class="basket-item">
                        <img src="assets/images/placeholder.jpg" alt="<?= htmlspecialchars($item['name']) ?>" class="basket-item-image">
                        <div class="basket-item-details">
                            <div class="basket-item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="basket-item-description"><?= htmlspecialchars($item['description']) ?></div>
                            <div class="basket-item-amount">
                                <form method="POST" action="basket_operations.php" style="display: flex; gap: 10px; align-items: center;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>" class="quantity-btn" <?php if ($item['quantity'] <= 1) echo 'disabled'; ?>>−</button>
                                    <span class="quantity-display"><?= $item['quantity'] ?></span>
                                    <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" class="quantity-btn">+</button>
                                </form>
                            </div>
                            <div class="basket-item-price">$<?= number_format($item['price'], 2) ?></div>
                            <div class="basket-item-subtotal">Subtotal: $<?= number_format($item['total'], 2) ?></div>
                            <form method="POST" action="basket_operations.php" style="display: inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger mt-2">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Divider -->
                <div class="basket-divider"></div>

                <!-- Order Summary -->
                <div class="basket-summary">
                    <div class="summary-row">
                        <span class="summary-label">Order Price:</span>
                        <span class="summary-value">$<?= number_format($totalPrice, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Delivery Price:</span>
                        <span class="summary-value">$<?= number_format($deliveryPrice, 2) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span class="summary-label">Total Price:</span>
                        <span class="summary-value">$<?= number_format($grandTotal, 2) ?></span>
                    </div>
                </div>

                <!-- Order Button -->
                <form method="POST" action="basket_operations.php" class="mt-3">
                    <input type="hidden" name="action" value="checkout">
                    <button type="submit" class="order-button">Place Order</button>
                    <a href="scroll.html" class="btn btn-secondary ms-2">Continue Shopping</a>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
