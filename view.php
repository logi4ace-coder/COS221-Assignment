<!DOCTYPE html>
<html lang="en">
<header>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/view.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@500&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <div class="header-content">
        <div class="logo">

            COMPARE<span>X</span>IT
        </div>

        <div class="search-container">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search.....">
                <div class="search-icon">
                    <svg focusable="false" height="24px" viewBox="0 0 24 24" width="24px" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="nav-links">
            <ul>
                <li><a href="index.html" class="active">Products</a></li>
                <li><a href="login.html" >Log-in</a></li>
                <li><a href="Signin.html" >Sign-in</a></li>
                <li><a href="wishlist.html" >Wishlist</a></li>
            </ul>
        </div>
        <div class="theme-select">
            <button id="theme-Dark">Dark</button>
            <button id="theme-Light">Light</button>
        </div>

    </div>
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
</html>
