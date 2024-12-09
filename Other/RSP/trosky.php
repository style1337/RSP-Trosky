<?php
	session_start();
  require("subpages/connect.php");
?>

<!DOCTYPE html>
<html lang="cs">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troskopis</title>
    <link rel="stylesheet" href="design.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="left-nav">
            <ul>
                <li><a href="./trosky.php"><i class="fas fa-home"></i> Hlavní strana</a></li>
                <li><a href="./subpages/articles.php">Články</a></li>
                <li><a href="./subpages/aboutus.php">O nás</a></li>
                <li><a href="./subpages/contact.php">Kontakt</a></li>
            </ul>
            </div>
            <div class="logo">
                <a href="./trosky.php">
                <img src="./images/logo.png" alt="Logo">
            </a>
            </div>
            <div class="right-nav">
                <ul>
                    <!-- Talčítko pro admin panel se zobrazí pouze u admina -->
                    <?php
                        if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                            echo '<li><a href="subpages/apanel.php"><i class="fas fa-tools"></i> Admin panel</a></li>';
                        }
                    ?>
                        <!-- Tlačítko pro nahrání článků se zobrazí pouze pro autora -->
                        <?php
                            if ((isset($_SESSION['role']) && $_SESSION['role'] == 'author') || (isset($_SESSION['role']) && $_SESSION['role'] == 'admin')) {
                                echo '<li><a href="subpages/article_upload.php"><i class="fas fa-upload"></i> Nahrát článek</a></li>';
                            }
                        ?>
                    <!-- Tlačítko pro panel článků se zobrazí pro přihlášené uživatele -->
                    <?php
                        if (isset($_SESSION['username'])) {
                            echo '<li><a href="subpages/article_panel.php"><i class="fas fa-newspaper"></i> Panel článků</a></li>';
                        }
                    ?>
                    <?php

                        if (isset($_SESSION['username'])) {
                            // User is logged in
                            //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                            echo "<li><a href=\"subpages/logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
                        } 
                        
                        else {
                            // User is not logged in
                            echo "<li><a href=\"subpages/login.php\"><i class=\"fas fa-sign-in-alt\"></i> Login</a></li>";
                        }
                    ?>

                </ul>
            </div>
            <div class="dropdown">
                <button class="dropbtn"><i class="fas fa-bars"></i></button>
                <div class="dropdown-content">
                    <ul>
                        <!-- Tlačítko pro nahrání článků se zobrazí pouze pro autora -->
                        <?php
                            if ((isset($_SESSION['role']) && $_SESSION['role'] == 'author') || (isset($_SESSION['role']) && $_SESSION['role'] == 'admin')) {
                                echo '<li><a href="subpages/article_upload.php"><i class="fas fa-upload"></i> Nahrát článek</a></li>';
                            }
                        ?>
                        <?php
                            if (isset($_SESSION['username'])) {
                                echo '<li><a href="subpages/article_panel.php"><i class="fas fa-newspaper"></i> Panel článků</a></li>';
                            }
                        ?>
                        <li><a href="./trosky.php"><i class="fas fa-home"></i> Hlavní strana</a></li>
                        <li><a href="./subpages/articles.php">Články</a></li>
                        <li><a href="./subpages/aboutus.php">O nás</a></li>
                        <li><a href="./subpages/contact.php">Kontakt</a></li>
                        <?php

							if (isset($_SESSION['username'])) {
    					        // User is logged in
   			 			        //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                                echo "<li><a href=\"subpages/logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
							} 
                            
                            else {
   						        // User is not logged in
    					        echo "<li><a href=\"subpages/login.php\"><i class=\"fas fa-sign-in-alt\"></i> Login</a></li>";
							}
						?>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <section class="main-content">
        <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="status-message status-message-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            } elseif (isset($_SESSION['error'])) {
                echo '<div class="status-message status-message-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
        ?>

            <article class="featured">
                    <h2>Vítejte na stránkách Troskopisu</h2>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Praesent in mauris eu tortor porttitor accumsan. Praesent id justo in neque elementum ultrices. Integer imperdiet lectus quis justo. Nulla pulvinar eleifend sem. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Duis condimentum augue id magna semper rutrum. Phasellus et lorem id felis nonummy placerat. Nullam eget nisl.</p>
            </article>
    </section>

            

    <footer>
        <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
        polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
    </footer>
</body>
</html>
