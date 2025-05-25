<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/wishlist.css">
    <link rel="stylesheet" href="css/view.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product View - COMPAREXIT</title>
</head>
<body>

<header>
    <div class="logo">
        COMPARE<span>X</span>IT
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Products</a></li>
            <li><a href="wishlist.php" >Wishlist</a></li>
            <li><a href="Dashboard.php" >Dashboard</a></li>
            <li><a href="Signin.php">Sign-up</a></li>
            <li><a href="login.php">Log-in</a></li>
        </ul>
    </nav>
</header>

<main class="view-container">
    <div class="product-main">
        <div class="product-gallery">
            <img id="main-image" src="https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg" alt="Product Image" class="main-image">
            <div class="thumbnail-container">
                <img src="https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg" alt="Thumbnail 1" class="thumbnail" onclick="changeMainImage(this.src)">
                <img src="https://fakestoreapi.com/img/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg" alt="Thumbnail 2" class="thumbnail" onclick="changeMainImage(this.src)">
                <img src="https://fakestoreapi.com/img/71li-ujtlUL._AC_UX679_.jpg" alt="Thumbnail 3" class="thumbnail" onclick="changeMainImage(this.src)">
            </div>
        </div>

        <div class="product-info">
            <h1 class="product-title" id="product-title">Fjallraven - Foldsack No. 1 Backpack, Fits 15 Laptops</h1>
            <div class="product-price">$109.95 <span class="best-price-badge">Best Price</span></div>

            <div class="product-description">
                <p>Your perfect pack for everyday use and walks in the forest. Stash your laptop (up to 15 inches) in the padded sleeve, your everyday backpack.</p>
                <p><strong>Features:</strong></p>
                <ul>
                    <li>Room enough for 15" laptop</li>
                    <li>Padded sleeve for laptop</li>
                    <li>Adjustable shoulder straps</li>
                    <li>Water-resistant material</li>
                    <li>Multiple compartments</li>
                </ul>
            </div>

            <button class="add-to-wishlist">Add to Wishlist</button>
        </div>
    </div>

    <div class="retailers-section">
        <h2 class="retailers-title">Available From These Retailers</h2>
        <div class="retailer-list" id="retailer-list">
        </div>
    </div>

    
    <script src="js/product-view.js"></script>
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

<script src="script/theme.js" defer></script>
</body>
</html>
