<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css" >
    <title>Shopping</title>
</head>
<body>

    <header>
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
                <button id="theme-Dack">Dack</button>
                <button id="theme-Light">Light</button>
            </div>
            
        </div>
    </header>

    <div class="container">
        <aside class="side-bar">
            <div class="filter-section">
                <h3>Price</h3>
                <div class="slider-container">
                    <input type="range" min="0" max="1000" value="500">
                </div>
                <div class="price-inputs">
                    <input type="text" class="price-input" placeholder="Min">
                    <input type="text" class="price-input" placeholder="Max">
                </div>
            </div>

            <div class="filter-section">
                <h3>Size</h3>
                <div class="filter-option">
                    <input type="checkbox" id="size-s">
                    <label for="size-s">Small</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="size-m">
                    <label for="size-m">Medium</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="size-l">
                    <label for="size-l">Large</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="size-xl">
                    <label for="size-xl">Extra Large</label>
                </div>
            </div>

            <div class="filter-section">
                <h3>Stock</h3>
                <div class="filter-option">
                    <input type="checkbox" id="in-stock">
                    <label for="in-stock">In stock</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="out-of-stock">
                    <label for="out-of-stock">Out of stock</label>
                </div>
            </div>

            <div class="filter-section">
                <h3>Currency</h3>
                <div class="filter-option">
                    <input type="radio" name="currency" checked>
                    <label for="usd">R (ZAR)</label>
                </div>
                <div class="filter-option">
                    <input type="radio" name="currency" >
                    <label for="eur">USA ($)</label>
                </div>
            </div>

            <div class="filter-section">
                <h3>Shop</h3>
                <div class="filter-option">
                    <input type="checkbox" class="store">
                    <label for="shop-amazon">Mike</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" class="store">
                    <label for="shop-amazon">Mike</label>
                </div>
            </div>

            <div class="filter-section">
                <h3>Materials</h3>
                <div class="filter-option">
                    <input type="checkbox" id="material-cotton">
                    <label for="material-cotton">Cotton</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="material-polyester">
                    <label for="material-polyester">Polyester</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="material-leather">
                    <label for="material-leather">Leather</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="material-metal">
                    <label for="material-metal">Metal</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="material-glass">
                    <label for="material-glass">Glass</label>
                </div>
            </div>
        </aside>


    <main class="container-products">

        <div class="pro-show">

            <div class="product-card">
                <img src="" alt="product" class="prod-img">
                <div class="pro-info">
                <p class="pro-name">product name</p>
                <div class="rating">★★★★★</div>
                    <div class="pro-price">$232</div>
                    <div class="pro-store">Mike</div>
                </div>
            </div>
            
            <div class="product-card">
                <img src="" alt="product" class="prod-img">
                <div class="pro-info">
                <p>product name</p>
                <div class="rating">★★★★★</div>
                    <div class="pro-price">$232</div>
                    <div class="pro-store">Mike</div>
                </div>
            </div>

        </div>
    </main>
    </div>

 <script src="script/theme.js" defer></script>
</body>
</html>
