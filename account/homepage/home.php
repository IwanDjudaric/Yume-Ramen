    <?php
session_start();

// Check if user is logged in (optional)
$isLoggedIn = isset($_SESSION['gebruikersnaam']) || isset($_SESSION['is_admin']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
$username = $isLoggedIn ? ($isAdmin ? $_SESSION['username'] : $_SESSION['gebruikersnaam']) : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen - Dashboard</title>
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../style/main.css">
    <link rel="stylesheet" href="../../style/navbar.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <!--NAVBAR-->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../../index.html">Yume Ramen</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../../scroll.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../basket.php">Basket</a>
                        </li>
                        <?php if ($isAdmin): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../read.php">Manage</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($isLoggedIn): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../logout.php">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../login.php">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!--CONTENT-->
        <div class="container mt-5">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
                            <p class="card-text">
                                <?php if ($isLoggedIn): ?>
                                    <?php if ($isAdmin): ?>
                                        You are logged in as an administrator.
                                    <?php else: ?>
                                        You are logged in successfully. You can now browse and order our delicious ramen!
                                    <?php endif; ?>
                                <?php else: ?>
                                    Welcome to Yume Ramen! Browse our delicious ramen collection and order your favorite dishes.
                                <?php endif; ?>
                            </p>
                            <div class="mt-4">
                                <a href="../../scroll.php" class="btn btn-salmon me-2">Browse Products</a>
                                <a href="../../basket.php" class="btn btn-outline-salmon">View Basket</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
