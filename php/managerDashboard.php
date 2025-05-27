<?php session_start()?>

<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manager Dashboard</title>
      <link rel="stylesheet" href="../css/Dashboard.css">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include "header.php"; ?>
<main>
  <div>

    <div>
      <h1>Welcome back, Manager!</h1>
      <div>
        Company: <span id="businessName"></span>
      </div>

    </div>

    <!-- Navigation -->
    <div>
      <button onclick="showSection('productManagement')">Products</button>
      <button onclick="showSection('priceAlerts')">Price Alerts</button>
      <button onclick="showSection('analytics')">Analytics</button>
      <button onclick="showSection('profileInfo')">Profile Info</button>
    </div>
   <div id="loader" class="loader-container">
              <img src="/Assignment/images/loading.gif" alt="Loading..." class="loader-img" />
</div>
    <!-- Product Management -->
    <div id="productManagement">

      <h2>All Products</h2>
     
      <div id="productList">

    </div>

      <!-- Add Product Form -->
      <h3>Add New Product</h3>
      <form id="productForm">
  <label>
    Name:
    <input type="text" name="Name" required>
  </label><br>

  <label>
    Brand:
    <input type="text" name="Brand" required>
  </label><br>

  <label>
    Material:
    <input type="text" name="Material">
  </label><br>

  <label>
    Description:
    <textarea name="Description" required></textarea>
  </label><br>

  <label>
    Color:
    <input type="text" name="Color">
  </label><br>

  <label>
    Price:
    <input type="number" name="Price" step="0.01" required>
  </label><br>

  <label>
  Stock:
  <select name="Stock" required>
    <option value="in_stock">In Stock</option>
    <option value="out_of_stock">Out of Stock</option>
  </select>
</label>
<br>

  <label>
    Image URL:
    <input type="text" name="ImageURL">
  </label><br>

  <label>
    Tags:
    <input type="text" name="Tags">
  </label><br>

  <button type="submit">Add Product</button>
</form>

    </div>

    <!-- Price Alerts -->
    <div id="priceAlerts" style="display:none">
      <h2>Price Alerts</h2>
      <div id="alertsList">

      </div>
      <div>
        <button id="notify">Notify Customers</button>
      </div>
    </div>

    <!-- Analytics -->
      <h2>Business Analytics</h2>
      <div id="analytics" style="display:none;"></div>

<div>
      <h1>Welcome back</h1>
      <div>
        <span id="businessName"></span>
      </div>

    </div>
    <!-- Profile Info -->
    <div id="profileInfo" style="display:none">
      <h2>Profile Information</h2>
      <p><strong>Email:</strong> <span id="managerEmail"></span></p>
      <p><strong>User Role:</strong> <span id="managerRole">Admin</span></p>
      

      <h3>Update Business Information</h3>
      <form id="businessForm">
        <div>
          <label for="newBusinessName">New Company Name: </label>
          <input type="text" id="newBusinessName"  required />
        </div>
        <button type="submit">Update Business Name</button>
      </form>
<div id="messageBox" style="display:none; margin-top:10px;"></div>

      

      <h3>Change Password</h3>
      <form id="passwordForm">
        
        <div>
          <label for="newPassword">New Password:</label>
          <input type="password" id="newPassword" required />
        </div>
        <div>
          <label for="confirmPassword">Confirm Password:</label>
          <input type="password" id="confirmPassword" required />
        </div>
        <button type="submit">Change Password:</button>
            <div id="status" class="message"></div>

      </form>
      
<button id="delete-account-btn">Delete Account</button>
    </div>
  </div>
</main>


<script>
function showSection(id) {
  document.querySelectorAll('[id="productManagement"], [id="priceAlerts"], [id="analytics"], [id="profileInfo"]').forEach(sec => {
    sec.style.display = 'none';
  });
  document.getElementById(id).style.display = 'block';
}


</script>
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
<script src="/Assignment/scripts/mdash.js"></script>

</body>
</html>