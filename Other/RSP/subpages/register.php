<?php
	session_start();
    require("connect.php");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace</title>
    <link rel="stylesheet" href="../design.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="left-nav">
                <ul>
                    <li><a href="./contact.html">Kontakt</a></li>
                    <li><a href="./aboutus.html">O nás</a></li>
                    <li><a href="./articles.html">Články</a></li>
                    <li><a href="../trosky.html">Hlavní strana</a></li>
                </ul>
            </div>
            <div class="logo">
                <a href="../trosky.html">
                    <img src="../images/logo.png" alt="Logo">
                </a>
            </div>
            <div class="right-nav">
                <ul>
                    
                </ul>
            </div>
        </div>
    </header>

    <section class="main-content">
        <div class="register-container">
            <h2>Registrace</h2>
            <form action="submit_register.php" method="POST">
                <div class="form-group">
                    <label for="username">Uživatelské jméno</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Registrovat se</button>
                </div>
            </form>
            <div class="form-options">
                <a href="./login.html">Už máte účet? Přihlaste se</a>
                <a href="../articles.html" class="guest-link">Pokračovat jako čtenář</a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Troskopis. Všechna práva vyhrazena.</p>
    </footer>
</body>
</html>