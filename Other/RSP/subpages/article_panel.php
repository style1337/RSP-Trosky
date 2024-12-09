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
                            // Uživatel je přihlášen
                            echo "<li><a href=\"./logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
                        } 
                        
                        else {
                            // Uživatel není přihlášen
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
                                echo '<li><a href="./article_upload.php"><i class="fas fa-upload\"></i> Nahrát článek</a></li>';
                            }
                        ?>
                        <?php
                            if (isset($_SESSION['username'])) {
                                echo '<li><a href="./article_panel.php"><i class="fas fa-newspaper\"></i> Panel článků</a></li>';
                            }
                        ?>
                        <li><a href="../trosky.php"><i class="fas fa-home"></i> Hlavní strana</a></li>
                        <li><a href="./articles.php">Články</a></li>
                        <li><a href="./aboutus.php">O nás</a></li>
                        <li><a href="./contact.php">Kontakt</a></li>
                        <?php

                            if (isset($_SESSION['username'])) {
                                // Uživatel je přihlášen
                                echo "<li><a href=\"./logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
                            } 
                            
                            else {
                                // Uživatel není přihlášen
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

                // Načtení všech článků
                $role = $_SESSION['role'];
                $user_id = $_SESSION['user_id'];
                $query = "";

                if ($role === 'admin') {
                    // Admin vidí všechny články se všemi možnými akcemi
                    $query = "
                        SELECT a.*, u.username, r.username AS reviewer_username 
                        FROM troskopis_articles a 
                        JOIN troskopis_users u ON a.author_id = u.user_id
                        LEFT JOIN troskopis_users r ON a.assigned_reviewer = r.user_id
                    ";
                } elseif ($role === 'author') {
                    $query = "
                        SELECT a.*, u.username, r.username AS reviewer_username 
                        FROM troskopis_articles a 
                        JOIN troskopis_users u ON a.author_id = u.user_id
                        LEFT JOIN troskopis_users r ON a.assigned_reviewer = r.user_id
                        WHERE a.author_id = $user_id
                    ";
                } elseif ($role === 'reviewer') {
                    $query = "
                        SELECT a.*, u.username, r.username AS reviewer_username 
                        FROM troskopis_articles a 
                        JOIN troskopis_users u ON a.author_id = u.user_id
                        LEFT JOIN troskopis_users r ON a.assigned_reviewer = r.user_id
                        WHERE a.assigned_reviewer = $user_id
                    ";
                } else {
                    $query = "
                        SELECT a.*, u.username, r.username AS reviewer_username 
                        FROM troskopis_articles a 
                        JOIN troskopis_users u ON a.author_id = u.user_id
                        LEFT JOIN troskopis_users r ON a.assigned_reviewer = r.user_id
                    ";
                }

                $result = mysqli_query($spojeni, $query);
                
                // Definování pořadí statusů podle role
                $status_order = [];
                switch($role) {
                    case 'admin':
                        $status_order = [
                            'pending_assignment', // Potřebuje přiřazení recenzenta
                            'appealed',          // Potřebuje posouzení námitky
                            'pending_review',    // V procesu
                            'reviewed',          // Potřebuje schválení/zamítnutí
                            'editing',           // V procesu
                            'rejected',          // Dokončeno
                            'approved'           // Dokončeno
                        ];
                        break;
                    case 'editor':
                        $status_order = [
                            'pending_assignment', // Akce potřebná: přiřadit recenzenta
                            'appealed',          // Akce potřebná: posoudit námitku
                            'reviewed',          // Akce potřebná: schválit/zamítnout
                            'pending_review',    // Čekání
                            'editing',           // Čekání
                            'rejected',          // Dokončeno
                            'approved'           // Dokončeno
                        ];
                        break;
                    case 'reviewer':
                        $status_order = [
                            'pending_review',    // Akce potřebná: recenze
                            'reviewed',          // Dokončeno
                            'editing',           // Dokončeno
                            'rejected',          // Dokončeno
                            'approved'           // Dokončeno
                        ];
                        break;
                    case 'author':
                        $status_order = [
                            'editing',           // Akce potřebná: úprava článku
                            'rejected',          // Akce možná: námitka
                            'reviewed',          // Čekání na editora
                            'pending_review',    // Čekání na recenzenta
                            'pending_assignment',// Čekání na přiřazení
                            'appealed',          // Čekání na odpověď
                            'approved'           // Dokončeno
                        ];
                        break;
                    default:
                        $status_order = [
                            'pending_assignment',
                            'pending_review',
                            'appealed',
                            'reviewed',
                            'editing',
                            'approved',
                            'rejected'
                        ];
                }

                // Skupinování článků podle statusu
                $articles_by_status = array();
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        if (!isset($articles_by_status[$row['status']])) {
                            $articles_by_status[$row['status']] = array();
                        }
                        $articles_by_status[$row['status']][] = $row;
                    }
                }

                // Mapa názvů statusů
                $status_titles = array(
                    'pending_assignment' => 'Čeká na přiřazení',
                    'pending_review' => 'Čeká na recenzi',
                    'appealed' => 'Odvoláno',
                    'reviewed' => 'Recenzováno',
                    'editing' => 'Námitka schválena, čeká na úpravy',
                    'approved' => 'Schváleno',
                    'rejected' => 'Zamítnuto'
                );

                // Zobrazení článků v definovaném pořadí
                foreach ($status_order as $status) {
                    if (!empty($articles_by_status[$status])) {
                        $status_title = $status_titles[$status];
                        
                        // Přidání vizuálního indikátoru pro potřebnou akci
                        $action_needed = false;
                        switch($role) {
                            case 'editor':
                                $action_needed = in_array($status, ['pending_assignment', 'appealed', 'reviewed']);
                                break;
                            case 'reviewer':
                                $action_needed = $status === 'pending_review';
                                break;
                            case 'author':
                                $action_needed = $status === 'editing' || $status === 'rejected';
                                break;
                        }

                        echo '<h2 style="margin: 20px;' . ($action_needed ? ' color: #dc3545;' : '') . '">';
                        if ($action_needed) {
                            echo '<i class="fas fa-exclamation-circle"></i> ';
                        }
                        echo htmlspecialchars($status_title) . '</h2>';
                        
                        echo '<div class="article-grid">';
                        
                        foreach ($articles_by_status[$status] as $row) {
                            $formatted_date = date("d.m.Y H:i:s", strtotime($row['date']));
                            echo '<div class="article-card">';
                            // Quick action tlačítka
                            echo '<div class="quick-actions">';
                            echo '<a href="' . htmlspecialchars($row['file']) . '" class="review-button" target="_blank">Zobrazit</a>';
                            if ($row['version'] > 1) {
                                echo '<a href="view_history.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Historie</a>';
                            }
                            if (in_array($row['status'], ['reviewed', 'editing', 'approved', 'rejected'])) {
                                echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Recenze</a>';
                            }
                            echo '</div>';

                            // Obsah článku
                            echo '<div class="article-content">';
                            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                            echo '<p>Autor: ' . htmlspecialchars($row['username']) . '</p>';
                            echo '<p>Tématické číslo: ' . htmlspecialchars($row['category']) . '</p>';
                            echo '<p>Datum: ' . htmlspecialchars($formatted_date) . '</p>';
                            echo '<p>Verze: ' . htmlspecialchars($row['version']) . '</p>';
                            echo '<p>Status: ' . htmlspecialchars(getFormalStatus($row['status'])) . '</p>';
                            echo '<p>Recenzent: ' . ($row['reviewer_username'] ? htmlspecialchars($row['reviewer_username']) : '<span style="color: red;">Žádný</span>') . '</p>';
                            echo '</div>';

                            // Akční tlačítka
                            echo '<div class="button-container">';
                            if ($role === 'admin') {
                                if ($row['status'] === 'appealed') {
                                    echo '<a href="review_appeal.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit námitku</a>';
                                }
                                echo '<a href="article_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Podat recenzi</a>';
                                echo '<a href="assign_reviewer.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Přiřadit recenzenta</a>';
                                echo '<a href="approve_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button green">Schválit</a>';
                                echo '<a href="reject_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button red">Zamítnout</a>';
                                echo '<a href="edit_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button yellow">Upravit</a>';
                                
                            } elseif ($role === 'author') {
                                if ($row['status'] === 'reviewed') {
                                    echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                } elseif ($row['status'] === 'editing') {
                                    echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                    echo '<a href="edit_article.php?article_id=' . htmlspecialchars($row['article_id']) . '&appealed=1" class="review-button" style="background-color: #FFD700; color: #000000;">Upravit článek</a>';
                                } elseif ($row['status'] === 'rejected') {
                                    echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                    echo '<a href="appeal.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Podat námitku</a>';
                                } elseif ($row['status'] === 'approved') {
                                    echo '<a href="view_review.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button">Zobrazit recenzi</a>';
                                    echo '<a href="edit_article.php?article_id=' . htmlspecialchars($row['article_id']) . '&appealed=0" class="review-button" style="background-color: #FFD700; color: #000000;">Upravit článek</a>';
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
                                        echo '<a href="approve_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button green">Schválit</a>';
                                        echo '<a href="reject_article.php?article_id=' . htmlspecialchars($row['article_id']) . '" class="review-button red">Zamítnout</a>';
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

                            echo '</div>'; // Konec obalu tlačítek
                            echo '</div>';
                        }
                        echo '</div>'; // Konec article-gridu
                    }
                }
            ?>
        </main>
        <footer>
            <p>Tato aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
            polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
        </footer>

    </body>
</html>