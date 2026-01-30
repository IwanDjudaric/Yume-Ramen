<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? htmlspecialchars($product['naam']) : 'Product' ?> - Yume Ramen</title>
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../style/navbar.css">
    <link rel="stylesheet" href="../../style/detail.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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
                <a class="navbar-brand" href="../homepage/index.php">Yume Ramen</a>
                <div class="d-flex align-items-center">
                    <a href="../basket/basket.php" class="btn btn-dark btn-sm me-2 position-relative" aria-label="Basket">
                        <i class="bi bi-basket"></i>
                        <?php 
                        $basketCount = 0;
                        if (isset($_SESSION['basket'])) {
                            $basketCount = array_sum($_SESSION['basket']);
                        }
                        if ($basketCount > 0): 
                        ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $basketCount ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if ($isLoggedIn): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../../account/logout.php">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../../account/login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../../account/register.php">Sign Up</a>
                            </li>
                        <?php endif; ?>
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
                <img src="../../assets/images/<?= htmlspecialchars($product['afbeelding']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>" class="detail-image">
                
                <div class="detail-content">
                    <h1 class="mb-3"><?= htmlspecialchars($product['naam']) ?></h1>
                    
                    <h4 class="mb-3 text-salmon">$<?= number_format($product['prijs'], 2) ?></h4>
                    
                    <p class="mb-4"><?= nl2br(htmlspecialchars($product['beschrijving'])) ?></p>
                    
                    <div class="d-flex gap-2">
                        <form method="POST" action="../basket/basket_operations.php" onsubmit="addToBasket(event)">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="ajax" value="1">
                            <button type="submit" class="btn btn-salmon btn-lg">Add to Basket</button>
                        </form>
                        <a href="scroll.php" class="btn btn-outline-secondary btn-lg">Back to Products</a>
                    </div>
                    <div id="notification" class="alert alert-success mt-3" role="alert" style="display: none;">
                        ✓ Product added to basket!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function addToBasket(event) {
            event.preventDefault();
            const form = event.target;
            
            try {
                const response = await fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: new FormData(form)
                });
                
                const data = await response.json();
                
                // Update basket counter
                updateBasketCounter(data.basketCount);
                
                // Show notification
                const notification = document.getElementById('notification');
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            } catch (error) {
                console.error('Error adding to basket:', error);
            }
        }
        
        function updateBasketCounter(count) {
            const basketBtn = document.querySelector('a[aria-label="Basket"]');
            let badge = basketBtn.querySelector('.badge');
            
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    basketBtn.appendChild(badge);
                }
                badge.textContent = count;
            } else if (badge) {
                badge.remove();
            }
        }
    </script>
</body>
</html>
