<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT id, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role == "user") {
            header("Location: ../guest/guest_dashboard.php");
        } elseif ($role == "receptionist") {
            header("Location: ../receptionist/receptionist_dashboard.php");
        } elseif ($role == "superadmin") {
            header("Location: ../superadmin/superadmin_dashboard.php");
        }
        exit();
    } else {
        echo "<script>alert('Invalid username or password');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8f9fa;
            padding: 50px;
            max-width: 100vw;
            max-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        header{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 125px;
            background-color: rgba(3, 1, 1, 0.5);
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
        body,section{
            width: 100vw;
            height: 100vh;
            background: url('/img/background/bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            position: absolute;
            top: 37%;
            left: 37%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color:white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
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
            color: white;
            margin-bottom: 20px;
        }
        .form-group {
            width: 95%;
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .register-link {
            margin-top: 15px;
            display: block;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }
        .register-link:hover {
            text-decoration: underline;
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
         <div class="login-container">
            <h2>Login</h2>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" placeholder="Enter Username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="Enter Password" required>
                </div>

                <button type="submit">Login</button>
            </form>

            <a href="register.php" class="register-link">Don't have an account? Register here</a>
        </div>
    </section>
   

</body>
</html>
