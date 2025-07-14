<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $middle_initial = trim($_POST['middle_initial']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $cell_number = trim($_POST['cell_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; // Default role

    // Password confirmation check
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Validate Cell Number (Must start with 09 & be 11 digits)
    if (!preg_match('/^09\d{9}$/', $cell_number)) {
        echo "<script>alert('Invalid phone number! Must start with 09 and be 11 digits.'); window.history.back();</script>";
        exit();
    }

    // Validate Email (Only Gmail, Yahoo, Outlook)
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|outlook\.com)$/', $email)) {
        echo "<script>alert('Invalid email! Only Gmail, Yahoo, and Outlook are allowed.'); window.history.back();</script>";
        exit();
    }

    // Concatenate Name Fields into full_name
    $full_name = $first_name . ' ' . strtoupper($middle_initial) . '. ' . $last_name;

    $sql = "INSERT INTO users (username, full_name, email, cell_number, password, role) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $full_name, $email, $cell_number, $password_hashed, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please log in.'); window.location='login.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
            background: url('/img/background/bg.jpg') no-repeat center center fixed;
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
        .register-container {
            position: absolute;
            top: 17%;
            left: 37%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color:white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
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
            text-align: center;
            color: white;
        }
        .form-group {
            margin-bottom: 15px;
            width: 95%;
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
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
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
<div class="register-container">
    <h2>Register</h2>
    <form method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Enter Username" required>
        </div>

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" placeholder="Enter First Name" required>
        </div>

        <div class="form-group">
            <label for="middle_initial">Middle Initial</label>
            <input type="text" name="middle_initial" placeholder="M" maxlength="1" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" placeholder="Enter Last Name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Enter Email" required>
        </div>

        <div class="form-group">
            <label for="cell_number">Cell Number</label>
            <input type="text" name="cell_number" placeholder="09XXXXXXXXX" pattern="09[0-9]{9}" maxlength="11" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>

        <button type="submit">Register</button>

        <div class="login-link">
            Already have an account? <a href="users/login.php">Login here</a>
        </div>
    </form>
</div>
</section>

</body>
</html>
