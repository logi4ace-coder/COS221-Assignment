<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/wishlist.css">
    
</head>
<body>
    <header>
        <div class="logo">
            COMPARE<span>X</span>IT
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Products</a></li>
                <li><a href="wishlist.html" class="active">Wishlist</a></li>
                <li><a href="Dashboard.html" >Dashboard</a></li>
                <li><a href="Signin.html">Sign-up</a></li>
                <li><a href="login.html">Log-in</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="wishlist-container">
        <div class="name">
            <div class="heart"></div>
            <h1>My Wishlist</h1>
        </div>
        
        <div class="empty-wishlist">
            <p>Your wishlist is currently empty.</p>
        </div>
    </main>

    <footer class="footer"> 
        <div class="footer-info">
            @2025 COMPARE<span>X</span>IT. All rights reserved.
        </div>
    
        <div class="theme-select">
            <button id="theme-Dark"><i class="fas fa-moon"></i> Dark</button>
            <button id="theme-Light"><i class="fas fa-sun"></i> Light</button>
        </div>
    </footer>
    
    <script src="script/theme.js"></script>
</body>
</html>
