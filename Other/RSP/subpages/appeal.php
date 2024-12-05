<?php
    session_start();
    require("connect.php");

    // Povolit přístup pouze pro roli "author"
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'author') {
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

    // Get article name
    $article_query = "SELECT name FROM troskopis_articles WHERE article_id = $article_id";
    $article_result = mysqli_query($spojeni, $article_query);
    $article = mysqli_fetch_assoc($article_result);

    if (!$article) {
        $_SESSION['error'] = "Článek nebyl nalezen.";
        header("Location: article_panel.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $appeal_text = mysqli_real_escape_string($spojeni, $_POST['appeal_text']);
        
        $update_query = "
            UPDATE troskopis_articles 
            SET status = 'appealed', 
                appeal_message = '$appeal_text'
            WHERE article_id = $article_id
        ";

        if (mysqli_query($spojeni, $update_query)) {
            $_SESSION['success'] = "Námitka byla úspěšně odeslána.";
            header("Location: article_panel.php");
            exit();
        } else {
            $_SESSION['error'] = "Chyba při ukládání námitky: " . mysqli_error($spojeni);
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Podání námitky</title>
        <link rel="stylesheet" href="../design.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script>
            function confirmSubmit() {
                if (confirm("Poslat námitku redakci?")) {
                    document.getElementById('confirm').value = 'yes';
                    return true;
                }
                return false;
            }
        </script>
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
            
            <div class="appeal-form-container">
            <h2>Podání námitky na verdikt u vašeho článku "<?php echo htmlspecialchars($article['name']); ?>"</h2>
            <form method="POST" onsubmit="return confirmSubmit();">
                <textarea 
                    name="appeal_text" 
                    rows="10" 
                    maxlength="1000" 
                    required 
                    placeholder="Zde napište obsah námitky, maximálně 1000 znaků"
                ></textarea>
                <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($article_id); ?>">
                <input type="hidden" name="confirm" id="confirm" value="no">
                <button type="submit">Odeslat námitku</button>
            </form>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>