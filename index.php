<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8f9fa;
            padding: 50px;
            max-width: 100vw;
            max-height: 100vh;
            margin: 0;
            
        }
        header{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 125px;
            background-color: rgba(0, 0, 0, 0.5);
            color:white;
            display: flex;
            flex-direction: row;
            align-items: center;
            z-index: 1000;
        }
        nav{
            position:fixed;
            left: 125px;
            width: 100%;
            display: flex;
            align-items: center;
        }
        body,section{
    
            width: 100vw;
            height: 100vh;
            background: url('img/background/bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .logo-container {
            background: #FFFFFF;
            width: 60px;
            height: 60px;
            border: 2px solidrgb(13, 13, 14);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
           
        }
        .container {
            position: absolute;
            top: 50%;
            left: 37%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color:white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        h1 {
            color: #343a40;
            padding: 0px;
            font-size: 24px;
            font-weight: bold;
            align-self: center;
            text-align: center;
        }
        p {
            color:rgb(255, 255, 255);
            font-size: 28px;
        }
        h2 {
            color:rgb(255, 255, 255);
            margin-bottom: 20px;
        }
        .btn {
            display: block;
            width: 94%;
            padding: 12px;
            margin: 10px 0;
            text-decoration: none;
            background: #007BFF;
            color: white;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 20px;
            }
            .btn {
                font-size: 16px;
                padding: 10px;
            }
        }
    </style>
    
</head>
<body>
    <header>
        <nav>
            <div class="logo-container">
                <h1>XYZ</h1>
            </div>
            <p>Hotel Reservations</p>
        </nav>
    </header>
    <section id="content-section">
         <div class="container">
        <h2>Welcome to XYZ Hotel Reservations</h2>
        <a href="users/login.php" class="btn">Login</a>
        <a href="users/register.php" class="btn">Register</a>
    </div>
    </section>

   
</body>
</html>
