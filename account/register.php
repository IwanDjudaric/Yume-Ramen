<?php require_once 'register_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link href="style/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="header">
    <h2>Register</h2>
</div>

<?php if (!empty($errors)) { ?>
    <?php foreach ($errors as $error) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php } ?>
<?php } ?>

<?php if (!empty($success)) { ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php } ?>

<form method="post" action="register.php">
    <div class="input-group">
        <label>Gebruikersnaam</label>
        <input type="text" name="gebruikersnaam" value="<?=$formData['gebruikersnaam'] ?>" required>
    </div>
    <div class="input-group">
        <label>E-mail</label>
        <input type="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
    </div>
    <div class="input-group">
        <label>Wachtwoord</label>
        <input type="password" name="wachtwoord" required placeholder="minimum 6 karakters">
    </div>
    <div class="input-group">
        <label>Herhaal Wachtwoord</label>
        <input type="password" name="wachtwoordrepeat" required>
    </div>
    <div class="input-group">
        <button class="btn" type="submit" name="reg_user">Register</button>
    </div>

    <p>
        Al een gebruiker? <a href="./login.php">Inloggen</a>
    </p>
</form>
</body>
</html>
