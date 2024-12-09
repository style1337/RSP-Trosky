<?php
    session_start();
    require("connect.php");

    // Povolit přístup pouze pro role "admin", "author", "reviewer", "editor", "chiefeditor"
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'author', 'reviewer', 'editor', 'chiefeditor'])) {
        header("Location: unauthorized.php");
        exit();
    }

    // Načtení aktuální verze článku
    $article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;
    $article_query = "
        SELECT a.*, u.username AS author_name,
            DATE_FORMAT(a.date, '%d.%m.%Y %H:%i') as formatted_date
        FROM troskopis_articles a 
        JOIN troskopis_users u 
        ON a.author_id = u.user_id 
        WHERE a.article_id = $article_id
    ";
    $article_result = mysqli_query($spojeni, $article_query);
    $article = mysqli_fetch_assoc($article_result);

    if (!$article) {
        $_SESSION['error'] = "Článek nebyl nalezen.";
        header("Location: article_panel.php");
        exit();
    }

    // Načtení historie článku
    $history_query = "
        SELECT h.*, u.username AS author_name,
            DATE_FORMAT(h.date, '%d.%m.%Y %H:%i') as formatted_date
        FROM troskopis_articlehistory h
        JOIN troskopis_users u ON h.author_id = u.user_id
        WHERE h.article_id = $article_id
        ORDER BY h.version DESC
    ";
    $history_result = mysqli_query($spojeni, $history_query);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Recenze článku</title>
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
            
            <div class="review-container">
                <h2>Historie článku "<?php echo htmlspecialchars($article['name']); ?>"</h2>
                
                <!-- Latest version from articles table -->
                <div class="review current">
                    <p><strong style="color: #28a745;">Aktuální verze</strong></p>
                    <p><strong>Název:</strong> <?php echo htmlspecialchars($article['name']); ?></p>
                    <p><strong>Autor:</strong> <?php echo htmlspecialchars($article['author_name']); ?></p>
                    <p><strong>Verze:</strong> <?php echo htmlspecialchars($article['version']); ?></p>
                    <p><strong>Kategorie:</strong> <?php echo htmlspecialchars($article['category']); ?></p>
                    <p><strong>Datum:</strong> <?php echo htmlspecialchars($article['formatted_date']); ?></p>
                    <a href="<?php echo htmlspecialchars($article['file']); ?>" class="review-button">Zobrazit soubor</a>
                </div>

                <?php
                    while ($version = mysqli_fetch_assoc($history_result)) {
                        $versionClass = $version['hidden'] == 1 ? 'review red' : 'review latest';
                        $statusText = $version['hidden'] == 1 
                            ? '<p><strong style="color: #dc3545;">Verze upravena na základě připomínek</strong></p>'
                            : '<p><strong style="color: #007bff;">Archivovaná verze</strong></p>';
                        
                        echo '<div class="' . $versionClass . '">';
                        echo $statusText;
                        echo '<p><strong>Název:</strong> ' . htmlspecialchars($version['name']) . '</p>';
                        echo '<p><strong>Autor:</strong> ' . htmlspecialchars($version['author_name']) . '</p>';
                        echo '<p><strong>Verze:</strong> ' . htmlspecialchars($version['version']) . '</p>';
                        echo '<p><strong>Kategorie:</strong> ' . htmlspecialchars($version['category']) . '</p>';
                        echo '<p><strong>Datum:</strong> ' . htmlspecialchars($version['formatted_date']) . '</p>';
                        echo '<a href="' . htmlspecialchars($version['file']) . '" class="review-button">Zobrazit soubor</a>';
                        echo '</div>';
                    }
                ?>
            </div>
        </main>

        <footer>
        <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>
    </body>
</html>