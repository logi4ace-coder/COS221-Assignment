<!--/*Zelamene Shazi 24742083*/-->

<!DOCTYPE html> 
<html lang="en">
<head>
    <title>under_construction</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="COS216/PA1/images/download(1).jpeg"> 
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        html {
            background: linear-gradient(to right, salmon, rgb(186, 62, 93));
        }

        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .design {
    background-color: rgb(139, 131, 131);
    background-image: 
        radial-gradient(circle, gray, rgb(217, 143, 143) 30%, gray 40%),
        radial-gradient(circle, gray, rgb(217, 143, 143) 30%, gray 40%),
        radial-gradient(circle, gray, rgb(217, 143, 143) 30%, gray 40%),
        radial-gradient(circle, gray, rgb(217, 143, 143) 30%, gray 40%),
        radial-gradient(circle, gray, rgb(217, 143, 143) 30%, gray 40%);
    
    background-position: 
        10% 20%, 
        50% 60%, 
        80% 30%, 
        20% 80%, 
        70% 50%;
    
    background-size: 150px 150px; 
    background-repeat: no-repeat;

    height: 600px;
    width: 1000px;
    padding: 50px;
    margin: auto;
    border-radius: 10px;
}


        .construction-text {
            display: flex;
            justify-content: center;
            font-weight: bold;
        }

        .Ctext {
    font-weight: bolder;
    font-size: 70px;
    color: wheat;
    font-family: 'Barlow', sans-serif;
    opacity: 0;
    animation: fadeIn 2s ease-in-out forwards;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}


        .readyText {
    color: wheat;
    font-weight: 500;
    font-family: 'Barlow', sans-serif;
    text-align: center;
    margin-top: 10px;
    animation: pulse 1.5s infinite alternate;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.05);
        opacity: 0.8;
    }
}


        .loading {
            width: 500px;
            display: flex;
            align-items: center;
            margin: auto;
        }

        .leftLoad {
            height: 18px;
            width: 80px;
            background-color: salmon;
            animation: fillBar 3s ease-in-out forwards;
        }
        @keyframes fillBar {
    0% {
        width: 0;
    }
    100% {
        width: 80px;
    }
}

        .rightLoad {
            height: 18px;
            background-color: white;
            width: 400px;
        }

        .o, .l00 {
            display: inline-block;
            color: wheat;
        }

        .l00 {
            padding-left: 399px;
        }

        .wavy-line {
            width: 80%;
            height: 10px;
            margin: 20px auto;
            background: repeating-linear-gradient(90deg, salmon, wheat 10px, transparent 10px, transparent 20px);
        }

        .notifyQestion p {
            color: wheat;
            font-family: 'Roboto', sans-serif;
            font-weight: bolder;
            font-size: 35px;
        }

        .email {
    display: flex;
    justify-content: center;
}

.in {
    width: 220px;
    height: 40px;
    border: 2px solid salmon;
    border-right: none; 
    border-radius: 5px 0 0 5px; 
    padding-left: 10px;
    font-size: 16px;
}

.notify-me {
    color: white;
    background-color: salmon;
    border: 2px solid salmon;
    border-left: none; 
    border-radius: 0 5px 5px 0; 
    font-weight: bold;
    width: 130px;
    height: 46px;
    font-size: 16px;
    cursor: pointer;
    border-left: darkgray;
    transition: background-color 0.1s, color 0.1s, opacity ease-in 0.1s, opacity ease-out 0.1s;
}

.notify-me:hover {
    color: salmon;
    background-color: white;
}

.notify-me:active {
    opacity: 0.8;
}


        footer {
            color: wheat;
            margin-top: 20px;
            margin-bottom: 20px;
        }

       

        .last-section-icons-round {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }

        .last-section-icons-round i {
            font-size: 25px;
            color: white;
            background-color: salmon;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .last-section-icons-round i:hover {
            background-color: wheat;
            color: black;
            transform: scale(1.1);
        }

    </style>
</head>
<body>
    <div class="main">
        <div class="design">
            <div class="construction-text">
                <p class="Ctext">UNDER CONSTRUCTION!</p> 
            </div>

            <p class="readyText">SITE NEARLY READY</p>    

            <div class="loading">
                <div class="leftLoad"></div>
                <div class="rightLoad"></div>
            </div>

            <p class="o">0%</p>
            <p class="l00">100%</p>

            <div class="wavy-line"></div>
            <div class="wavy-line"></div>

            <div class="notifyy">
                <div class="notifyQestion">
                    <p>Get Notified When We Launch!</p>
                </div>
                <div class="email">
                    <input type="email" placeholder="Enter your email" class="in">
                    <button class="notify-me">Notify me</button>
                </div>
            </div>

            <div class="last-section-icons-round">
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-linkedin"></i>
            </div>

            <footer>
                <p>&copy; 2025 LiveFútbol. All rights reserved.</p>
            </footer>
        </div>
    </div>
</body>
</html>
