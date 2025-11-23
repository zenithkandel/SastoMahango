<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SastoMahango";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper character handling
$conn->set_charset("utf8mb4");

// Set Timezone to Nepal Standard Time
date_default_timezone_set('Asia/Kathmandu');
$conn->query("SET time_zone = '+05:45'");
?>
