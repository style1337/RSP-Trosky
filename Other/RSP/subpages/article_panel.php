<?php
    session_start();
    require("connect.php");

    // Zkontrolování, zda je uživatel přihlášen a má příslušnou roli
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'reviewer', 'editor', 'chiefeditor', 'author'])) {
        // Pokud uživatel není přihlášen nebo nemá příslušnou roli, přesměrování na stránku s chybou
        header("Location: unauthorized.php");
        exit();
    }

    // Funkce pro formátování statusu článku
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
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Panel článků</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../design.css">
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
                <?php
                            if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                echo '<li><a href="./apanel.php">Admin panel</a></li>';
                            }
                        ?>
                        <!-- Tlačítko pro nahrání článků se zobrazí pouze pro autora -->
                        <?php
                        
                            if (isset($_SESSION['role']) && $_SESSION['role'] == 'author' || $_SESSION['role'] == 'admin') {
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
                            if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                echo '<li><a href="./apanel.php">Admin panel</a></li>';
                            }
                        ?>
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
            <div class="article-grid">
                <?php
                        $role = $_SESSION['role'];
                        $user_id = $_SESSION['user_id'];
                        $query = "";

                        if ($role === 'author') {
                            $query = "
                                SELECT a.*, u.username 
                                FROM troskopis_articles a 
                                JOIN troskopis_users u 
                                ON a.author_id = u.user_id
                                WHERE a.author_id = $user_id
                            ";
                        } elseif ($role === 'reviewer') {
                            $query = "
                                SELECT a.*, u.username 
                                FROM troskopis_articles a 
                                JOIN troskopis_users u 
                                ON a.author_id = u.user_id
                                WHERE a.assigned_reviewer = $user_id
                            ";
                        } else {
                            $query = "
                                SELECT a.*, u.username 
                                FROM troskopis_articles a 
                                JOIN troskopis_users u 
                                ON a.author_id = u.user_id
                            ";
                        }

                        $result = mysqli_query($spojeni, $query);

                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $formatted_date = date("d.m.Y H:i:s", strtotime($row['date']));
                                echo '<div class="article-card">';
                                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                                echo '<p>Autor: ' . htmlspecialchars($row['username']) . '</p>';
                                echo '<p>Tématické číslo: ' . htmlspecialchars($row['category']) . '</p>';
                                echo '<p>Datum: ' . htmlspecialchars($formatted_date) . '</p>';
                                echo '<p>Verze: ' . htmlspecialchars($row['version']) . '</p>';
                                echo '<p>Status: ' . htmlspecialchars(getFormalStatus($row['status'])) . '</p>';
                                echo '<a href="' . htmlspecialchars($row['file']) . '" class="review-button" target="_blank">Zobrazit PDF</a>';

                                if ($role === 'author') {
                                    if ($row['status'] === 'reviewed') {
                                        echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                    } elseif ($row['status'] === 'editing') {
                                        echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                        echo '<a href="edit_article.php?article_id=' . htmlspecialchars($row['article_id']) . '&appealed=1" class="review-button">Upravit článek</a>';
                                    } elseif ($row['status'] === 'rejected') {
                                        echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                        echo '<a href="appeal.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Podat námitku</a>';
                                    } elseif ($row['status'] === 'approved') {
                                        echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                        echo '<a href="edit_article.php?article_id=' . htmlspecialchars($row['article_id']) . '&appealed=0" class="review-button">Upravit článek</a>';
                                    }
                                } elseif ($role === 'reviewer') {
                                    if ($row['status'] === 'pending_review') {
                                        echo '<a href="article_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Podat recenzi</a>';
                                    } elseif (in_array($row['status'], ['approved', 'rejected', 'editing', 'reviewed'])) {
                                        echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                    }
                                } elseif ($role === 'editor') {
                                    if ($row['status'] === 'pending_assignment') {
                                        echo '<a href="assign_reviewer.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Přiřadit recenzenta</a>';
                                    } elseif (in_array($row['status'], ['reviewed', 'editing', 'approved', 'rejected', 'appealed'])) {
                                        echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                        if ($row['status'] === 'reviewed') {
                                            echo '<a href="approve_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button" style="background-color: green;">Schválit</a>';
                                            echo '<a href="reject_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button" style="background-color: red;">Zamítnout</a>';
                                        } elseif ($row['status'] === 'rejected') {
                                            echo '<a href="approve_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button" style="background-color: green;">Změnit na schválený</a>';
                                        } elseif ($row['status'] === 'appealed') {
                                            echo '<a href="review_appeal.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Posoudit námitku</a>';
                                        }
                                    }
                                } elseif ($role === 'chiefeditor') {
                                    if (in_array($row['status'], ['reviewed', 'editing', 'approved', 'rejected'])) {
                                        echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                    }
                                }

                                echo '</div>';
                            }
                        } else {
                            echo '<p>Nebyly nalezeny žádné články.</p>';
                        }
                    ?>
            </div>
        </main>
        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>

    </body>
</html>