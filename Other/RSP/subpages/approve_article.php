<?php
session_start();
require("connect.php");

// Povolit přístup pouze pro roli "editor"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'editor') {
    header("Location: unauthorized.php");
    exit();
}

// Načtení ID článku z GET parametrů
$article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;

if ($article_id > 0) {
    // Aktualizace stavu článku na "approved"
    $update_status_query = "
        UPDATE troskopis_articles 
        SET status = 'approved' 
        WHERE article_id = $article_id
    ";

    if (mysqli_query($spojeni, $update_status_query)) {
        $_SESSION['success'] = "Článek byl úspěšně schválen.";
    } else {
        $_SESSION['error'] = "Chyba při schvalování článku: " . mysqli_error($spojeni);
    }
} else {
    $_SESSION['error'] = "Neplatné ID článku.";
}

// Přesměrování zpět na panel článků
header("Location: article_panel.php");
exit();
?>