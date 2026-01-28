<?php require_once 'login_logic.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <title>Login</title>
    <link href="style/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <h1>Login</h1>
    
    <?php if (!empty($errors)) { ?>
        <?php foreach ($errors as $error) { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php } ?>
    <?php } ?>
    <form action="login.php" method="post">
        <div class="input-group">
            <input type="text" name="gebruikersnaam" required placeholder="Gebruikersnaam">
        </div>
        <div class="input-group">
            <input type="password" name="wachtwoord" required placeholder="Wachtwoord">
        </div>
        <div class="input-group">
            <button class="btn" type="submit" name="login_gebruiker">Login</button>
        </div>
    <p>
        Nog geen gebruiker?<a href="./register.php"><br>Account aanmaken</a>
    </p>
    </form>
</div>
</body>
</html>