<?php
session_start();
require("connect.php");

// Kontrola přístupu
if ($_SESSION['role'] != 'admin') {
    header("Location: unauthorized.php");
    exit();
}



// Get user_id from URL parameter
$user_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$user_id) {
    header("Location: apanel.php");
    exit();
}

// Funkce pro formátování role
function formatRole($role) {
    switch($role) {
        case 'author': return 'Autor';
        case 'editor': return 'Redaktor';
        case 'chiefeditor': return 'Šéfredaktor';
        case 'admin': return 'Administrátor';
        case 'reviewer': return 'Recenzent';
        default: return $role;
    }
}

// Fetch user details
$query = "SELECT * FROM troskopis_users WHERE user_id = ?";
$stmt = $spojeni->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "Uživatel nenalezen";
    header("Location: apanel.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Convert formal role back to system role
    $roleMap = [
        'Autor' => 'author',
        'Redaktor' => 'editor',
        'Šéfredaktor' => 'chiefeditor',
        'Administrátor' => 'admin',
        'Recenzent' => 'reviewer'
    ];

    $systemRole = $roleMap[$role];

    $updateQuery = "UPDATE troskopis_users SET username = ?, email = ?, role = ? WHERE user_id = ?";
    $updateStmt = $spojeni->prepare($updateQuery);
    $updateStmt->bind_param("sssi", $username, $email, $systemRole, $user_id);
    
    if ($updateStmt->execute()) {
        $_SESSION['success'] = "Údaje byly úspěšně aktualizovány";
        header("Location: apanel.php");
        exit();
    } else {
        $_SESSION['error'] = "Chyba při aktualizaci údajů";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Změna údajů</title>
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
    
    <main>

    <div class="edit-user-container">
        <h2>Upravit uživatele</h2>
        <form method="POST" class="edit-user-form">
            <div class="form-group">
                <label for="username">Uživatelské jméno:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <?php
                        $roles = [
                            'author' => 'Autor',
                            'editor' => 'Redaktor',
                            'chiefeditor' => 'Šéfredaktor',
                            'admin' => 'Administrátor',
                            'reviewer' => 'Recenzent'
                        ];
                        foreach ($roles as $value => $label) {
                            $selected = ($user['role'] === $value) ? 'selected' : '';
                            echo "<option value=\"$label\" $selected>$label</option>";
                        }
                    ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-button">Uložit změny</button>
                <a href="apanel.php" class="cancel-button">Zrušit</a>
            </div>
        </form>
    </div>
</main>

    <footer>
        <p>Tata aplikace je výsledkem školního projektu v kurzu Řízení SW projektů na Vysoké škole
        polytechnické Jihlava. Nejedná se o stránky skutečného odborného časopisu!</p>  
    </footer>
</body>
</html>
