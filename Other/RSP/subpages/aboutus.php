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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            

Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Proin mattis lacinia justo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Etiam sapien elit, consequat eget, tristique non, venenatis quis, ante. Donec iaculis gravida nulla. Aenean vel massa quis mauris vehicula lacinia. Nulla non arcu lacinia neque faucibus fringilla. Maecenas sollicitudin. Proin pede metus, vulputate nec, fermentum fringilla, vehicula vitae, justo. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Nunc auctor. Etiam sapien elit, consequat eget, tristique non, venenatis quis, ante. Etiam dui sem, fermentum vitae, sagittis id, malesuada in, quam.

Duis sapien nunc, commodo et, interdum suscipit, sollicitudin et, dolor. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Fusce aliquam vestibulum ipsum. Fusce dui leo, imperdiet in, aliquam sit amet, feugiat eu, orci. Vivamus luctus egestas leo. Fusce tellus. Etiam egestas wisi a erat. Et harum quidem rerum facilis est et expedita distinctio. Etiam bibendum elit eget erat. Quisque tincidunt scelerisque libero. Nullam sit amet magna in magna gravida vehicula. Fusce aliquam vestibulum ipsum.

Phasellus et lorem id felis nonummy placerat. Cras elementum. Nullam rhoncus aliquam metus. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Vivamus luctus egestas leo. Integer pellentesque quam vel velit. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Mauris dictum facilisis augue. Quisque porta. Fusce nibh. Integer malesuada. In laoreet, magna id viverra tincidunt, sem odio bibendum justo, vel imperdiet sapien wisi sed libero. Praesent vitae arcu tempor neque lacinia pretium. Aliquam erat volutpat.

Fusce nibh. Nullam sit amet magna in magna gravida vehicula. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Suspendisse sagittis ultrices augue. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Quisque porta. Fusce consectetuer risus a nunc. Aliquam ante. Nunc tincidunt ante vitae massa. Fusce wisi. Sed elit dui, pellentesque a, faucibus vel, interdum nec, diam. Aliquam id dolor. Donec quis nibh at felis congue commodo. Phasellus rhoncus. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.

Nullam at arcu a est sollicitudin euismod. Aliquam ante. Proin pede metus, vulputate nec, fermentum fringilla, vehicula vitae, justo. Vivamus porttitor turpis ac leo. Nullam faucibus mi quis velit. Etiam commodo dui eget wisi. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Morbi scelerisque luctus velit. Nullam sapien sem, ornare ac, nonummy non, lobortis a enim. Pellentesque ipsum. Nullam justo enim, consectetuer nec, ullamcorper ac, vestibulum in, elit. Phasellus rhoncus. Nulla non lectus sed nisl molestie malesuada. Cras elementum.

        </section>

        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>
    </body>
</html>