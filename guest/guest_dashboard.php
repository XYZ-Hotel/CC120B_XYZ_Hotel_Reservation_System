<?php
session_start();
include '../includes/db.php';

// Ensure only logged-in users can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../users/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username']; // Assuming session stores username

// Fetch user's reservations
$sql = "SELECT r.room_number, r.type, r.price, res.status 
        FROM reservations res
        JOIN rooms r ON res.room_id = r.id
        WHERE res.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Dashboard</title>
    <style>
        body {
            
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            text-align: center;
            margin: 40px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        section {
            display: flex;
            flex-direction: row;
            max-height: auto;
            width: 100%;
            height: 100%;
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        
       
        h2 {
            color: #343a40;
        }
        .dashboard-container {
            width: 100%;
            height: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .left-section {
            width: 20%;
            height: 800px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 8px;
            margin-right: 20px;
        }
        table {
            width: 100%;
            height:auto;
            margin-top: 10px;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 12px 18px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <section>
        <div class="left-section">
            <h2>👤 Hello, <?= htmlspecialchars($username); ?>!</h2>
            <p>Welcome to your dashboard. Here you can view and manage your reservations.</p>
            <br>
            <a href="../users/logout.php" class="btn btn-danger">Logout</a>
        </div>
        <div class="dashboard-container">
            <h2>🏨 Welcome to XYZ Hotel Reservations!</h2>
    
            <h3>📌 Available Rooms</h3>
            <a href="available_rooms.php" class="btn btn-primary">View and Reserve a Room</a>
    
            <h3>📌 My Reservations</h3>
            <table>
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reservations->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['room_number']; ?></td>
                            <td><?= $row['type']; ?></td>
                            <td>₱<?= number_format($row['price'], 2); ?></td>
                            <td style="color: <?= $row['status'] == 'confirmed' ? 'green' : 'red'; ?>; font-weight: bold;">
                                <?= ucfirst($row['status']); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            
        </div>

    </section> 
</body>
</html>
