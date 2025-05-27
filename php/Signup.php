
<?php session_start(); 

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
$show_modal = $_SESSION['show_modal'] ?? false;

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['show_modal']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="/Assignment/css/signin.css">
  <link href="https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="">

</head>
<body>


  <div class="signup-form-container">
    <h2>Compare<span>X</span>It Signup</h2>
    <p style="text-align: center; margin-top: 24px; font-size: 15px; color: var(--text-color); line-height: 1.6;">
  Join <strong style="color: var(--accent-color);">Compare<span style="color: var(--accent-color);">X</span>It</strong> and make smarter, faster choices — every time.
</p>

<?php if (!$show_modal): ?>
<div id="form-message" style="color: red; font-size: 14px; margin-bottom: 1em;">
  <?php 
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
    }
  ?>
</div>
<?php endif; ?>


    <form  method="POST" action="/Assignment/api.php" id="fom">
      <label for="name">Name:</label>
<input type="text" id="name" name="name" class="gorm" placeholder="Name" required
    value="<?= htmlspecialchars($old['name'] ?? '') ?>">
      <label for="surname">Surname:</label>

      <input type="text" id="surname" name="surname" class="gorm" placeholder="Surname" required
    value="<?= htmlspecialchars($old['surname'] ?? '') ?>">
      <label for="email">Email:</label>

      <input type="email" id="email" name="email" class="gorm" placeholder="Email" required
    value="<?= htmlspecialchars($old['email'] ?? '') ?>">

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" class="gorm butt" placeholder="Password" required>

      <label for="user_type">Account Type:</label>
     <select name="user_type" id="user_type" class="gorm" required>
    <option value="" disabled <?= !isset($old['user_type']) ? 'selected' : '' ?>>I am logging in as a/an ....</option>
    <option value="Customer" <?= ($old['user_type'] ?? '') === 'Customer' ? 'selected' : '' ?>>Customer</option>
    <option value="Manager" <?= ($old['user_type'] ?? '') === 'Manager' ? 'selected' : '' ?>>Manager</option>
</select>
      <label for="type">Action:</label>

<select name="type" id="type" class="gorm" required>
    <option value="" disabled <?= !isset($old['type']) ? 'selected' : '' ?>>I am logging to ....</option>
    <option value="Register" <?= ($old['type'] ?? '') === 'Register' ? 'selected' : '' ?>>Register</option>
</select>


      <button type="submit" name="button" class="form-button">Sign Up</button>
    </form>
    <img id="loadingSpinner" src="/Assignment/images/loading.gif" alt="Loading..." style="display: none; margin: 10px auto; height: 40px;" />

  </div>
  <div id="businessModal" style="display:none;">
  <form id="businessForm" action="/Assignment/api.php" method="POST">
  <h2>Register your business!</h2>
  <?php if ($show_modal && !empty($errors)): ?>
<div id="businessErrors" style="color: red; font-size: 14px; margin-bottom: 1em;">
  <?php foreach ($errors as $error): ?>
    <p><?= htmlspecialchars($error) ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

    <input type="hidden" name="type" value="RegisterBusiness" />

  <label>Business Name*</label>
  <input type="text" name="Name" required />

  <label>Country*</label>
  <input type="text" name="Country" required />

  <label>Type*</label>
  <select name="Type" required>
    <option value="" disabled selected>Select type</option>
    <option value="online">Online</option>
    <option value="physical">Physical</option>
    <option value="hybrid">Hybrid</option>
  </select>

  <label>Website URL</label>
  <input type="url" name="WebsiteURL" />

  <label>Store Address</label>
  <input type="text" name="StoreAddress" />

  <button type="submit">Save Business</button>
</form>

  <img id="businessSpinner" src="/Assignment/images/loading.gif" alt="Loading..." style="display: none; margin: 10px auto; height: 30px;" />

</div>


  <div id="errorMessage" class="error" style="display:none;">Missing field! All fields must be filled in.</div>
 <?php if ($show_modal) : ?>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      document.getElementById("businessModal").style.display = "block";
    });
  </script>
  <?php endif; ?>

  
  <script src="/Assignment/scripts/signin.js"></script>

</body>
</html>

<?php include("footer.php"); ?>

