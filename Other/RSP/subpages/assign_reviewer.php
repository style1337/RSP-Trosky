<?php
    session_start();
    require("connect.php");

    // Allow access only for role "editor"
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'editor') {
        header("Location: unauthorized.php");
        exit();
    }

    // Zobrazení formálního statusu
    function getFormalStatus($status) {
        switch($status) {
            case 'pending_assignment':
                return 'Čeká na přiřazení';
            case 'pending_review':
                return 'Čeká na recenzi';
            case 'reviewed':
                return 'Recenzováno';
            case 'editing':
                return 'Námitka schválena, čeká na úpravy';
            case 'approved':
                return 'Schváleno';
            case 'rejected':
                return 'Zamítnuto';
            case 'appealed':
                return 'Odvoláno';
            default:
                return $status;
        }
    }

    // Save posted "?article_id=" to a variable
    $article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $reviewer_id = isset($_POST['reviewer_id']) ? intval($_POST['reviewer_id']) : 0;

        if ($reviewer_id > 0) {
            // Update the article with the selected reviewer
            $update_query = "
                UPDATE troskopis_articles 
                SET status = 'pending_review', assigned_reviewer = $reviewer_id 
                WHERE article_id = $article_id
            ";

            if (mysqli_query($spojeni, $update_query)) {
                $_SESSION['success'] = "Recenzent byl úspěšně přiřazen.";
            } else {
                $_SESSION['error'] = "Došlo k chybě při přiřazování recenzenta.";
            }

            header("Location: article_panel.php");
            exit();
        } else {
            $_SESSION['error'] = "Musíte vybrat recenzenta.";
            header("Location: assign_reviewer.php?article_id=$article_id");
            exit();
        }
    }

    // Fetch article information
    $article_query = "
        SELECT a.*, u.username 
        FROM troskopis_articles a 
        JOIN troskopis_users u 
        ON a.author_id = u.user_id 
        WHERE a.article_id = $article_id
    ";
    $article_result = mysqli_query($spojeni, $article_query);
    $article = mysqli_fetch_assoc($article_result);

    // Fetch reviewers
    $reviewers_query = "
        SELECT user_id, username 
        FROM troskopis_users 
        WHERE role = 'reviewer'
    ";
    $reviewers_result = mysqli_query($spojeni, $reviewers_query);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Přiřadit recenzenta</title>
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
            <div class="article-info">
                <h2>Informace o článku</h2>
                <p><strong>Název:</strong> <?php echo htmlspecialchars($article['name']); ?></p>
                <p><strong>Autor:</strong> <?php echo htmlspecialchars($article['username']); ?></p>
                <p><strong>Tématické číslo:</strong> <?php echo htmlspecialchars($article['category']); ?></p>
                <p><strong>Datum:</strong> <?php echo htmlspecialchars(date("d.m.Y H:i:s", strtotime($article['date']))); ?></p>
                <p><strong>Verze:</strong> <?php echo htmlspecialchars($article['version']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars(getFormalStatus($row['status'])); ?></p>
                <a href="<?php echo htmlspecialchars($article['file']); ?>" target="_blank">Zobrazit PDF</a>
            </div>
            <div class="reviewer-selection">
                <h2>Vyberte recenzenta</h2>
                <form method="post" action="assign_reviewer.php?article_id=<?php echo $article_id; ?>">
                    <select name="reviewer_id">
                        <option value="">-- Vyberte recenzenta --</option>
                        <?php
                            while ($reviewer = mysqli_fetch_assoc($reviewers_result)) {
                                echo '<option value="' . htmlspecialchars($reviewer['user_id']) . '">' . htmlspecialchars($reviewer['username']) . '</option>';
                            }
                        ?>
                    </select>
                    <div class="button-container">
                        <button type="submit">Přiřadit recenzenta</button>
                        <a href="article_panel.php">Zpět na panel článků</a>
                    </div>
                </form>
            </div>
        </main>

        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>
    </body>
</html>