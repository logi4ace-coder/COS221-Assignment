<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css" >
    <link rel="stylesheet" href="css/signin.css" >
    <title>Sign In</title>
</head>
<body>

    <header>
        <div class="header-content">
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
            <div class="theme-select">
                <button id="theme-Dack">Dack</button>
                <button id="theme-Light">Light</button>
            </div>
            
        </div>
    </header>

    <main class="container-form">

        <div class="the-form">
            <form class="sign-form">
                <h3>Sign In</h3>

                <label for="name-input">Name: </label>
                <input id="name-input" type="text" >

                <label for="surname-input">Surname: </label>
                <input id="surname-input" type="text" >

                <label for="email-input">Email: </label>
                <input id="email-input" type="email"  required>

                <label for="pass-input">Password: </label>
                <input id="pass-input" type="password"  required>

                <label>UserType: </label>
                <select id="user-type">
                    <option>Customer</option>
                    <option>Stuff</option>
                </select>

                <button id="submit-btn" type="submit">Submit</button>

            </form>
        </div>
            
    </main>

     <script src="script/theme.js" defer></script>
</body>
</html>
