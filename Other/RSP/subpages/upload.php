<?php
session_start();

// Zahrnutí souboru connect.php pro připojení k databázi
require_once 'connect.php';

// Ověření, zda je uživatel přihlášen
if (!isset($_SESSION['user_id'])) {
    echo "Musíte být přihlášeni, abyste mohli nahrávat soubory.";
    exit;
}

// Zpracování formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kontrola, zda byl soubor odeslán a zda jsou vyplněna pole
    if (isset($_FILES['pdfFile'], $_POST['article_name'], $_POST['article_number'])) {
        $fileTmpPath = $_FILES['pdfFile']['tmp_name'];
        $fileName = $_FILES['pdfFile']['name'];
        $fileSize = $_FILES['pdfFile']['size'];
        $fileType = $_FILES['pdfFile']['type'];
        $fileTitle = mysqli_real_escape_string($spojeni, $_POST['article_name']); // Ošetření vstupu
        $artNumber = (int)$_POST['article_number']; // Zpracování hodnocení jako celé číslo

        // Urč, kam se soubor uloží
        $uploadPath = '../articles/' . uniqid() . '_' . basename($_FILES['pdfFile']['name']);

        // Zkontroluj, zda je soubor ve formátu PDF
        if ($fileType === 'application/pdf') {
            // Přesun souboru do určeného adresáře
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                // Uložení dat do databáze
                $user_id = $_SESSION['user_id']; // ID přihlášeného uživatele
                $sql = "INSERT INTO troskopis_articles (author_id, name, file, category, date, status) VALUES ('$user_id', '$fileName', '$uploadPath', '$artNumber', NOW(), 'pending_review');";

                if (mysqli_query($spojeni, $sql)) {
                    echo "Soubor byl úspěšně nahrán a uložen.";
                    header("Location: articles.php");
                } else {
                    echo "Chyba při ukládání do databáze: " . mysqli_error($spojeni);
                }
            } else {
                echo "Došlo k chybě při ukládání souboru na server.";
            }
        } else {
            echo "Pouze PDF soubory jsou povoleny.";
        }
    } else {
        echo "Nebyl odeslán soubor, název nebo hodnocení.";
    }
} else {
    echo "Neplatná metoda odeslání.";
}

// Zavření připojení k databázi
mysqli_close($spojeni);
?>
