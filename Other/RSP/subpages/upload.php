<?php
session_start();

// Zahrnutí souboru connect.php pro připojení k databázi
require_once 'connect.php';

// Umožnění přístupu pouze pro roli "author"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'author') {
    $_SESSION['error'] = "Musíte být přihlášeni jako autor, abyste mohli nahrávat soubory.";
    header("Location: unauthorized.php");
    exit();
}

/*
// Ověření, zda je uživatel přihlášen
if (!isset($_SESSION['user_id'])) {
    echo "Musíte být přihlášeni, abyste mohli nahrávat soubory.";
    exit;
}
*/

// Zpracování formuláře
// Kontrola, zda byl soubor odeslán a zda jsou vyplněna pole
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $sql = "INSERT INTO troskopis_articles (author_id, name, file, category, date, status) VALUES ('$user_id', '$fileTitle', '$uploadPath', '$artNumber', NOW(), 'pending_assignment');";

                if (mysqli_query($spojeni, $sql)) {
                    $_SESSION['success'] = "Soubor byl úspěšně nahrán a uložen.";
                    header("Location: articles.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Chyba při ukládání do databáze: " . mysqli_error($spojeni);
                }
            } else {
                $_SESSION['error'] = "Došlo k chybě při ukládání souboru na server.";
            }
        } else {
            $_SESSION['error'] = "Pouze PDF soubory jsou povoleny.";
        }
    } else {
        $_SESSION['error'] = "Nebyl odeslán soubor, název nebo hodnocení.";
}
} else {
    $_SESSION['error'] = "Neplatná metoda odeslání.";
}

// Přesměrování zpět na formulář pro nahrání souboru, pokud došlo k chybě
header("Location: article_upload.php");
exit();

// Zavření připojení k databázi
mysqli_close($spojeni);
?>
