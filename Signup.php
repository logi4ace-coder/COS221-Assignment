<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css" >
    <link rel="stylesheet" href="css/signin.css" >
    <title>Sign In</title>
</head>
<body>

    <header class="header">
            <div class="logo">
            
                COMPARE<span>X</span>IT
            </div>
            
           <div class="nav-links">
                <ul>
                    <li><a href="index.html" >Products</a></li>
                    <li><a href="login.html" >Log-in</a></li>
                    <li><a href="Signin.html" class="active" >Sign-in</a></li>
                    <li><a href="wishlist.html" >Wishlist</a></li>
                </ul>
            </div>
    </header>

    <main class="container-form">

        <div class="the-form">
            <form class="sign-form">
                <h3>Sign In</h3>

                <label for="name-input">Name: </label>
                <input id="name-input" type="text" name="name" value="name" >

                <label for="surname-input">Surname: </label>
                <input id="surname-input" type="text" name="surname" value="surname" >

                <label for="email-input">Email: </label>
                <input id="email-input" type="email" name="email" value="email" required>

                <label for="pass-input">Password: </label>
                <input id="pass-input" type="password" name="password"  value="password" required>

                <label>UserType: </label>
                <select id="user-type">
                    <option value="Customer">Customer</option>
                    <option value="Manager">Manager</option>
                    <option value="" disabled selected>I am logging to ....</option>
                    <option value="Register">Register</option>
                </select>

                <button id="submit-btn" type="submit">Submit</button>

            </form>
        </div>
            
    </main>

    <footer class="footer"> 
        <div class="footer-info">
            @2025 COMPARE<span>X</span>IT. All rights reserved.
        </div>
    
        <div class="theme-select">
            <button id="theme-Dark"><i class="fas fa-moon"></i> Dark</button>
            <button id="theme-Light"><i class="fas fa-sun"></i> Light</button>
        </div>
    </footer>

     <script src="script/theme.js" defer></script>
</body>
</html>
