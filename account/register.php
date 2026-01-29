<?php require_once 'register_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen - Register</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/auth.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <!--NAVBAR (same structure/classes as signup.html, paths adjusted) -->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.php">Yume Ramen</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../register.php">Sign Up</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../basket.php">Basket</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- REGISTER FORM with same structure/classes/ids as signup.html -->
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Sign Up</h2>

                            <?php if (!empty($errors)) { ?>
                                <?php foreach ($errors as $error) { ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php } ?>
                            <?php } ?>

                            <?php if (!empty($success)) { ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php } ?>

                            <form method="post" action="register.php">
                                <div class="mb-4">
                                    <label for="accountname" class="form-label">Account Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="accountname"
                                        name="gebruikersnaam"
                                        placeholder="Enter your account name"
                                        value="<?php echo htmlspecialchars($formData['gebruikersnaam'] ?? ''); ?>"
                                        required
                                    >
                                </div>
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email"
                                        value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
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
                                <div class="mb-4">
                                    <label for="passwordrepeat" class="form-label">Repeat Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="passwordrepeat"
                                        name="wachtwoordrepeat"
                                        placeholder="Repeat your password"
                                        required
                                    >
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-salmon" name="reg_user">Sign Up</button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="./login.php" class="text-decoration-none create-account-link">Already have an account? Login</a>
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
