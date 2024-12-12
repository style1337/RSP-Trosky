<?php
    session_start();
    require("connect.php");

    // Povolit přístup pouze pro roli "author" nebo "admin"
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'author' && $_SESSION['role'] !== 'admin')) {
        header("Location: unauthorized.php");
        exit();
    }

    // Get article ID and appealed parameter
    $article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;
    $appealed = isset($_GET['appealed']) ? intval($_GET['appealed']) : 0;

    // Check if article exists
    $article_query = "
        SELECT * FROM troskopis_articles 
        WHERE article_id = $article_id
    ";

    // Prepared statement pro získání článku
    if ($_SESSION['role'] === 'admin') {
        $article_query = "SELECT * FROM troskopis_articles WHERE article_id = ?";
        $stmt = mysqli_prepare($spojeni, $article_query);
        mysqli_stmt_bind_param($stmt, "i", $article_id);
    } else {
        $article_query = "SELECT * FROM troskopis_articles WHERE article_id = ? AND author_id = ?";
        $stmt = mysqli_prepare($spojeni, $article_query);
        mysqli_stmt_bind_param($stmt, "ii", $article_id, $_SESSION['user_id']);
    }

    mysqli_stmt_execute($stmt);
    $article_result = mysqli_stmt_get_result($stmt);
    $article = mysqli_fetch_assoc($article_result);

    if (!$article) {
        $_SESSION['error'] = "Článek nebyl nalezen nebo k němu nemáte přístup.";
        header("Location: article_panel.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['pdfFile']['tmp_name'];
            $fileName = $_FILES['pdfFile']['name'];

            // Vylepšená validace PDF
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fileTmpPath);
            finfo_close($finfo);

            // Seznam povolených MIME typů
            $allowedMimeTypes = [
                'application/pdf',
                'application/x-pdf',
                'application/acrobat',
                'application/vnd.pdf',
                'text/pdf',
                'text/x-pdf'
            ];

            // Kontrola MIME typu a přípony
            $isPDF = (in_array($mimeType, $allowedMimeTypes) && 
            strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'pdf');

            if ($isPDF) {
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
                    header("Location: edit_article.php?id=" . $article_id . "&appealed=" . $appealed);
                    exit();
                }
            } else {
                $_SESSION['error'] = "Soubor musí být ve formátu PDF. Detekován formát: " . $mimeType;
                header("Location: edit_article.php?id=" . $article_id . "&appealed=" . $appealed);
                exit();
            }
        } else {
            $_SESSION['error'] = "Chyba při nahrávání souboru nebo soubor nebyl vybrán.";
            header("Location: edit_article.php?id=" . $article_id . "&appealed=" . $appealed);
            exit();
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