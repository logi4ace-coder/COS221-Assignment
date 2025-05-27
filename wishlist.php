<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    

    <link rel="stylesheet" href="../css/wishlist.css">
 
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">

</head>
<body>
<script src="wishlist.js"></script>
    <header>
        <div class="logo">
            COMPARE<span>X</span>IT
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Products</a></li>
                <li><a href="login.html">Log-in</a></li>
                <li><a href="Signin.html">Sign-in</a></li>
                <li><a href="wishlist.html" class="active">Wishlist</a></li>
            </ul>
        </nav>
        <div class="theme-select">
            <button id="theme-Dack">Dark</button>
            <button id="theme-Light">Light</button>
        </div>
    </header>
    
    <main class="wishlist-container">
        <div class="name">
            <div class="heart"></div>
            <h1>My Wishlist</h1>
        </div>

        <div id="wishlist-items" class="wishlist-items">

        </div>


        <div id="empty-wishlist" class="empty-wishlist-message" style="display: none;">
            <p>Your wishlist is currently empty.</p>
            <a href="index.html" class="browse-btn">Browse Products</a>
        </div>
    </main>

</body>
</html>
