<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'conn.php';

session_start();

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password'];

    // Check in contributors table
    $sql = "SELECT id, full_name, password FROM contributors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            // Note: In a real application, passwords should be hashed. 
            // Assuming they are hashed with password_hash()
            if (password_verify($password, $user['password'])) {
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = 'contributor';

                // Update last login
                $updateSql = "UPDATE contributors SET last_login = NOW() WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("i", $user['id']);
                $updateStmt->execute();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful',
                    'redirect' => 'dashboard.html'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing email or password']);
}

$conn->close();
?>
