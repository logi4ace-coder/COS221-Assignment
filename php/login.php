<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
$formInput = $_SESSION['form-input'] ?? [];

unset($_SESSION['errors'], $_SESSION['form-input']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="icon" type="image/x-icon" href="images/Logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="login.css" />
</head>
<body>
<div class="outer">
  <div class="mapper">
    <div class="message">
      <h1>Welcome back!</h1>
      <p>You can sign in to access with your existing account.</p>
    </div>
    <div class="form">
      <h1>Login</h1>
      <form action="#" method="post">
        <div class="fomINput">
          <i class="fas fa-envelope input-icon"></i>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Email" 
            required
            value="<?php echo htmlspecialchars($formInput['email'] ?? '', ENT_QUOTES); ?>"
          >
          <span class="error-message">
            <?php 
              if (in_array('email', $errors['missing'] ?? [])) {
                echo "Email is required.";
              } elseif (isset($errors['validation']['email'])) {
                echo htmlspecialchars($errors['validation']['email'], ENT_QUOTES);
              }
            ?>
          </span>
        </div>

        <div class="fomINput">
          <i class="fas fa-lock input-icon"></i>
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Password" 
            required
          >
          <span class="error-message">
            <?php 
              if (in_array('password', $errors['missing'] ?? [])) {
                echo "Password is required.";
              } elseif (isset($errors['validation']['password'])) {
                echo htmlspecialchars($errors['validation']['password'], ENT_QUOTES);
              }
            ?>
          </span>
        </div>

        <div class="forgot">
          <a href="forgot.php">Forgot Password?</a>
        </div>
        <input type="hidden" name="type" value="Login">
        <div class="g-recaptcha" data-sitekey="6LeTSjYrAAAAAPWLz1dlJ3D1nriKFJF7oOm7irit"></div>
        <button type="submit" class="burr">Log In</button>
      </form>

      <div class="divider">OR</div>

      <div class="NEW">
        <p>New Here? <a href="../../PA3/php/signup.php" target="_blank">Create an Account</a></p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
