<?php
    session_start();
    require("connect.php");

    // Zkontrolování, zda je uživatel přihlášen a má příslušnou roli
    if ($_SESSION['role'] != 'admin') {
        // Pokud uživatel není přihlášen nebo nemá příslušnou roli, přesměrování na stránku s chybou
        header("Location: unauthorized.php");
        exit();
    } 
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Panel článků</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <!-- Talčítko pro admin panel se zobrazí pouze u admina -->
                    <?php
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                        echo '<li><a href="./apanel.php">Admin panel</a></li>';
                        }
                    ?>
                        <!-- Tlačítko pro nahrání článků se zobrazí pouze pro autora -->
                        <?php
                            if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                echo '<li><a href="./article_upload.php">Nahrát článek</a></li>';
                            }
                        ?>
                        <!-- Tlačítko pro panel článků se zobrazí pro přihlášené uživatele -->
                        <?php
                            if (isset($_SESSION['username'])) {
                                echo '<li><a href="./article_panel.php">Panel článků</a></li>';
                            }
                        ?>
                        <?php

							if (isset($_SESSION['username'])) {
    					        // User is logged in
   			 			        //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                                echo "<li><a href=\"./logout.php\">Logout</a></li>";
							} 
                            
                            else {
   						        // User is not logged in
    					        echo "<li><a href=\"./login.php\">Login</a></li>";
							}
						?>
                </ul>
            </div>
            <div class="dropdown">
                <button class="dropbtn">&#9776;</button>
                <div class="dropdown-content">
                    <ul>
                    <!-- Talčítko pro admin panel se zobrazí pouze u admina -->
                    <?php
                        if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                            echo '<li><a href="./apanel.php">Admin panel</a></li>';
                        }
                    ?>
                        <?php
                            if (isset($_SESSION['role']) && $_SESSION['role'] == 'author') {
                                echo '<li><a href="./article_upload.php">Nahrát článek</a></li>';
                            }
                        ?>
                        <?php
                            if (isset($_SESSION['username'])) {
                                echo '<li><a href="./article_panel.php">Panel článků</a></li>';
                            }
                        ?>
                        <li><a href="../trosky.php">Hlavní strana</a></li>
                        <li><a href="./articles.php">Články</a></li>
                        <li><a href="./aboutus.php">O nás</a></li>
                        <li><a href="./contact.php">Kontakt</a></li>
                        <?php

							if (isset($_SESSION['username'])) {
    					        // User is logged in
   			 			        //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                                echo "<li><a href=\"./logout.php\">Logout</a></li>";
							} 
                            
                            else {
   						        // User is not logged in
    					        echo "<li><a href=\"./login.php\">Login</a></li>";
							}
						?>
                    </ul>
                </div>
            </div>
        </div>
    </header>

        <div class="article-grid">
        <?php
            $role = $_SESSION['role'];
            $user_id = $_SESSION['user_id'];
            $query = "SELECT * from troskopis_users;";
            $dispusers = mysqli_query($spojeni, $query);

            if ($dispusers) {
                while ($row = mysqli_fetch_assoc($dispusers)) {
                    echo "<div class=\"article-card\">
                    <p>ID: " . $row['user_id'] . "</p>
                    <p>Username: " . $row['username'] . "</p>
                    <p>Password: <a class=\"review-button\">Změnit heslo</a> 
                    <p>E-mail: " . $row['email'] . "</p>
                    <p>Role: " . $row['role'] . "</p>
                    </div> ";
                }
            }
        ?>
        </div>

    </main>
        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>

    </body>
</html>