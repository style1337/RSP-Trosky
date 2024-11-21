<?php
	session_start();
    require("connect.php");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prihlášení</title>
    <link rel="stylesheet" href="../design.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="left-nav">
                <ul>
                    <li><a href="./contact.php">Kontakt</a></li>
                    <li><a href="./aboutus.php">O nás</a></li>
                    <li><a href="./articles.php">Články</a></li>
                    <li><a href="../trosky.php">Hlavní strana</a></li>
                </ul>
            </div>
            <div class="logo">
                <a href="../trosky.php">
                    <img src="../images/logo.png" alt="Logo">
                </a>
            </div>
            <div class="right-nav">
                <ul>
                    
                </ul>
            </div>
        </div>
    </header>

    <!--Dodělat design okénka pro informaci o statusu registrace a přihlášení-->
<div id="error-message">
            <?php
            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            }
            ?>
        </div>
        <div id="success-message">
            <?php
            if (isset($_SESSION['success'])) {
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            }
            ?>
</div>
    <section class="main-content">
        <div class="login-container">
            <h2>Prihlášení</h2>
            <form action="submit_login.php" method="POST">
                <div class="form-group">
                    <label for="username">Uživatelské jméno</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Přihlásit se</button>
                </div>
            </form>
            <div class="form-options">
                <a href="register.php">Nemáte účet? Zaregistrujte se</a>
                <a href="articles.php" class="guest-link">Pokracovat jako čtenář</a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Troskopis. Všechna práva vyhrazena.</p>
    </footer>
</body>
</html>
