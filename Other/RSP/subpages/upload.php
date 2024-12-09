<?php
session_start();

// Zahrnutí souboru connect.php pro připojení k databázi
require_once 'connect.php';

// Umožnění přístupu pouze pro roli "author" nebo "admin"
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'author' && $_SESSION['role'] !== 'admin')) {
    $_SESSION['error'] = "Musíte být přihlášeni jako autor nebo administrátor, abyste mohli nahrávat soubory.";
    header("Location: unauthorized.php");
    exit();
}

// Helper functions
function convertPHPSizeToBytes($sSize) {
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix, array('P','T','G','M','K'))) {
        return (int)$sSize;
    }
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        case 'P': $iValue *= 1024;
        case 'T': $iValue *= 1024;
        case 'G': $iValue *= 1024;
        case 'M': $iValue *= 1024;
        case 'K': $iValue *= 1024;
    }
    return (int)$iValue;
}

function formatBytes($bytes) {
    if ($bytes > 1024*1024) {
        return round($bytes / (1024*1024), 2) . " MB";
    } elseif ($bytes > 1024) {
        return round($bytes / 1024, 2) . " KB";
    }
    return $bytes . " B";
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

        // Check file size (assuming upload_max_filesize is the limiting factor)
        $maxFileSize = min(
            convertPHPSizeToBytes(ini_get('upload_max_filesize')),
            convertPHPSizeToBytes(ini_get('post_max_size'))
        );
        
        if ($fileSize > $maxFileSize) {
            $_SESSION['error'] = "Soubor je příliš velký. Maximální povolená velikost je " . 
                                formatBytes($maxFileSize) . ".";
            header("Location: article_upload.php");
            exit();
        }

        // Improved PDF validation
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileTmpPath);
        finfo_close($finfo);

        $uploadPath = '../articles/' . uniqid() . '_' . basename($_FILES['pdfFile']['name']);

        // Check both mime type and file extension
        $isPDF = ($mimeType === 'application/pdf' || $mimeType === 'application/x-pdf') 
                 && strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'pdf';

        if ($isPDF) {
            // Přesun souboru do určeného adresáře
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                // Uložení dat do databáze
                $user_id = $_SESSION['user_id']; // ID přihlášeného uživatele
                $sql = "INSERT INTO troskopis_articles (author_id, name, file, category, date, status) VALUES ('$user_id', '$fileTitle', '$uploadPath', '$artNumber', NOW(), 'pending_assignment');";

                if (mysqli_query($spojeni, $sql)) {
                    $_SESSION['success'] = "Soubor byl úspěšně nahrán a uložen.";
                    header("Location: article_panel.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Chyba při ukládání do databáze: " . mysqli_error($spojeni);
                }
            } else {
                $_SESSION['error'] = "Došlo k chybě při ukládání souboru na server.";
            }
        } else {
            $_SESSION['error'] = "Soubor musí být ve formátu PDF. Detekován formát: " . $mimeType;
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



?>
