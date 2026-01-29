<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen</title>
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../style/main.css">
    <link rel="stylesheet" href="../../style/navbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <!--NAVBAR-->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../../index.php">Yume Ramen</a>
                <div class="d-flex align-items-center">
                    <a href="../basket/basket.php" class="btn btn-dark btn-sm me-2" aria-label="Basket">
                        <i class="bi bi-basket"></i>
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
        
        <!--HERO HEADER-->
        <div class="hero-header position-relative">
            <img src="../../assets/images/Retro Ramen Poster _ Ramen Poster _ Vintage Poster _ Retro Poster _ Japans Eten Poster _ Klassiek Poster _ Eetgelegenheid Poster _ 60x90cm _ Wandde_.jpg" alt="Hero Image" class="w-100">
            <div class="position-absolute top-50 start-50 translate-middle">
                <a href="../products/scroll.php" class="btn btn-salmon btn-lg">Order Now</a>
            </div>
        </div>
        
        <!--NEWEST ADDITIONS CAROUSEL-->
        <div class="p-4">
            <h2 class="text-center mb-4">Latest Additions</h2>
            <?php if (!empty($producten)): ?>
            <div id="productsCarousel" class="carousel slide carousel-container" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($producten as $index => $product): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <a href="../products/detail.php?id=<?= $product['id'] ?>" class="product-card product-card-block">
                            <img src="../../assets/images/<?= htmlspecialchars($product['afbeelding']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>" class="d-block w-100 carousel-img">
                            <div class="product-info p-3 text-center">
                                <h5 class="mb-1"><?= htmlspecialchars($product['naam']) ?></h5>
                                <p class="text-muted small mb-2"><?= htmlspecialchars(substr($product['beschrijving'], 0, 60)) ?>...</p>
                                <p class="mb-2"><strong>€<?= number_format($product['prijs'], 2, ',', '.') ?></strong></p>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="carousel-controls d-flex justify-content-center gap-2 mt-3">
                <button class="btn btn-sm btn-outline-dark" type="button" data-bs-target="#productsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="btn btn-sm btn-outline-dark" type="button" data-bs-target="#productsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            <?php else: ?>
                <p class="text-center">No products available yet.</p>
            <?php endif; ?>
        </div>
        
    </div>
    <script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
