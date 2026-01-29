<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen - Products</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/scroll.css">
    <style>
        .items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product-card-large {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .product-card-large:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .carousel-img-large {
            height: 200px;
            object-fit: cover;
        }
        .add-to-basket-btn {
            width: 100%;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    
    <div class="yumeramenmaindiv">
        <!--NAVBAR-->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Yume Ramen</a>
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

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="items">
            <?php if (empty($producten)): ?>
                <div class="col-12">
                    <p class="text-center">No products available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($producten as $product): ?>
                    <div class="product-card-large">
                        <img src="assets/images/<?= htmlspecialchars($product['afbeelding']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>" class="d-block w-100 carousel-img-large">
                        <div class="product-info p-3">
                            <h6 class="mb-1"><?= htmlspecialchars($product['naam']) ?></h6>
                            <p class="text-muted small mb-2"><?= htmlspecialchars(substr($product['beschrijving'], 0, 50)) ?>...</p>
                            <p class="mb-2 fw-bold">$<?= number_format($product['prijs'], 2) ?></p>
                            <form method="POST" action="basket_operations.php" onsubmit="submitToBasket(event)" class="form-inline-block">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-salmon add-to-basket-btn">Add to Basket</button>
                            </form>
                            <div id="notification-<?= $product['id'] ?>" class="notification-message alert alert-success py-1 px-2" role="alert">✓ Added!</div>
                            <a href="detail.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-secondary add-to-basket-btn mt-2">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function submitToBasket(event) {
            event.preventDefault();
            const form = event.target;
            const productId = form.querySelector('[name="product_id"]').value;
            fetch(form.action, { method: 'POST', body: new FormData(form) });
            const notification = document.getElementById('notification-' + productId);
            notification.style.display = 'block';
            setTimeout(() => notification.style.display = 'none', 2000);
        }
    </script>
</body>
</html>
