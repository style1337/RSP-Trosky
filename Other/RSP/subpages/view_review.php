<?php
    session_start();
    require("connect.php");

    // Povolit přístup pouze pro role "admin", "author", "reviewer", "editor", "chiefeditor"
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'author', 'reviewer', 'editor', 'chiefeditor'])) {
        header("Location: unauthorized.php");
        exit();
    }

    // Načtení informací o článku a recenzi
    $article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;
    $article_query = "
        SELECT a.*, u.username 
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

    // Kontrola oprávnění pro autora a recenzenta
    if ($_SESSION['role'] === 'author' && $article['author_id'] !== $_SESSION['user_id']) {
        $_SESSION['error'] = "Nemáte oprávnění zobrazit tuto recenzi.";
        header("Location: article_panel.php");
        exit();
    }

    if ($_SESSION['role'] === 'reviewer' && $article['assigned_reviewer'] !== $_SESSION['user_id']) {
        $_SESSION['error'] = "Nemáte oprávnění zobrazit tuto recenzi.";
        header("Location: article_panel.php");
        exit();
    }

    // Načtení recenzí
    $review_query = "
        SELECT r.*, u.username AS reviewer_name, 
            a.version as current_version,
            CASE 
                WHEN r.version = a.version THEN 'current'
                WHEN r.date = (SELECT MAX(r2.date) FROM troskopis_reviews r2 WHERE r2.article_id = r.article_id) THEN 'latest'
                ELSE 'old'
            END as review_status
        FROM troskopis_reviews r 
        JOIN troskopis_users u ON r.reviewer_id = u.user_id 
        JOIN troskopis_articles a ON r.article_id = a.article_id
        WHERE r.article_id = $article_id
        ORDER BY r.version DESC, r.date DESC
    ";
    $review_result = mysqli_query($spojeni, $review_query);
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
            <h2>Recenze článku "<?php echo htmlspecialchars($article['name']); ?>" od autora "<?php echo htmlspecialchars($article['username']); ?>"</h2>
            <?php
                while ($review = mysqli_fetch_assoc($review_result)) {
                    echo '<div class="review ' . $review['review_status'] . '">';
                    if ($review['review_status'] == 'current') {
                        echo '<p><strong style="color: #28a745;">Recenze aktuální verze</strong></p>';
                    } elseif ($review['review_status'] == 'latest') {
                        echo '<p><strong style="color: #007bff;">Nejnovější recenze</strong></p>';
                    }
                    echo '<p><strong>Recenzent:</strong> ' . htmlspecialchars($review['reviewer_name']) . '</p>';
                    echo '<p><strong>Verze článku:</strong> ' . htmlspecialchars($review['version']) . 
                         ($review['version'] == $review['current_version'] ? ' (aktuální)' : '') . '</p>';
                    echo '<p><strong>Relevance:</strong> ' . htmlspecialchars($review['score_relevance']) . '</p>';
                    echo '<p><strong>Originalita:</strong> ' . htmlspecialchars($review['score_originality']) . '</p>';
                    echo '<p><strong>Odborná úroveň:</strong> ' . htmlspecialchars($review['score_scientific']) . '</p>';
                    echo '<p><strong>Jazyková a stylistická úroveň:</strong> ' . htmlspecialchars($review['score_style']) . '</p>';
                    echo '<p><strong>Komentáře:</strong> ' . htmlspecialchars($review['comment']) . '</p>';
                    echo '<p><strong>Datum recenze:</strong> ' . date('d.m.Y H:i:s', strtotime($review['date'])) . '</p>';
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