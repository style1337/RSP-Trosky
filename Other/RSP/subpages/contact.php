<?php
    session_start();
    require("connect.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Kontakt</title>
        <link rel="stylesheet" href="../design.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
    <body>
    <header>
        <div class="header-container">
            <div class="left-nav">
            <ul>
                <li><a href="../trosky.php"><i class="fas fa-home"></i> Hlavní strana</a></li>
                <li><a href="./articles.php">Články</a></li>
                <li><a href="./aboutus.php">O nás</a></li>
                <li><a href="./contact.php">Kontakt</a></li>
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
                            echo '<li><a href="./apanel.php"><i class="fas fa-tools"></i> Admin panel</a></li>';
                        }
                    ?>
                        <!-- Tlačítko pro nahrání článků se zobrazí pouze pro autora -->
                        <?php
                            if ((isset($_SESSION['role']) && $_SESSION['role'] == 'author') || (isset($_SESSION['role']) && $_SESSION['role'] == 'admin')) {
                                echo '<li><a href="./article_upload.php"><i class="fas fa-upload"></i> Nahrát článek</a></li>';
                            }
                        ?>
                    <!-- Tlačítko pro panel článků se zobrazí pro přihlášené uživatele -->
                    <?php
                        if (isset($_SESSION['username'])) {
                            echo '<li><a href="./article_panel.php"><i class="fas fa-newspaper"></i> Panel článků</a></li>';
                        }
                    ?>
                    <?php

                        if (isset($_SESSION['username'])) {
                            // User is logged in
                            //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                            echo "<li><a href=\"./logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
                        } 
                        
                        else {
                            // User is not logged in
                            echo "<li><a href=\"./login.php\"><i class=\"fas fa-sign-in-alt\"></i> Login</a></li>";
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
                                echo '<li><a href="./article_upload.php"><i class="fas fa-upload"></i> Nahrát článek</a></li>';
                            }
                        ?>
                        <?php
                            if (isset($_SESSION['username'])) {
                                echo '<li><a href="./article_panel.php"><i class="fas fa-newspaper"></i> Panel článků</a></li>';
                            }
                        ?>
                        <li><a href="../trosky.php"><i class="fas fa-home"></i> Hlavní strana</a></li>
                        <li><a href="./articles.php">Články</a></li>
                        <li><a href="./aboutus.php">O nás</a></li>
                        <li><a href="./contact.php">Kontakt</a></li>
                        <?php

							if (isset($_SESSION['username'])) {
    					        // User is logged in
   			 			        //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                                echo "<li><a href=\"./logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
							} 
                            
                            else {
   						        // User is not logged in
    					        echo "<li><a href=\"./login.php\"><i class=\"fas fa-sign-in-alt\"></i> Login</a></li>";
							}
						?>
                    </ul>
                </div>
            </div>
        </div>
    </header>
        <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="status-message status-message-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            } elseif (isset($_SESSION['error'])) {
                echo '<div class="status-message status-message-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
        ?>

    <section class="main-content">
        <article class="featured">
            <h2>Kontakt</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-avatar">
                        <img src="../images/tachovsk.png" alt="MT">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">Matyáš Tachovský</div>
                        <div class="contact-role">Scrum Master</div>
                        <a href="mailto:tachovsk@student.vspj.cz" class="contact-email">tachovsk@student.vspj.cz</a>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-avatar">
                        <img src="../images/kriz17.png" alt="PK">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">Patrik Kříž</div>
                        <div class="contact-role">Product Owner</div>
                        <a href="mailto:kriz17@student.vspj.cz" class="contact-email">kriz17@student.vspj.cz</a>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-avatar">
                        <img src="../images/kos07.png" alt="MK">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">Martin Kos</div>
                        <div class="contact-role">Team Member</div>
                        <a href="mailto:kos07@student.vspj.cz" class="contact-email">kos07@student.vspj.cz</a>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-avatar">
                        <img src="../images/macek06.jpg" alt="FM">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">Filip Macek</div>
                        <div class="contact-role">Team Member</div>
                        <a href="mailto:macek06@student.vspj.cz" class="contact-email">macek06@student.vspj.cz</a>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-avatar">
                        <img src="../images/kudlac11.jpg" alt="JK">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">Jan Kudláček</div>
                        <div class="contact-role">Team Member</div>
                        <a href="mailto:kudlac11@student.vspj.cz" class="contact-email">kudlac11@student.vspj.cz</a>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-avatar">
                        <img src="../images/haruda.jpg" alt="MH">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">Marek Haruda</div>
                        <div class="contact-role">Team Member</div>
                        <a href="mailto:haruda@student.vspj.cz" class="contact-email">haruda@student.vspj.cz</a>
                    </div>
                </div>
            </div>
        </article>
    </section>
            
        

        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>
    </body>
</html>