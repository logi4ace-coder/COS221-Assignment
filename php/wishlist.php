<?php session_start()?>

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
        <?php include "header.php"; ?>

    
    <main class="wishlist-container">
        <div class="name">
            <div class="heart"></div>
            <h1>My Wishlist</h1>
        </div>

        <div id="wishlist-items" class="wishlist-items">

        </div>


        <div id="empty-wishlist" class="empty-wishlist-message" style="display: none;">
            <p>Your wishlist is currently empty.</p>
            <a href="index.php" class="browse-btn">Browse Products</a>
        </div>
    </main>
<script src="/Assignment/scripts/wishlist.js"></script>
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
