<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Bewerken - Yume Ramen</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/auth.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.php">Yume Ramen</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="./login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./register.php">Sign Up</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../basket.php">Basket</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Product Bewerken</h2>

                            <?php if (!empty($error)) { ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php } ?>

                            <?php if (!empty($success)) { ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php } ?>

                            <?php if ($product) { ?>
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="naam" class="form-label">Naam</label>
                                        <input type="text" class="form-control" id="naam" name="naam" value="<?php echo htmlspecialchars($product['naam']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="beschrijving" class="form-label">Beschrijving</label>
                                        <textarea class="form-control" id="beschrijving" name="beschrijving" rows="3" required><?php echo htmlspecialchars($product['beschrijving']); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="prijs" class="form-label">Prijs</label>
                                        <input type="number" step="0.01" class="form-control" id="prijs" name="prijs" value="<?php echo htmlspecialchars($product['prijs']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="categorie_id" class="form-label">Categorie</label>
                                        <select class="form-control" id="categorie_id" name="categorie_id" required>
                                            <option value="">Kies een categorie</option>
                                            <?php foreach ($categories as $cat) { ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php echo $product['categorie_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['naam']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-salmon">Bijwerken</button>
                                    </div>
                                </form>

                                <div class="mt-3">
                                    <a href="./read.php" class="btn btn-secondary w-100">Terug naar Producten</a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
