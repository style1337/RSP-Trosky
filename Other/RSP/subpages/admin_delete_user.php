<?php
session_start();
require("connect.php");

// Zkontrolovat, zda je uživatel přihlášen a má roli "admin"
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: unauthorized.php");
    exit();
}

// Zkontrolovat, zda bylo zadáno ID uživatele
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Není zadáno ID uživatele.";
    header("Location: apanel.php");
    exit();
}

$user_id = mysqli_real_escape_string($spojeni, $_GET['id']);

// Zamezit smazání vlastního účtu
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = "Nemůžete smazat svůj vlastní účet.";
    header("Location: apanel.php");
    exit();
}

mysqli_begin_transaction($spojeni);

try {
    // Smazat všechny recenze na články uživatele
    $query1 = "DELETE FROM troskopis_reviews WHERE article_id IN (SELECT article_id FROM troskopis_articles WHERE author_id = '$user_id')";
    mysqli_query($spojeni, $query1);

    // Smazat všechny historie článků uživatele
    $query1 = "DELETE FROM troskopis_articlehistory WHERE article_id IN (SELECT article_id FROM troskopis_articles WHERE author_id = '$user_id')";
    mysqli_query($spojeni, $query1);

    // Smazat všechny články uživatele
    $query2 = "DELETE FROM troskopis_articles WHERE author_id = '$user_id'";
    mysqli_query($spojeni, $query2);

    // Smazat uživatele
    $query3 = "DELETE FROM troskopis_users WHERE user_id = '$user_id'";
    mysqli_query($spojeni, $query3);


    mysqli_commit($spojeni);
    $_SESSION['success'] = "Uživatel a všechny jeho články byli úspěšně smazány.";
} catch (Exception $e) {
    // Rollback v případě chyby
    mysqli_rollback($spojeni);
    $_SESSION['error'] = "Chyba při mazání: " . $e->getMessage();
}

header("Location: apanel.php");
exit();
?>