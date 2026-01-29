<?php require_once 'login_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/auth.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <!--NAVBAR (same as login.html but paths adjusted) -->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../pages/homepage/index.php">Yume Ramen</a>
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
                            <a class="nav-link" href="../pages/basket/basket.php">Basket</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!--LOGIN FORM with same structure/classes/ids as login.html -->
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Login</h2>

                            <?php if (!empty($errors)) { ?>
                                <?php foreach ($errors as $error) { ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php } ?>
                            <?php } ?>

                            <form action="login.php" method="post">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Username</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="username"
                                        name="gebruikersnaam"
                                        placeholder="Enter your username"
                                        value="<?php echo htmlspecialchars($formData['gebruikersnaam'] ?? ''); ?>"
                                        required
                                    >
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="wachtwoord"
                                        placeholder="Enter your password"
                                        required
                                    >
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-salmon" name="login_gebruiker">Login</button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="./register.php" class="text-decoration-none create-account-link">New? Create an account</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>