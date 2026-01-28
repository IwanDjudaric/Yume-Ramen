<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producten - Yume Ramen</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/auth.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.html">Yume Ramen</a>
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
                            <a class="nav-link" href="../basket.html">Basket</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <h1 class="mb-4">Producten</h1>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <?php if (count($producten) > 0) { ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Naam</th>
                                <th>Beschrijving</th>
                                <th>Prijs</th>
                                <th>Categorie</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($producten as $product) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td><?php echo htmlspecialchars($product['naam']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($product['beschrijving'], 0, 50) . (strlen($product['beschrijving']) > 50 ? '...' : '')); ?></td>
                                    <td>$<?php echo htmlspecialchars($product['prijs']); ?></td>
                                    <td><?php echo htmlspecialchars($product['categorie_naam'] ?? '-'); ?></td>
                                    <td>
                                        <a href="./update.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="./delete.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Zeker?')">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <p>Geen producten gevonden.</p>
            <?php } ?>

            <div class="mt-3">
                <a href="./create.php" class="btn btn-salmon">Nieuw Product</a>
            </div>
        </div>
    </div>

    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
