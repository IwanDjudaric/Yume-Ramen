<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yume Ramen - Checkout</title>
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../style/main.css">
    <link rel="stylesheet" href="../../style/navbar.css">
    <link rel="stylesheet" href="../../style/basket.css">
</head>
<body>
    <div class="yumeramenmaindiv">
        <!--NAVBAR-->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../homepage/index.php">Yume Ramen</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../basket/basket.php">Back to Basket</a>
                        </li>
                        <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../../account/logout.php">Logout</a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../../account/login.php">Login</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!--CHECKOUT PAGE-->
        <div class="basket-container checkout-container">
            <h1 class="basket-page-title">Checkout</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger mt-4">
                    <p><?= htmlspecialchars($_SESSION['error']) ?></p>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success mt-4">
                    <p><?= htmlspecialchars($_SESSION['success']) ?></p>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mt-4">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <?php if ($isLoggedIn): ?>
                    <strong>Welcome back!</strong> Your saved address information has been loaded for faster checkout.
                <?php else: ?>
                    <strong>Guest checkout.</strong> You can <a href="../../account/login.php">login to your account</a> or <a href="../../account/register.php">create an account</a> for faster checkout with saved addresses.
                <?php endif; ?>
            </div>

            <div class="row mt-2 g-3">
                <!-- Order Summary -->
                <div class="col-md-6">
                    <h5 class="mb-2">Order Summary</h5>
                    <div class="card">
                        <div class="card-body p-2">
                            <?php foreach ($basketItems as $item): ?>
                                <div class="d-flex justify-content-between" style="font-size: 0.9rem;">
                                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                                    <span>€<?= number_format($item['total'], 2, ',', '.') ?></span>
                                </div>
                            <?php endforeach; ?>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between" style="font-size: 0.9rem;">
                                <span>Subtotal:</span>
                                <span>€<?= number_format($totalPrice, 2, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size: 0.9rem;">
                                <span>Delivery:</span>
                                <span>€<?= number_format($deliveryPrice, 2, ',', '.') ?></span>
                            </div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between" style="font-size: 1.1rem; font-weight: bold;">
                                <span>Total:</span>
                                <span>€<?= number_format($grandTotal, 2, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information Form -->
                <div class="col-md-6">
                    <h5 class="mb-2">Delivery & Contact Information</h5>
                    <form method="POST" action="checkout_process.php" novalidate>
                        <?php if ($isLoggedIn && !empty($savedAddresses)): ?>
                            <div class="mb-2">
                                <label for="useDefault" class="form-label mb-0 small">
                                    <input type="radio" name="address_option" value="default" id="useDefault" checked>
                                    Use default address
                                </label>
                            </div>
                            <div id="defaultAddressDisplay" class="alert alert-info mb-2 p-2" style="font-size: 0.9rem;">
                                <strong><?= htmlspecialchars($savedAddresses[0]['straat'] ?? '') ?> <?= htmlspecialchars($savedAddresses[0]['huisnummer'] ?? '') ?></strong><br>
                                <?= htmlspecialchars($savedAddresses[0]['postcode'] ?? '') ?> <?= htmlspecialchars($savedAddresses[0]['stad'] ?? '') ?>
                            </div>
                            <div class="mb-2">
                                <label for="useNew" class="form-label mb-0 small">
                                    <input type="radio" name="address_option" value="new" id="useNew">
                                    Enter a new address
                                </label>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="address_option" value="new">
                        <?php endif; ?>

                        <div id="addressForm" class="<?php if ($isLoggedIn && !empty($savedAddresses)) echo 'form-hidden'; ?>">
                            <!-- Contact Information -->
                            <h6 class="mb-2">Contact Information</h6>
                            <div class="mb-2">
                                <label for="voornaam" class="form-label mb-1 small">First Name <span class="text-muted">(optional)</span></label>
                                <input type="text" class="form-control form-control-sm" id="voornaam" name="voornaam" value="<?= htmlspecialchars($formData['voornaam']) ?>" pattern="^[a-zA-Z\s'-]*$" title="First name cannot contain only numbers">
                            </div>

                            <div class="mb-2">
                                <label for="achternaam" class="form-label mb-1 small">Last Name <span class="text-muted">(optional)</span></label>
                                <input type="text" class="form-control form-control-sm" id="achternaam" name="achternaam" value="<?= htmlspecialchars($formData['achternaam']) ?>" pattern="^[a-zA-Z\s'-]*$" title="Last name cannot contain only numbers">
                                <small class="text-danger d-block">At least first name or last name is required</small>
                            </div>

                            <div class="mb-2">
                                <label for="email" class="form-label mb-1 small">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" data-required="true" <?php if (!$isLoggedIn || empty($savedAddresses)) echo 'required'; ?>>
                            </div>

                            <div class="mb-2">
                                <label for="telefoonnummer" class="form-label mb-1 small">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control form-control-sm" id="telefoonnummer" name="telefoonnummer" value="<?= htmlspecialchars($formData['telefoonnummer']) ?>" pattern="[0-9]{1,10}" title="Phone number must be up to 10 digits" data-required="true" <?php if (!$isLoggedIn || empty($savedAddresses)) echo 'required'; ?>>
                                <small class="text-muted d-block">Max 10 digits</small>
                            </div>

                            <!-- Delivery Address -->
                            <h6 class="mt-2 mb-2">Delivery Address</h6>
                            <div class="mb-2">
                                <label for="straat" class="form-label mb-1 small">Street <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="straat" name="straat" value="<?= htmlspecialchars($formData['straat']) ?>" pattern="^[a-zA-Z0-9\s.,'-]+$" title="Street name cannot be only numbers" data-required="true" <?php if (!$isLoggedIn || empty($savedAddresses)) echo 'required'; ?>>
                            </div>

                            <div class="mb-2">
                                <label for="huisnummer" class="form-label mb-1 small">House Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="huisnummer" name="huisnummer" value="<?= htmlspecialchars($formData['huisnummer']) ?>" pattern="[0-9a-zA-Z/\-]+" title="House number must contain numbers (e.g., 23, 23a, 23-25)" data-required="true" <?php if (!$isLoggedIn || empty($savedAddresses)) echo 'required'; ?>>
                                <small class="text-muted d-block">e.g., 23, 23a, 23-25</small>
                            </div>

                            <div class="mb-2">
                                <label for="postcode" class="form-label mb-1 small">Postcode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="postcode" name="postcode" value="<?= htmlspecialchars($formData['postcode']) ?>" data-required="true" <?php if (!$isLoggedIn || empty($savedAddresses)) echo 'required'; ?>>
                            </div>

                            <div class="mb-2">
                                <label for="stad" class="form-label mb-1 small">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="stad" name="stad" value="<?= htmlspecialchars($formData['stad']) ?>" pattern="^[a-zA-Z\s'-]+$" title="City name cannot be only numbers" data-required="true" <?php if (!$isLoggedIn || empty($savedAddresses)) echo 'required'; ?>>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label for="opmerkingen" class="form-label mb-1 small">Special Instructions</label>
                            <textarea class="form-control form-control-sm" id="opmerkingen" name="opmerkingen" rows="2" placeholder="Allergies, dietary requirements..."></textarea>
                        </div>

                        <h6 class="mt-2 mb-2">Payment Method</h6>
                        <div class="mb-2">
                            <select class="form-control form-control-sm" name="betaalmethode" required>
                                <option value="creditcard">Credit Card</option>
                                <option value="debitcard">Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="banktransfer">Bank Transfer</option>
                            </select>
                        </div>
                        <input type="hidden" name="total_price" value="<?= $grandTotal ?>">

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            Complete Order - €<?= number_format($grandTotal, 2, ',', '.') ?>
                        </button>

                        <a href="../basket/basket.php" class="btn btn-secondary w-100 mt-1 py-2">Back to Basket</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($isLoggedIn && !empty($savedAddresses)): ?>
        const addressOptions = document.querySelectorAll('input[name="address_option"]');
        const addressForm = document.getElementById('addressForm');

        addressOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.value === 'new') {
                    addressForm.style.display = 'block';
                    // Mark form fields as required
                    document.querySelectorAll('#addressForm input[data-required], #addressForm textarea[data-required]').forEach(input => {
                        input.required = true;
                    });
                } else {
                    addressForm.style.display = 'none';
                    // Mark form fields as not required
                    document.querySelectorAll('#addressForm input[data-required], #addressForm textarea[data-required]').forEach(input => {
                        input.required = false;
                    });
                }
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>
