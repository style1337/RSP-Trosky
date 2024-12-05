<?php
    session_start();
    require("connect.php");

    // Povolit přístup pouze pro roli "reviewer"
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
        header("Location: unauthorized.php");
        exit();
    }

    // Načtení informací o článku a autorovi
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

    // Zpracování odeslaného formuláře
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $relevance = intval($_POST['relevance']);
        $originality = intval($_POST['originality']);
        $scientific = intval($_POST['scientific']);
        $style = intval($_POST['style']);
        $comments = mysqli_real_escape_string($spojeni, $_POST['comments']);
        $review_date = date('Y-m-d H:i:s');
        $article_version = $article['version'];

        // Vložení recenze do tabulky troskopis_reviews
        $review_query = "
            INSERT INTO troskopis_reviews (
                article_id, 
                reviewer_id, 
                article_version, 
                score_relevance, 
                score_originality, 
                score_scientific, 
                score_style, 
                comment, 
                date,
                version
            ) 
            VALUES (
                $article_id, 
                {$_SESSION['user_id']}, 
                $article_version, 
                $relevance, 
                $originality, 
                $scientific, 
                $style, 
                '$comments', 
                '$review_date',
                (SELECT version FROM troskopis_articles WHERE article_id = $article_id)
            )
        ";

        if (mysqli_query($spojeni, $review_query)) {
            // Aktualizace stavu článku na "reviewed"
            $update_status_query = "
                UPDATE troskopis_articles 
                SET status = 'reviewed' 
                WHERE article_id = $article_id
            ";
            mysqli_query($spojeni, $update_status_query);

            $_SESSION['success'] = "Recenze byla úspěšně odeslána.";
        } else {
            $_SESSION['error'] = "Chyba při ukládání recenze: " . mysqli_error($spojeni);
        }

        header("Location: article_panel.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Recenze článku</title>
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
            <div class="review-form">
                <h2>Recenze článku</h2>
                <p><strong>Autor:</strong> <?php echo htmlspecialchars($article['username']); ?></p>
                <p><strong>Název článku:</strong> <?php echo htmlspecialchars($article['name']); ?></p>
                <form action="article_review.php?article_id=<?php echo $article_id; ?>" method="POST">
                    <label for="relevance">Relevance:</label>
                    <div class="radio-group">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="relevance" value="<?php echo $i; ?>" required> <?php echo $i; ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <label for="originality">Originalita:</label>
                    <div class="radio-group">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="originality" value="<?php echo $i; ?>" required> <?php echo $i; ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <label for="scientific">Odborná úroveň:</label>
                    <div class="radio-group">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="scientific" value="<?php echo $i; ?>" required> <?php echo $i; ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <label for="style">Jazyková a stylistická úroveň:</label>
                    <div class="radio-group">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="style" value="<?php echo $i; ?>" required> <?php echo $i; ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <label for="comments">Komentáře:</label>
                    <textarea name="comments" rows="5" required></textarea>
                    <button type="submit">Odeslat recenzi</button>
                </form>
            </div>
        </main>

        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>
    </body>
</html>