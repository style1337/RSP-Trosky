
<?php
	session_start();
  require("subpages/connect.php");
?>

<!DOCTYPE html>
<html lang="cs">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troskopis</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="left-nav">
                <ul>
                    <li><a href="./subpages/contact.php">Kontakt</a></li>
                    <li><a href="./subpages/aboutus.php">O nás</a></li>
                    <li><a href="./subpages/articles.php">Články</a></li>
                    <li><a href="./trosky.php">Hlavní strana</a></li>
                </ul>
            </div>
            <div class="logo">
                <a href="./trosky.php">
                <img src="./images/logo.png" alt="Logo">
            </a>
            </div>
            <div class="right-nav">
                <ul> 
                        <?php

							if (isset($_SESSION['username'])) {
    					        // User is logged in
   			 			        //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                                echo "<li><a href=\"subpages/logout.php\">Logout</a></li>";
							} 
                            
                            else {
   						        // User is not logged in
    					        echo "<li><a href=\"subpages/login.php\">Login</a></li>";
							}
						?>
                </ul>
            </div>
            <div class="dropdown">
                <button class="dropbtn">&#9776;</button>
                <div class="dropdown-content">
                    <ul>
                        <?php

							if (isset($_SESSION['username'])) {
    					        // User is logged in
   			 			        //echo '<li><a>' . $_SESSION['username'] . '</a></li><br />';
                                echo "<li><a href=\"subpages/logout.php\">Logout</a></li>";
							} 
                            
                            else {
   						        // User is not logged in
    					        echo "<li><a href=\"subpages/login.php\">Login</a></li>";
							}
						?>
                        <li><a href="./trosky.php">Hlavní strana</a></li>
                        <li><a href="./subpages/articles.php">Články</a></li>
                        <li><a href="./subpages/aboutus.php">O nás</a></li>
                        <li><a href="./subpages/contact.php">Kontakt</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <section class="main-content">
        <div class="slideshow-container">
            <div class="mySlides">
                <article class="featured">
                    <h2>Vítejte na stránkách Troskopisu</h2>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Praesent in mauris eu tortor porttitor accumsan. Praesent id justo in neque elementum ultrices. Integer imperdiet lectus quis justo. Nulla pulvinar eleifend sem. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Duis condimentum augue id magna semper rutrum. Phasellus et lorem id felis nonummy placerat. Nullam eget nisl.</p>
                </article>
            </div>

            <div class="mySlides">
                <article class="featured">
                    <h2>Obsah</h2>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Praesent in mauris eu tortor porttitor accumsan. Praesent id justo in neque elementum ultrices. Integer imperdiet lectus quis justo. Nulla pulvinar eleifend sem. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Duis condimentum augue id magna semper rutrum. Phasellus et lorem id felis nonummy placerat. Nullam eget nisl.</p>
                </article>
            </div>
    
            <div class="mySlides">
                <article class="featured">
                    <h2>O nás</h2>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Praesent in mauris eu tortor porttitor accumsan. Praesent id justo in neque elementum ultrices. Integer imperdiet lectus quis justo. Nulla pulvinar eleifend sem. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Duis condimentum augue id magna semper rutrum. Phasellus et lorem id felis nonummy placerat. Nullam eget nisl.</p>
                </article>
            </div>
    
            <div class="mySlides">
                <article class="featured">
                    <h2>Kontakt</h2>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Praesent in mauris eu tortor porttitor accumsan. Praesent id justo in neque elementum ultrices. Integer imperdiet lectus quis justo. Nulla pulvinar eleifend sem. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Duis condimentum augue id magna semper rutrum. Phasellus et lorem id felis nonummy placerat. Nullam eget nisl.</p>
                </article>
            </div>


    
            
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
    </section>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);
        
        
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }
        
        
        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            if (n > slides.length) { slideIndex = 1 } 
            if (n < 1) { slideIndex = slides.length }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            slides[slideIndex - 1].style.display = "block";  
        }
        </script>
            

    <footer>
        <p>&copy; 2024 Troskopis. Všechna práva vyhrazena.</p>
        
    </footer>
</body>
</html>
