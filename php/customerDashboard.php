<?php session_start()?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customer Dashboard</title>
      <link rel="stylesheet" href="../css/dash.css">

 
</head>
<body>

<?php include "header.php"?>
<div class="container">
  <h1>Customer Dashboard</h1>

  <nav>
    <button class="active" data-tab="alerts">Price Alerts</button>
    <button data-tab="recommendations">Recommendations</button>
    <button data-tab="account">Account</button>
  </nav>

  <!-- Price Alerts Tab -->
  <section id="alerts" class="tab-content">
    <h2>Your Price Alerts</h2>
    
    <div class="product-grid">
      <div class="product-card">
        <img src="https://via.placeholder.com/280x160?text=Wireless+Earbuds" class="product-image" alt="Wireless Earbuds">
        <div class="product-name">Wireless Earbuds Pro</div>
        <div class="product-price">$89.99</div>
        <div class="product-meta">Previous: $99.99</div>
        <div class="product-meta price-down">↓ $10.00 (10%)</div>
        <div class="product-meta">Store: Amazon</div>
        <div class="alert-message">Alert! Price dropped below $95</div>
        <button>Manage Alert</button>
      </div>
      
      
      
      
  </section>

  <!-- Recommendations Tab -->
 <section id="recommendations" class="tab-content" style="display:none;">
  <h2>Recommended For You</h2>
  <div class="loading-message" style="text-align: center; padding: 2rem;">Loading recommendations...</div>
  <div class="product-grid"></div>
</section>

  <!-- Account Tab -->
  <section id="account" class="tab-content" style="display:none;">
    <h2>Account Settings</h2>
    
    <div class="account-form">
      <h3>Change Password</h3>
      <div>
        <label for="current-password">Current Password</label>
        <input type="password" id="current-password">
      </div>
      <div>
        <label for="new-password">New Password</label>
        <input type="password" id="new-password">
      </div>
      <div>
        <label for="confirm-password">Confirm New Password</label>
        <input type="password" id="confirm-password">
      </div>
      <button>Update Password</button>
      
      <h3 style="margin-top: 2rem;">Account Management</h3>
      <button class="secondary" style="background: #ff3333; color: white;">Delete Account</button>
    </div>
  </section>
</div>

<script src="/Assignment/scripts/cdash.js"></script>

</body>
</html>