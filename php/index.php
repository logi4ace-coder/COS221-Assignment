<?php session_start()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CompareXIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow&display=swap" rel="stylesheet">

    <style>

    </style>
</head>
<body>
    <?php include "header.php"; ?>

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
                    <input type="checkbox" id="size-S" />
<label for="size-S">S</label>
                </div>
                <div class="filter-option">
                  <input type="checkbox" id="size-M" />
<label for="size-M">M</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="size-L" />
<label for="size-L">L</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" id="size-XL" />
<label for="size-XL">XL</label>
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
                    <input type="radio" name="currency" id="zar" checked>
                    <label for="zar">R (ZAR)</label>
                </div>
                <div class="filter-option">
                    <input type="radio" name="currency" id="usd">
                    <label for="usd">USA ($)</label>
                </div>
            </div>

            <div class="filter-section">
                <h3>Shop</h3>
                <div class="filter-option">
                    <input type="checkbox" id="shop-mike1">
                    <label for="shop-mike1"></label>
                </div>
                <div class="filter-option">
<input type="checkbox" id="shop-Amazon" value="Amazon" />
                    <label for="shop-mike2"></label>
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
        <div id="loader" class="loader-container">
              <img src="/Assignment/images/loading.gif" alt="Loading..." class="loader-img" />
</div>
        <div id="loading-gif" style="display: none;">
                <img src="/Assignment/images/Searching.gif" alt="Loading..." >
            </div>
            <div id="no-results-gif" style="display: none;">
                <img src="/Assignment/images/Notfound.gif" alt="No Results Found">
            </div>
            <div id="no-results-div" style="display: none;">
  <p>Sorry, we couldn't find any products matching your search.
                    <button onclick="goBackHome()" class="go-home-button">Go Back Home</button>


</p>
</div>


        <main class="container-products">
            <div class="pro-show">

            


        </div>
        </main>
    </div>



    <?php include "footer.php"; ?>

<script src="/Assignment/scripts/index.js"></script>

<script>
  <?php if (isset($_SESSION['api_key'])): ?>
    localStorage.setItem('api_key', '<?php echo addslashes($_SESSION['api_key']); ?>');
  <?php endif; ?>

  <?php if (isset($_SESSION['user_role'])): ?>
    localStorage.setItem('user_role', '<?php echo addslashes($_SESSION['user_role']); ?>');
  <?php endif; ?>

  <?php if (isset($_SESSION['user_email'])): ?>
    localStorage.setItem('user_email', '<?php echo addslashes($_SESSION['user_email']); ?>');
  <?php endif; ?>
</script>



</body>
</html>

