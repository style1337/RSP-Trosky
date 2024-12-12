<?php
    session_start();
    require("connect.php");
    $role = $_SESSION['role'] ?? 'guest';
    
    // Get selected category, default to 1 if not set
    $category = isset($_GET['category']) ? (int)$_GET['category'] : 1;
    // Ensure category is between 1-5
    $category = max(1, min(5, $category));
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Články Troskopisu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
    <script>
        window.addEventListener('load', function() {
            document.querySelector('.preloader').style.display = 'none';
        });
    </script>
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

    <nav class="category-nav">
        <ul>
            <li class="<?php echo $category == 1 ? 'active' : ''; ?>">
                <a href="?category=1">Kategorie 1</a>
            </li>
            <li class="<?php echo $category == 2 ? 'active' : ''; ?>">
                <a href="?category=2">Kategorie 2</a>
            </li>
            <li class="<?php echo $category == 3 ? 'active' : ''; ?>">
                <a href="?category=3">Kategorie 3</a>
            </li>
            <li class="<?php echo $category == 4 ? 'active' : ''; ?>">
                <a href="?category=4">Kategorie 4</a>
            </li>
            <li class="<?php echo $category == 5 ? 'active' : ''; ?>">
                <a href="?category=5">Kategorie 5</a>
            </li>
        </ul>
    </nav>

    <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="status-message status-message-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            } elseif (isset($_SESSION['error'])) {
                echo '<div class="status-message status-message-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
        ?>

    <section class="main-content">
        <div class="preloader">
            <div class="spinner"></div>
        </div>
        
        
        
        <?php
            $query = "SELECT a.name, a.file, a.category, a.date, a.featured, a.article_id, u.username as author 
                     FROM troskopis_articles a 
                     LEFT JOIN troskopis_users u ON a.author_id = u.user_id 
                     WHERE a.status = 'approved' AND a.category = ? 
                     ORDER BY a.featured DESC, a.date DESC;";
            $stmt = mysqli_prepare($spojeni, $query);
            mysqli_stmt_bind_param($stmt, "i", $category);
            mysqli_stmt_execute($stmt);
            $texts = mysqli_stmt_get_result($stmt);

            if ($texts && mysqli_num_rows($texts) > 0) {
                $featured_shown = false;
                $all_articles = [];
                
                // First pass: check if there's any featured article and collect all articles
                while ($text = mysqli_fetch_assoc($texts)) {
                    $all_articles[] = $text;
                    if ($text['featured'] == 1) {
                        $featured_shown = true;
                    }
                }

                // If no featured article, make the latest one featured
                if (!$featured_shown && !empty($all_articles)) {
                    $all_articles[0]['featured'] = 1;
                    $featured_shown = true;
                }

                // Display articles
                foreach ($all_articles as $text) {
                    if ($text['featured'] == 1 && $featured_shown) {
                        echo "<article class=\"featured\">
                            <h2>" . $text['name'] . "</h2>
                            <div class='article-meta'>
                                <span class='author'>Autor: " . htmlspecialchars($text['author']) . "</span>
                                <span class='date'>Datum: " . date('d.m.Y', strtotime($text['date'])) . "</span>
                            </div>
                            <div class=\"pdf-container\">
                                <object data=\"" . $text['file'] . "\" type=\"application/pdf\" width=\"100%\" height=\"800\">
                                    <p>Unable to display PDF file. <a href=\"" . $text['file'] . "\">Download</a> instead.</p>
                                </object>
                            </div>
                        </article>";
                        echo "<section class=\"articles\">";
                    } else {
                        echo "<article class='article-card'>";
                        if (($role == 'admin' || $role == 'editor') && $text['featured'] != 1) {
                            echo "<div class='quick-actions featured-container'>
                                <a href='set_featured.php?id=" . $text['article_id'] . "&category=" . $category . "' class='featured-button'>Nastavit jako hlavní</a>
                            </div>";
                        }
                        echo "<h3>" . $text['name'] . "</h3>
                            <div class='article-meta'>
                                <span class='author'>Autor: " . htmlspecialchars($text['author']) . "</span>
                                <span class='date'>Datum: " . date('d.m.Y', strtotime($text['date'])) . "</span>
                            </div>
                            <a href=\"" . $text['file'] . "\" class=\"review-button\">Zobrazit článek</a>
                        </article>";
                    }
                }
                echo "</section>";
            } else {
                echo "<article class=\"featured\">
                    <h2>Žádný článek není k dispozici</h2>
                    <p>V této kategorii není momentálně žádný článek</p>
                </article>";
            }
        ?>
    </section>

    <footer>
        <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
        polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
    </footer>
</body>
</html>
