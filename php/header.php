<?php 
$current_page = basename($_SERVER['SCRIPT_NAME']); 
$dashboard_page = '';

if (isset($_SESSION['api_key']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'customer') {
        $dashboard_page = 'customerDashboard.php';
    } elseif ($_SESSION['user_role'] === 'admin') {
        $dashboard_page = 'managerDashboard.php';
    }
}
?>
<header>
<div class="site-branding">
  <img src="/Assignment/images/logo.jpg" alt="Logo" class="site-logo" />
<div class="site-title">
  <span class="compare-text">Compare</span>
  <span class="x-mark">X</span>
  <span class="it-text">IT</span>
</div>
</div>
  <?php if ($current_page === 'index.php') { ?>
        <div class="search-container">
            <div class="search-box">
                <input type="text"  id="search-input"   class="search-input" placeholder="Search...">
<div class="search-icon" id="search-btn" style="cursor: pointer;">
    <svg focusable="false" height="24px" viewBox="0 0 24 24" width="24px" xmlns="http://www.w3.org/2000/svg">
        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 
                 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 
                 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 
                 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 
                 14 7.01 14 9.5 11.99 14 9.5 14z"/>
    </svg>
</div>

            </div>
        </div>
    <?php } ?>

<nav>
  <ul>
    <li><a href="index.php" <?php echo ($current_page === 'index.php') ? 'class="active"' : ''; ?>>Products</a></li>

    <?php if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'): ?>
      <li><a href="wishlist.php" <?php echo ($current_page === 'wishlist.php') ? 'class="active"' : ''; ?>>Wishlist</a></li>
    <?php endif; ?>

    <?php if (!empty($dashboard_page)): ?>
      <li><a href="<?php echo $dashboard_page; ?>" <?php echo ($current_page === $dashboard_page) ? 'class="active"' : ''; ?>>Dashboard</a></li>
    <?php endif; ?>

    <?php if (!isset($_SESSION['api_key'])): ?>
      <li><a href="Signup.php" <?php echo ($current_page === 'Signup.php') ? 'class="active"' : ''; ?>>Sign-up</a></li>
      <li><a href="login.php" <?php echo ($current_page === 'login.php') ? 'class="active"' : ''; ?>>Log-in</a></li>
    <?php else: ?>
      <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
  </ul>
</nav>

</header>