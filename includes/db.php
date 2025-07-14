<?php
$host = "localhost";
$user = "root";  // Change if needed
$pass = "";      // Change if you set a password
$dbname = "hotel_reservations";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
