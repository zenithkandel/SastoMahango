<?php
// This script generates a Bcrypt hash for a given password.
// Usage: http://localhost/projects/SastoMahango/API/generate_hash.php?password=your_password

header('Content-Type: text/plain');

if (isset($_GET['password'])) {
    $password = $_GET['password'];
    // PASSWORD_BCRYPT forces the $2y$ format
    // The default cost is 10
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    echo "Password: " . $password . "\n";
    echo "Hash: " . $hash . "\n";
    echo "\nCopy the Hash value above and paste it into the 'password' column of your 'contributors' table.";
} else {
    echo "Please provide a password parameter. Example:\n";
    echo "http://localhost/projects/SastoMahango/API/generate_hash.php?password=password123";
}
?>
