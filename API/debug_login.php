<?php
header('Content-Type: text/plain');
include 'conn.php';

// Usage: http://localhost/projects/SastoMahango/API/debug_login.php?email=name@example.com&password=password123

$email = $_GET['email'] ?? '';
$password = $_GET['password'] ?? '';

if (!$email || !$password) {
    die("Please provide email and password parameters.\nExample: ?email=name@example.com&password=password123");
}

echo "Debugging Login for Email: " . $email . "\n";
echo "Input Password: " . $password . "\n";
echo "--------------------------------------------------\n";

$sql = "SELECT id, full_name, password FROM contributors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ User NOT FOUND in database.\n";
    echo "Please check the email address in your 'contributors' table.";
} else {
    $user = $result->fetch_assoc();
    $stored_hash = $user['password'];
    
    echo "✅ User FOUND.\n";
    echo "User ID: " . $user['id'] . "\n";
    echo "Stored Password (Hash): " . $stored_hash . "\n";
    echo "Hash Length: " . strlen($stored_hash) . "\n";
    
    echo "--------------------------------------------------\n";
    echo "Verifying Password...\n";
    
    if (password_verify($password, $stored_hash)) {
        echo "✅ SUCCESS: Password matches the hash.\n";
    } else {
        echo "❌ FAILED: Password does NOT match the hash.\n";
        
        // Diagnosis
        if ($password === $stored_hash) {
            echo "\n⚠️ DIAGNOSIS: The stored password is PLAIN TEXT.\n";
            echo "You stored the password directly without hashing it.\n";
            echo "You must update the database with this hash instead:\n";
            echo password_hash($password, PASSWORD_BCRYPT);
        } elseif (strlen($stored_hash) < 60) {
            echo "\n⚠️ DIAGNOSIS: The stored hash looks too short.\n";
            echo "It might be truncated or using an old format (like MD5).\n";
        } elseif (substr($stored_hash, 0, 4) !== '$2y$') {
             echo "\n⚠️ DIAGNOSIS: The stored hash does not start with '$2y$'.\n";
             echo "It might not be a valid Bcrypt hash.\n";
        }
    }
}
?>
