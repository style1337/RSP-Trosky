<?php
session_start();
require("connect.php");

// Check for editor or admin role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'editor' && $_SESSION['role'] !== 'admin')) {
    header("Location: unauthorized.php");
    exit();
}

// Get article_id and category from GET parameters
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category = isset($_GET['category']) ? intval($_GET['category']) : 1;

if ($article_id > 0) {
    // Start transaction
    mysqli_begin_transaction($spojeni);
    
    try {
        // First, set all articles in the category to featured = 0
        $query1 = "UPDATE troskopis_articles SET featured = 0 WHERE category = ?";
        $stmt1 = mysqli_prepare($spojeni, $query1);
        mysqli_stmt_bind_param($stmt1, "i", $category);
        mysqli_stmt_execute($stmt1);

        // Then, set the selected article to featured = 1
        $query2 = "UPDATE troskopis_articles SET featured = 1 WHERE article_id = ? AND category = ?";
        $stmt2 = mysqli_prepare($spojeni, $query2);
        mysqli_stmt_bind_param($stmt2, "ii", $article_id, $category);
        mysqli_stmt_execute($stmt2);

        // Commit transaction
        mysqli_commit($spojeni);
        $_SESSION['success'] = "Článek byl úspěšně nastaven jako hlavní.";
    } catch (Exception $e) {
        // Rollback in case of error
        mysqli_rollback($spojeni);
        $_SESSION['error'] = "Nastala chyba při nastavování hlavního článku.";
    }
}

// Redirect back to articles page with the same category
header("Location: articles.php?category=" . $category);
exit();
?>