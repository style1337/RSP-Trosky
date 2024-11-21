<?php
    session_start();
    require("connect.php");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Časopis</title>
    <link rel="stylesheet" href="../design.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="left-nav">
                <ul>
                    <li><a href="contact.php">Kontakt</a></li>
                    <li><a href="aboutus.php">O nás</a></li>
                    <li><a href="articles.php">Články</a></li>
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
                        <?php

							if (isset($_SESSION['username'])) {
    					        // User is logged in
   			 			        //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                                echo "<li><a href=\"logout.php\">Logout</a></li>";
							}
                            
                            else {
   						        // User is not logged in
    					        echo "<li><a href=\"subpages/login.php\">Login</a></li>";
							}
						?>
                </ul>
            </div>
        </div>
    </header>

    <section class="main-content">
        <article class="featured">
            <h2>Nadpis hlavního článku</h2>
            <p>Tento článek obsahuje nejdůležitější informace týdne. Zde najdete podrobné analýzy a zajímavé rozhovory.</p>
        </article>

        <section class="articles">
            <article>
                <h3>Nadpis článku 1</h3>
                <p>Krátký popis článku 1. Klikněte zde pro více informací.</p>
            </article>

            <article>
                <h3>Nadpis článku 2</h3>
                <p>Krátký popis článku 2. Klikněte zde pro více informací.</p>
            </article>

            <article>
                <h3>Nadpis článku 3</h3>
                <p>Krátký popis článku 3. Klikněte zde pro více informací.</p>
            </article>
        </section>
    </section>

    <footer>
        <p>&copy; 2024 Online Časopis. Všechna práva vyhrazena.</p>
    </footer>
</body>
</html>
