<?php
    session_start();
    require("connect.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>O nás</title>
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
    </body>
</html>