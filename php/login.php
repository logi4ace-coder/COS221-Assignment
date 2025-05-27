<?php
session_start();
//var_dump($_SESSION);
$errors = $_SESSION['errors'] ?? [];
$formInput = $_SESSION['form-input'] ?? [];

unset($_SESSION['errors'], $_SESSION['form-input']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="icon" type="image/x-icon" href="images/Logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    :root {
      --primary-color: #42ff33;
      --primary-hover:#42ff33;
      --secondary-color: #a0a2a0;
      --light-bg: #fff5f7;
      --whitish: #e0e0e0;
      --text-light: #777;
      --transition-speed: 0.3s;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      margin: 0;
      font-family: 'Barlow', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: var(--light-bg);
      background-image: url("images/Backgroundimage.jpg");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      line-height: 1.6;
    }

    .outer {
      display: flex;
      width: 90%;
      max-width: 1100px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
      border-radius: 20px;
      overflow: hidden;
      background-color: white;
      margin: 2rem 0;
      transition: transform 0.3s ease;
    }

    .outer:hover {
      transform: translateY(-5px);
    }

    .mapper {
      display: flex;
      width: 100%;
      min-height: 650px;
    }

    .message {
      background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("images/Leftpanel.avif");
      background-size: cover;
      background-position: center;
      flex: 1;
      padding: 3rem;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
    }

    .message::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(68, 65, 65, 0.4);
      z-index: 0;
    }

    .message h1, .message p {
      position: relative;
      z-index: 1;
    }

    .message h1 {
      font-size: 2.8rem;
      margin-bottom: 1.5rem;
      line-height: 1.2;
      font-weight: 700;
    }

    .message p {
      font-size: 1.1rem;
      opacity: 0.9;
      max-width: 90%;
    }

    .form {
      flex: 1;
      padding: 4rem 3rem;
      background-color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form h1 {
      color: var(--secondary-color);
      font-size: 2.2rem;
      margin-bottom: 2.5rem;
      font-weight: 700;
      position: relative;
    }

    .form h1::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary-color);
      border-radius: 2px;
    }

    form {
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
    }

    .fomINput {
      position: relative;
      margin-bottom: 1.5rem;
    }

    form input[type="email"],
    form input[type="password"] {
      width: 100%;
      padding: 1rem 1rem 1rem 2.8rem;
      border: 2px solid var(--whitish);
      border-radius: 8px;
      font-size: 1rem;
      transition: all var(--transition-speed) ease;
      background-color: #f9f9f9;
    }

    form input[type="email"]:focus,
    form input[type="password"]:focus {
      border-color: var(--primary-color);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 51, 102, 0.1);
      background-color: white;
    }

    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
    }

    .forgot {
      text-align: right;
      margin-bottom: 2rem;
    }

    .forgot a {
      color: var(--secondary-color);
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 500;
      transition: color var(--transition-speed) ease;
    }

    .forgot a:hover {
      color: var(--primary-color);
    }

    .burr {
      background-color: var(--primary-color);
      color: white;
      padding: 1rem;
      width: 100%;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all var(--transition-speed) ease;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 1.5rem;
    }

    .burr:hover {
      background-color: var(--primary-hover);
      box-shadow: 0 5px 15px rgba(255, 51, 102, 0.3);
    }

    .NEW {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.95rem;
      color: var(--text-light);
    }

    .NEW a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
      margin-left: 5px;
      transition: all var(--transition-speed) ease;
    }

    .NEW a:hover {
      text-decoration: underline;
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 1.5rem 0;
      color: var(--text-light);
      font-size: 0.9rem;
    }

    .divider::before, .divider::after {
      content: "";
      flex: 1;
      border-bottom: 1px solid var(--whitish);
    }

    .divider::before {
      margin-right: 1rem;
    }

    .divider::after {
      margin-left: 1rem;
    }

    @media (max-width: 992px) {
      .mapper {
        min-height: auto;
      }

      .message, .form {
        padding: 2.5rem 2rem;
      }
    }

    @media (max-width: 768px) {
      .outer {
        width: 95%;
        margin: 1rem 0;
      }

      .mapper {
        flex-direction: column;
        min-height: auto;
      }

      .message {
        padding: 2rem 1.5rem;
        text-align: center;
      }

      .message h1 {
        font-size: 2.2rem;
      }

      .message p {
        max-width: 100%;
      }

      .form {
        padding: 2.5rem 1.5rem;
      }

      .form h1 {
        font-size: 1.8rem;
      }
    }

    @media (max-width: 480px) {
      .message, .form {
        padding: 1.5rem;
      }

      form input[type="email"],
      form input[type="password"] {
        padding: 0.8rem 0.8rem 0.8rem 2.5rem;
      }
    }

    .error-message {
      color: red;
      font-size: 0.85rem;
      margin-top: -0.5rem;
      margin-bottom: 1rem;
      display: block;
    }

    input:required:invalid {
      border-left: 3px solid red;
    }

    .form-error {
      animation: shake 0.5s;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-5px); }
      40%, 80% { transform: translateX(5px); }
    }
  </style>
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
      <?php if (!empty($errors['validation']) && is_array($errors['validation'])): ?>
  <div class="error-message form-error">
    <?php 
      foreach ($errors['validation'] as $error) {
        echo htmlspecialchars($error, ENT_QUOTES) . "<br>";
      }
    ?>
  </div>
<?php endif; ?>

<form action="/Assignment/api.php" method="POST">
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
        <p>New Here? <a href="Signup.php" target="_blank">Create an Account</a></p>
      </div>
    </div>
  </div>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
  localStorage.setItem('api_key', '<?php echo addslashes($_SESSION['api_key']); ?>');
  localStorage.setItem('user_role', '<?php echo addslashes($_SESSION['user_role']); ?>');
  localStorage.setItem('user_email', '<?php echo addslashes($_SESSION['user_email']); ?>');
</script>
</body>
</html>
