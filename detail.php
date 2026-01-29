<?php
session_start();
require_once 'config/config.php';

$productId = $_GET['id'] ?? null;
$product = null;
$error = '';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? htmlspecialchars($product['naam']) : 'Product' ?> - Yume Ramen</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/detail.css">
    <style>
        .detail-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .detail-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .detail-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
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
                            <a class="nav-link" href="basket.php">Basket</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="detail-container">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
                <a href="scroll.php" class="btn btn-primary">Back to Products</a>
            <?php elseif ($product): ?>
                <img src="assets/images/<?= htmlspecialchars($product['afbeelding']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>" class="detail-image">
                
                <div class="detail-content">
                    <h1 class="mb-3"><?= htmlspecialchars($product['naam']) ?></h1>
                    
                    <h4 class="mb-3 text-salmon">$<?= number_format($product['prijs'], 2) ?></h4>
                    
                    <p class="mb-4"><?= nl2br(htmlspecialchars($product['beschrijving'])) ?></p>
                    
                    <div class="d-flex gap-2">
                        <form method="POST" action="basket_operations.php">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-salmon btn-lg">Add to Basket</button>
                        </form>
                        <a href="scroll.php" class="btn btn-outline-secondary btn-lg">Back to Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
