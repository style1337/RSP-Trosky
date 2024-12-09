<?php
    session_start();
    require("connect.php");

    // Zkontrolování, zda je uživatel přihlášen a má příslušnou roli
    if ($_SESSION['role'] != 'admin') {
        // Pokud uživatel není přihlášen nebo nemá příslušnou roli, přesměrování na stránku s chybou
        header("Location: unauthorized.php");
        exit();
    } 

    // Funkce pro formátování role
    function formatRole($role) {
        switch($role) {
            case 'author': return 'Autor';
            case 'editor': return 'Redaktor';
            case 'chiefeditor': return 'Šéfredaktor';
            case 'admin': return 'Administrátor';
            case 'reviewer': return 'Recenzent';
            default: return $role;
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Panel článků</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../design.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <script>
            async function confirmDelete(userId) {
                try {
                    const response = await fetch('get_user_articles.php?id=' + userId);
                    const data = await response.json();
                    
                    const message = `Tento uživatel má ${data.articleCount} článků.\n\n` +
                                  `Opravdu chcete smazat tohoto uživatele a všechny jeho články?`;
                    
                    if (confirm(message)) {
                        window.location.href = 'admin_delete_user.php?id=' + userId;
                    }
                } catch (error) {
                    alert('Nepodařilo se získat informace o článcích.');
                }
            }
        </script>
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
                <button class="dropbtn"><i class="fas fa-bars\"></i></button>
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

    <main>
         <?php
                if (isset($_SESSION['success'])) {
                    echo '<div class="status-message status-message-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                    unset($_SESSION['success']);
                } elseif (isset($_SESSION['error'])) {
                    echo '<div class="status-message status-message-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                    unset($_SESSION['error']);
                }
            ?>
        <div class="user-list">
            <div class="user-row user-header">
                <div>ID</div>
                <div>Username</div>
                <div>Password</div>
                <div>E-mail</div>
                <div>Role</div>
                <div>Akce</div>
            </div>
            <?php
                $role = $_SESSION['role'];
                $user_id = $_SESSION['user_id'];
                $query = "SELECT * from troskopis_users;";
                $dispusers = mysqli_query($spojeni, $query);

                if ($dispusers) {
                    while ($row = mysqli_fetch_assoc($dispusers)) {
                        echo "<div class=\"user-row\">
                        <div>" . $row['user_id'] . "</div>
                        <div>" . $row['username'] . "</div>
                        <div><a href=\"passwdscript.php?id=" . $row['user_id'] . "\" class=\"change-password-button\">Změnit heslo</a></div>
                        <div>" . $row['email'] . "</div>
                        <div>" . formatRole($row['role']) . "</div>
                        <div class=\"action-buttons\">
                            <a href=\"admin_edit_details.php?id=" . $row['user_id'] . "\" class=\"edit-button\">Upravit</a>
                            <a href=\"#\" onclick=\"confirmDelete(" . $row['user_id'] . "); return false;\" class=\"delete-button\">Odstranit</a>
                        </div>
                        </div>";
                    }
                }
            ?>
        </div>

    </main>
        <footer>
            <p>Tata aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>

    </body>
</html>