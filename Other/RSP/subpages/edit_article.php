<?php
    session_start();
    require("connect.php");

    // Povolit přístup pouze pro roli "author"
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'author') {
        header("Location: unauthorized.php");
        exit();
    }

    // Get article ID and appealed parameter
    $article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;
    $appealed = isset($_GET['appealed']) ? intval($_GET['appealed']) : 0;

    // Check if article exists and user is owner
    $article_query = "
        SELECT * FROM troskopis_articles 
        WHERE article_id = $article_id 
        AND author_id = {$_SESSION['user_id']}
    ";
    $article_result = mysqli_query($spojeni, $article_query);
    $article = mysqli_fetch_assoc($article_result);

    if (!$article) {
        $_SESSION['error'] = "Článek nebyl nalezen nebo k němu nemáte přístup.";
        header("Location: article_panel.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['pdfFile'])) {
            $fileTmpPath = $_FILES['pdfFile']['tmp_name'];
            $fileName = $_FILES['pdfFile']['name'];
            $fileType = $_FILES['pdfFile']['type'];

            if ($fileType === 'application/pdf') {
                // Generate new unique filename
                $uploadPath = '../articles/' . uniqid() . '_' . basename($fileName);

                if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                    // Begin transaction
                    mysqli_begin_transaction($spojeni);

                    try {
                        // Insert current version into history
                        $history_query = "
                            INSERT INTO troskopis_articlehistory 
                            (article_id, author_id, name, file, category, date, version, hidden)
                            VALUES 
                            ({$article['article_id']}, {$article['author_id']}, 
                             '{$article['name']}', '{$article['file']}', 
                             {$article['category']}, '{$article['date']}', 
                             {$article['version']}, " . ($appealed ? "TRUE" : "FALSE") . ")
                        ";
                        
                        mysqli_query($spojeni, $history_query);

                        // Update article with new file, increment version, update date and status
                        $new_version = $article['version'] + 1;
                        $current_date = date('Y-m-d H:i:s');
                        $update_query = "
                            UPDATE troskopis_articles 
                            SET file = '$uploadPath',
                                version = $new_version,
                                date = '$current_date',
                                status = 'pending_review'
                            WHERE article_id = $article_id
                        ";
                        
                        mysqli_query($spojeni, $update_query);

                        mysqli_commit($spojeni);
                        $_SESSION['success'] = "Článek byl úspěšně aktualizován.";
                        header("Location: article_panel.php");
                        exit();
                    } catch (Exception $e) {
                        mysqli_rollback($spojeni);
                        $_SESSION['error'] = "Chyba při aktualizaci článku: " . $e->getMessage();
                    }
                } else {
                    $_SESSION['error'] = "Chyba při nahrávání souboru.";
                }
            } else {
                $_SESSION['error'] = "Pouze PDF soubory jsou povoleny.";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Nahrát článek</title>
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
            <div class="upload-form">
                <h2>Upravit článek: <?php echo htmlspecialchars($article['name']); ?></h2>
                <form action="edit_article.php?article_id=<?php echo $article_id; ?>&appealed=<?php echo $appealed; ?>" method="POST" enctype="multipart/form-data">
                    <label for="pdfFile">Vyberte novou verzi PDF souboru:</label>
                    <input type="file" name="pdfFile" accept="application/pdf" required>
                    <button type="submit">Nahrát novou verzi</button>
                </form>
            </div>
        </section>

        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>
    </body>
</html>