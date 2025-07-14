<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin') {
    header("Location: ../users/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Superadmin Dashboard</title>
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
        .dashboard-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .btn {
            display: block;
            width: 94%;
            padding: 12px;
            margin: 10px 0;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome to Admin Dashboard</h2>
    <a href="manage_rooms.php" class="btn">🏨 Manage Rooms</a>
    <a href="generate_report.php" class="btn">📊 Generate Report</a>
    <a href="../users/logout.php" class="btn logout-btn">🚪 Logout</a>
</div>

</body>
</html>
