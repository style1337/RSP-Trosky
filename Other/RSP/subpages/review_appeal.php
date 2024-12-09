<?php
    session_start();
    require("connect.php");

    // Modify access check to include admin
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'editor'])) {
        header("Location: unauthorized.php");
        exit();
    }

    // Načtení ID článku z GET parametrů
    $article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;

    if ($article_id <= 0) {
        $_SESSION['error'] = "Neplatné ID článku.";
        header("Location: article_panel.php");
        exit();
    }

    // Get article details including appeal
    $article_query = "
        SELECT a.*, u.username 
        FROM troskopis_articles a 
        JOIN troskopis_users u ON a.author_id = u.user_id 
        WHERE a.article_id = $article_id
    ";
    $article_result = mysqli_query($spojeni, $article_query);
    $article = mysqli_fetch_assoc($article_result);

    if (!$article) {
        $_SESSION['error'] = "Článek nebyl nalezen.";
        header("Location: article_panel.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch($_POST['action']) {
                case 'approve':
                    $new_status = 'approved';
                    $success_message = "Článek byl úspěšně schválen.";
                    break;
                case 'reject':
                    $new_status = 'rejected';
                    $success_message = "Článek byl zamítnut.";
                    break;
                case 'edit':
                    $new_status = 'editing';
                    $success_message = "Článek byl vrácen k úpravě.";
                    break;
            }
            
            $update_query = "
                UPDATE troskopis_articles 
                SET status = '$new_status'
                WHERE article_id = $article_id
            ";

            if (mysqli_query($spojeni, $update_query)) {
                $_SESSION['success'] = $success_message;
                header("Location: article_panel.php");
                exit();
            } else {
                $_SESSION['error'] = "Chyba při aktualizaci stavu článku: " . mysqli_error($spojeni);
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Posouzení námitky</title>
        <link rel="stylesheet" href="../design.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <script>
            function confirmAction(action) {
                let message;
                switch(action) {
                    case 'approve':
                        message = "Opravdu chcete schválit tento článek?";
                        break;
                    case 'reject':
                        message = "Opravdu chcete zamítnout tento článek?";
                        break;
                    case 'edit':
                        message = "Opravdu chcete vrátit článek k úpravě?";
                        break;
                }
                return confirm(message);
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
            
            <div class="appeal-container">
                <h2>Námitka k zamítnutí článku "<?php echo htmlspecialchars($article['name']); ?>" od autora "<?php echo htmlspecialchars($article['username']); ?>"</h2>
                
                <div class="appeal-text">
                    <?php echo htmlspecialchars($article['appeal_message']); ?>
                </div>

                <form method="POST" class="button-container">
                <button 
                    type="submit" 
                    name="action" 
                    value="approve" 
                    class="appeal-button approve-button"
                    onclick="return confirmAction('approve')"
                >
                    Schválit
                </button>
                <button 
                    type="submit" 
                    name="action" 
                    value="edit" 
                    class="appeal-button edit-button-appeal"
                    onclick="return confirmAction('edit')"
                    style="background-color: #ffc107;"
                >
                    Vrátit k úpravě
                </button>
                <button 
                    type="submit" 
                    name="action" 
                    value="reject" 
                    class="appeal-button reject-button"
                    onclick="return confirmAction('reject')"
                >
                    Zamítnout
                </button>
                    <a href="article_panel.php" class="appeal-button" style="background-color: #6c757d;">Zpět</a>
                </form>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>