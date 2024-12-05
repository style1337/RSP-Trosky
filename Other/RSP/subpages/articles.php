<?php
    session_start();
    require("connect.php");
    //použití ternárního operátoru pro nastavení výchozí role v případě nepřihlášeného člena
    $role = $_SESSION['role'] ?? 'guest';
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Články Troskopisu</title>
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
                        <!-- Tlačítko pro nahrání článků se zobrazí pouze pro autora -->
                        <?php
                            if (isset($_SESSION['role']) && $_SESSION['role'] == 'author') {
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


        <?php
            $query = "SELECT name, file, category, date FROM troskopis_articles WHERE status = 'approved' ORDER BY date DESC;";
            $texts = mysqli_query($spojeni, $query);
            //$now = max($texts['date']);
            if (isset($texts)) {
                $text = mysqli_fetch_assoc($texts);
                echo "<article class=\"featured\">
                <h2>" . $text['name'] . "</h2>
                <object data=\"" . $text['file'] . "\" type=\"application/pdf\" width=\"100%\" height=\"500px\">
                    <p>Unable to display PDF file. <a href=\"/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf\">Download</a> instead.</p>
                </object>
                </article>";
                while ($text = mysqli_fetch_assoc($texts)) {

                    echo "<section class=\"articles\">
                    <article>
                        <h3>" . $text['name'] . "</h3>
                        <a href=\"" . $text['file'] . "\" class=\"review-button\">Zobrazit článek</a> 
                    </article>";
                }
                
            }
            else {
                echo "<article class=\"featured\">
                <h2>Žádný článek není k dispozici</h2>
                <p>Aktuálně není žádný článek</p>
                </article>";
            }
            /*else if ($now) {
                echo "<article class=\"featured\">
                <h2>" . $texts['name'] . "</h2>
                <object data=\"/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf\" type=\"application/pdf\" width=\"100%\" height=\"500px\">
      <p>Unable to display PDF file. <a href=\"/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf\">Download</a> instead.</p>
    </object>
                </article>";
            }*/

            /*else {
                echo "<section class=\"articles\">
                <article>
                    <h3>" . $texts['name'] . "</h3>
                    <object data=\"/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf\" type=\"application/pdf\" width=\"100%\" height=\"500px\">
      <p>Unable to display PDF file. <a href=\"/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf\">Download</a> instead.</p>
    </object>
                </article>";
            }*/

            /*<object data=\"" . $text['file'] . "\" type=\"application/pdf\" width=\"100%\" height=\"500px\">
                        <p>Unable to display PDF file. <a href=\"/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf\">Download</a> instead.</p>
                        </object>*/
?>
    </section>
</section>

    <footer>
        <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
        polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
    </footer>
</body>
</html>
